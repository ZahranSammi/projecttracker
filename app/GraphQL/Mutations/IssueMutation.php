<?php

namespace App\GraphQL\Mutations;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use App\Models\User;
use App\Services\IssueService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class IssueMutation
{
    public function __construct(
        private readonly IssueService $issueService,
    ) {
    }

    public function createIssue($_, array $args): Issue
    {
        /** @var User $user */
        $user = Auth::user();
        $project = Project::query()->with('workspace')->findOrFail($args['input']['projectId']);
        Gate::authorize('create', [Issue::class, $project]);

        Validator::make($args['input'], [
            'projectId' => ['required'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ])->validate();

        return $this->issueService->create($user, $project, [
            'title' => $args['input']['title'],
            'description' => $args['input']['description'] ?? null,
            'status_id' => $args['input']['statusId'] ?? null,
            'priority' => $args['input']['priority'] ?? 'MEDIUM',
            'assignee_id' => $args['input']['assigneeId'] ?? null,
            'reporter_id' => $args['input']['reporterId'] ?? $user->getKey(),
            'label_ids' => $args['input']['labelIds'] ?? [],
            'due_date' => $args['input']['dueDate'] ?? null,
        ]);
    }

    public function updateIssue($_, array $args): Issue
    {
        /** @var User $user */
        $user = Auth::user();
        $issue = Issue::query()->with('workspace')->findOrFail($args['id']);
        Gate::authorize('update', $issue);

        return $this->issueService->update($user, $issue, [
            'title' => $args['input']['title'] ?? null,
            'description' => $args['input']['description'] ?? null,
            'status_id' => $args['input']['statusId'] ?? null,
            'priority' => $args['input']['priority'] ?? null,
            'assignee_id' => $args['input']['assigneeId'] ?? null,
            'label_ids' => $args['input']['labelIds'] ?? null,
            'due_date' => $args['input']['dueDate'] ?? null,
        ]);
    }

    public function deleteIssue($_, array $args): bool
    {
        /** @var User $user */
        $user = Auth::user();
        $issue = Issue::query()->findOrFail($args['id']);
        Gate::authorize('delete', $issue);

        return $this->issueService->delete($user, $issue);
    }

    public function assignIssue($_, array $args): Issue
    {
        /** @var User $user */
        $user = Auth::user();
        $issue = Issue::query()->findOrFail($args['issueId']);
        Gate::authorize('assign', $issue);

        $assignee = isset($args['assigneeId']) ? User::query()->findOrFail($args['assigneeId']) : null;

        return $this->issueService->assign($user, $issue, $assignee);
    }

    public function moveIssueStatus($_, array $args): Issue
    {
        /** @var User $user */
        $user = Auth::user();
        $issue = Issue::query()->with('workspace')->findOrFail($args['issueId']);
        Gate::authorize('moveStatus', $issue);

        $status = IssueStatus::query()
            ->where('workspace_id', $issue->workspace_id)
            ->findOrFail($args['statusId']);

        return $this->issueService->moveStatus($user, $issue, $status);
    }
}
