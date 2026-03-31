<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * @return Collection<int, Project>
     */
    public function listForWorkspace(Workspace $workspace, ?string $search = null): Collection
    {
        return Project::query()
            ->with(['lead', 'issues.status', 'issues.assignee'])
            ->withCount('issues')
            ->where('workspace_id', $workspace->getKey())
            ->when($search, fn ($query, $term) => $query
                ->where(fn ($nested) => $nested
                    ->where('name', 'like', '%'.$term.'%')
                    ->orWhere('description', 'like', '%'.$term.'%')))
            ->orderByDesc('updated_at')
            ->get();
    }

    public function create(User $actor, Workspace $workspace, array $input): Project
    {
        $project = DB::transaction(function () use ($actor, $workspace, $input): Project {
            $project = Project::query()->create([
                'workspace_id' => $workspace->getKey(),
                'key' => strtoupper($input['key']),
                'name' => $input['name'],
                'description' => $input['description'] ?? null,
                'lead_user_id' => $input['lead_user_id'] ?? $actor->getKey(),
                'icon_path' => $input['icon_path'] ?? 'images/glacier/illustrations/workspace-logo.png',
                'network_path' => $input['network_path'] ?? 'images/glacier/illustrations/network-map.png',
                'created_by' => $actor->getKey(),
            ]);

            $this->activityLogService->record($workspace, $actor, 'project.created', $project, [
                'name' => $project->name,
                'key' => $project->key,
            ]);

            return $project;
        });

        return $project->load(['lead', 'issues.status', 'issues.assignee']);
    }

    public function update(User $actor, Project $project, array $input): Project
    {
        DB::transaction(function () use ($actor, $project, $input): void {
            $project->fill([
                'name' => $input['name'] ?? $project->name,
                'description' => $input['description'] ?? $project->description,
                'is_archived' => $input['is_archived'] ?? $project->is_archived,
                'lead_user_id' => $input['lead_user_id'] ?? $project->lead_user_id,
            ]);

            $project->save();

            $this->activityLogService->record($project->workspace, $actor, 'project.updated', $project, [
                'name' => $project->name,
                'is_archived' => $project->is_archived,
            ]);
        });

        return $project->fresh(['lead', 'issues.status', 'issues.assignee']);
    }

    public function progress(Project $project): int
    {
        $total = max($project->issues->count(), 1);
        $done = $project->issues->filter(fn ($issue) => $issue->status?->type === 'DONE')->count();

        return (int) round(($done / $total) * 100);
    }

    public function health(Project $project): string
    {
        $overdue = $project->issues->filter(fn ($issue) => $issue->due_date?->isPast() && $issue->status?->type !== 'DONE')->count();
        $progress = $this->progress($project);

        if ($overdue > 0) {
            return 'Needs Review';
        }

        if ($progress >= 80) {
            return 'Shipping';
        }

        if ($progress >= 55) {
            return 'On Track';
        }

        return 'Focused';
    }
}
