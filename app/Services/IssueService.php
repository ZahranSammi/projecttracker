<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class IssueService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    public function queryForWorkspace(
        Workspace $workspace,
        array $filters = [],
        ?string $search = null,
        ?string $projectId = null,
        string $sortBy = 'updatedAt',
        string $sortDirection = 'DESC',
    ): Builder {
        $query = Issue::query()
            ->with(['project', 'status', 'assignee', 'reporter', 'labels'])
            ->withCount('comments')
            ->where('workspace_id', $workspace->getKey())
            ->when($projectId, fn (Builder $builder, string $id) => $builder->where('project_id', $id));

        $this->applyFilters($query, $filters);
        $this->applySearch($query, $search);

        $direction = strtoupper($sortDirection) === 'ASC' ? 'asc' : 'desc';
        $sortColumn = match ($sortBy) {
            'createdAt' => 'created_at',
            'dueDate' => 'due_date',
            'priority' => 'priority',
            default => 'updated_at',
        };

        return $query->orderBy($sortColumn, $direction);
    }

    /**
     * @return Collection<int, Issue>
     */
    public function listForWorkspace(
        Workspace $workspace,
        array $filters = [],
        ?string $search = null,
        ?string $projectId = null,
    ): Collection {
        return $this->queryForWorkspace($workspace, $filters, $search, $projectId)->get();
    }

    /**
     * @return Collection<int, Issue>
     */
    public function listAssignedToUser(User $user, Workspace $workspace, array $filters = []): Collection
    {
        return $this->queryForWorkspace($workspace, $filters)
            ->where('assignee_id', $user->getKey())
            ->get();
    }

    public function create(User $actor, Project $project, array $input): Issue
    {
        $issue = DB::transaction(function () use ($actor, $project, $input): Issue {
            $workspace = $project->workspace;
            $status = isset($input['status_id'])
                ? IssueStatus::query()->where('workspace_id', $workspace->getKey())->findOrFail($input['status_id'])
                : $workspace->statuses()->where('is_default', true)->firstOrFail();

            $number = (int) Issue::query()
                ->where('project_id', $project->getKey())
                ->withTrashed()
                ->max('number') + 1;

            $issue = Issue::query()->create([
                'workspace_id' => $workspace->getKey(),
                'project_id' => $project->getKey(),
                'number' => $number,
                'title' => $input['title'],
                'description' => $input['description'] ?? null,
                'status_id' => $status->getKey(),
                'priority' => strtoupper($input['priority'] ?? 'MEDIUM'),
                'type' => strtoupper($input['type'] ?? 'TASK'),
                'assignee_id' => $input['assignee_id'] ?? null,
                'reporter_id' => $input['reporter_id'] ?? $actor->getKey(),
                'due_date' => $input['due_date'] ?? null,
            ]);

            $issue->labels()->sync($input['label_ids'] ?? []);

            $this->activityLogService->record($workspace, $actor, 'issue.created', $issue, [
                'title' => $issue->title,
                'priority' => $issue->priority,
                'status' => $status->name,
            ]);

            return $issue;
        });

        return $issue->fresh(['project', 'status', 'assignee', 'reporter', 'labels']);
    }

    public function update(User $actor, Issue $issue, array $input): Issue
    {
        DB::transaction(function () use ($actor, $issue, $input): void {
            $before = $issue->only(['title', 'priority', 'status_id', 'assignee_id', 'due_date']);

            $issue->fill([
                'title' => $input['title'] ?? $issue->title,
                'description' => $input['description'] ?? $issue->description,
                'status_id' => $input['status_id'] ?? $issue->status_id,
                'priority' => isset($input['priority']) ? strtoupper($input['priority']) : $issue->priority,
                'type' => isset($input['type']) ? strtoupper($input['type']) : $issue->type,
                'assignee_id' => array_key_exists('assignee_id', $input) ? $input['assignee_id'] : $issue->assignee_id,
                'due_date' => array_key_exists('due_date', $input) ? $input['due_date'] : $issue->due_date,
            ]);

            $issue->save();

            if (array_key_exists('label_ids', $input)) {
                $issue->labels()->sync($input['label_ids'] ?? []);
            }

            $this->activityLogService->record($issue->workspace, $actor, 'issue.updated', $issue, [
                'before' => $before,
                'after' => $issue->only(['title', 'priority', 'status_id', 'assignee_id', 'due_date']),
            ]);
        });

        return $issue->fresh(['project', 'status', 'assignee', 'reporter', 'labels']);
    }

    public function delete(User $actor, Issue $issue): bool
    {
        return DB::transaction(function () use ($actor, $issue): bool {
            $this->activityLogService->record($issue->workspace, $actor, 'issue.deleted', $issue, [
                'title' => $issue->title,
            ]);

            return (bool) $issue->delete();
        });
    }

    public function assign(User $actor, Issue $issue, ?User $assignee): Issue
    {
        return $this->update($actor, $issue, [
            'assignee_id' => $assignee?->getKey(),
        ]);
    }

    public function moveStatus(User $actor, Issue $issue, IssueStatus $status): Issue
    {
        return $this->update($actor, $issue, [
            'status_id' => $status->getKey(),
        ]);
    }

    public function addComment(User $actor, Issue $issue, string $body): Comment
    {
        $comment = DB::transaction(function () use ($actor, $issue, $body): Comment {
            $comment = $issue->comments()->create([
                'workspace_id' => $issue->workspace_id,
                'author_id' => $actor->getKey(),
                'body' => $body,
            ]);

            $this->activityLogService->record($issue->workspace, $actor, 'comment.created', $comment, [
                'issue' => $issue->identifier,
            ]);

            return $comment;
        });

        return $comment->fresh(['author', 'issue']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function boardColumns(Project $project, array $filters = []): array
    {
        $statuses = $project->workspace->statuses()->get();
        $issues = $this->queryForWorkspace($project->workspace, $filters, projectId: $project->getKey())->get()->groupBy('status_id');

        return $statuses->map(function (IssueStatus $status) use ($issues): array {
            $cards = $issues->get($status->getKey(), collect());

            return [
                'status' => $status,
                'issues' => $cards,
            ];
        })->all();
    }

    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['statusIds'])) {
            $query->whereIn('status_id', $filters['statusIds']);
        }

        if (! empty($filters['priorities'])) {
            $query->whereIn('priority', array_map('strtoupper', $filters['priorities']));
        }

        if (! empty($filters['assigneeIds'])) {
            $query->whereIn('assignee_id', $filters['assigneeIds']);
        }

        if (! empty($filters['reporterIds'])) {
            $query->whereIn('reporter_id', $filters['reporterIds']);
        }

        if (! empty($filters['labelIds'])) {
            $query->whereHas('labels', fn (Builder $builder) => $builder->whereIn('labels.id', $filters['labelIds']));
        }

        if (! empty($filters['dueDateFrom'])) {
            $query->whereDate('due_date', '>=', $filters['dueDateFrom']);
        }

        if (! empty($filters['dueDateTo'])) {
            $query->whereDate('due_date', '<=', $filters['dueDateTo']);
        }

        if (empty($filters['includeCompleted'])) {
            $query->whereHas('status', fn (Builder $builder) => $builder->where('type', '!=', 'DONE'));
        }
    }

    private function applySearch(Builder $query, ?string $search): void
    {
        if (! filled($search)) {
            return;
        }

        $query->where(function (Builder $builder) use ($search): void {
            $builder->where('title', 'like', '%'.$search.'%')
                ->orWhere('description', 'like', '%'.$search.'%')
                ->orWhereHas('labels', fn (Builder $labelQuery) => $labelQuery->where('name', 'like', '%'.$search.'%'));
        });
    }
}
