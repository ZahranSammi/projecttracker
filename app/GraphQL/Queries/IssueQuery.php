<?php

namespace App\GraphQL\Queries;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use App\Services\IssueService;
use App\Support\GraphQLConnection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class IssueQuery
{
    public function __construct(
        private readonly IssueService $issueService,
    ) {
    }

    public function issues($_, array $args): array
    {
        $project = Project::query()->with('workspace')->findOrFail($args['projectId']);
        Gate::authorize('viewAny', [Issue::class, $project]);

        $builder = $this->issueService->queryForWorkspace(
            $project->workspace,
            $args['filters'] ?? [],
            $args['search'] ?? null,
            $project->getKey(),
            $args['sortBy'] ?? 'updatedAt',
            $args['sortDirection'] ?? 'DESC',
        );

        return GraphQLConnection::fromBuilder($builder, $args['pagination'] ?? []);
    }

    public function issue($_, array $args): Issue
    {
        $issue = Issue::query()
            ->with(['workspace', 'project', 'status', 'assignee', 'reporter', 'labels'])
            ->findOrFail($args['id']);

        Gate::authorize('view', $issue);

        return $issue;
    }

    public function myAssignedIssues($_, array $args): array
    {
        /** @var User $user */
        $user = Auth::user();
        $workspace = Workspace::query()->findOrFail($args['workspaceId']);
        Gate::authorize('view', $workspace);

        $builder = $this->issueService->queryForWorkspace(
            $workspace,
            $args['filters'] ?? [],
            null,
        )->where('assignee_id', $user->getKey());

        return GraphQLConnection::fromBuilder($builder, $args['pagination'] ?? []);
    }

    public function issuesForProject(Project $project, array $args): array
    {
        Gate::authorize('view', $project);

        $builder = $this->issueService->queryForWorkspace(
            $project->workspace,
            $args['filters'] ?? [],
            null,
            $project->getKey(),
        );

        return GraphQLConnection::fromBuilder($builder, $args['pagination'] ?? []);
    }

    public function comments(Issue $issue, array $args): array
    {
        Gate::authorize('view', $issue);

        return GraphQLConnection::fromBuilder(
            $issue->comments()->with('author')->getQuery(),
            $args['pagination'] ?? [],
        );
    }

    public function activity(Issue $issue, array $args): array
    {
        Gate::authorize('view', $issue);

        return GraphQLConnection::fromBuilder(
            $issue->activityLogs()->with('actor')->getQuery(),
            $args['pagination'] ?? [],
        );
    }

    public function assignedIssuesForUser(User $user, array $args): array
    {
        /** @var User $viewer */
        $viewer = Auth::user();
        $workspace = $user->currentWorkspace();

        abort_unless($workspace instanceof Workspace, 403);
        Gate::authorize('view', $workspace);

        $builder = $this->issueService->queryForWorkspace(
            $workspace,
            [],
            null,
            $args['projectId'] ?? null,
        )->where('assignee_id', $user->getKey());

        return GraphQLConnection::fromBuilder($builder, $args['pagination'] ?? []);
    }
}
