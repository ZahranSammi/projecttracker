<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly IssueService $issueService,
        private readonly ActivityLogService $activityLogService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(User $user, Workspace $workspace): array
    {
        $projects = $this->projectService->listForWorkspace($workspace);
        $issues = $this->issueService->queryForWorkspace($workspace, ['includeCompleted' => true])->get();
        $assigned = $issues->where('assignee_id', $user->getKey());
        $completedThisWeek = $issues->filter(fn (Issue $issue) => $issue->status?->type === 'DONE' && $issue->updated_at?->isCurrentWeek())->count();
        $overdue = $issues->filter(fn (Issue $issue) => $issue->due_date?->isPast() && $issue->status?->type !== 'DONE')->count();
        $open = $issues->filter(fn (Issue $issue) => $issue->status?->type !== 'DONE')->count();
        $inProgress = $issues->filter(fn (Issue $issue) => $issue->status?->type === 'IN_PROGRESS')->count();

        $spotlight = $projects->sortByDesc(fn ($project) => $project->issues->count())->first();

        return [
            'workspace' => $workspace,
            'projects' => $projects,
            'metrics' => [
                ['label' => 'Open Issues', 'value' => (string) $open, 'delta' => $overdue.' overdue', 'tone' => 'primary'],
                ['label' => 'Projects in Flight', 'value' => (string) $projects->count(), 'delta' => $projects->where('is_archived', false)->count().' active', 'tone' => 'secondary'],
                ['label' => 'Assigned to Me', 'value' => (string) $assigned->count(), 'delta' => $inProgress.' in progress', 'tone' => 'tertiary'],
                ['label' => 'Completed This Week', 'value' => (string) $completedThisWeek, 'delta' => 'Recent throughput', 'tone' => 'primary'],
            ],
            'spotlightProject' => $spotlight,
            'priorityIssues' => $issues
                ->filter(fn (Issue $issue) => $issue->status?->type !== 'DONE')
                ->sortByDesc(fn (Issue $issue) => [$issue->priority, $issue->updated_at?->timestamp])
                ->take(4)
                ->values(),
            'teamMembers' => $workspace->memberships()->with('user')->get(),
            'activityFeed' => $this->activityLogService->feed($workspace, limit: 6),
        ];
    }
}
