<?php

namespace App\GraphQL\Queries;

use App\Models\Project;
use App\Models\Workspace;
use App\Services\ProjectService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ProjectQuery
{
    public function __construct(
        private readonly ProjectService $projectService,
    ) {
    }

    public function projects($_, array $args)
    {
        $workspace = Workspace::query()->findOrFail($args['workspaceId']);
        Gate::authorize('view', $workspace);

        return $this->projectService->listForWorkspace($workspace, $args['search'] ?? null);
    }

    public function project($_, array $args): Project
    {
        $project = Project::query()
            ->with(['workspace', 'lead', 'issues.status'])
            ->findOrFail($args['id']);

        Gate::authorize('view', $project);

        return $project;
    }

    /**
     * @return array<string, int>
     */
    public function issueStats(Project $project): array
    {
        $project->loadMissing('issues.status');

        $issues = $project->issues;

        return [
            'total' => $issues->count(),
            'backlog' => $issues->where('status.type', 'BACKLOG')->count(),
            'todo' => $issues->where('status.type', 'TODO')->count(),
            'inProgress' => $issues->where('status.type', 'IN_PROGRESS')->count(),
            'inReview' => $issues->where('status.type', 'IN_REVIEW')->count(),
            'done' => $issues->where('status.type', 'DONE')->count(),
            'overdue' => $issues->filter(fn ($issue) => $issue->due_date?->isPast() && $issue->status?->type !== 'DONE')->count(),
        ];
    }
}
