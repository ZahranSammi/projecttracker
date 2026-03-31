<?php

namespace Database\Seeders;

use App\Models\Issue;
use App\Models\Label;
use App\Models\User;
use App\Services\IssueService;
use App\Services\ProjectService;
use App\Services\WorkspaceService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $workspaceService = app(WorkspaceService::class);
        $projectService = app(ProjectService::class);
        $issueService = app(IssueService::class);

        $owner = User::query()->create([
            'name' => 'Zahra Tan',
            'email' => 'zahra@glacier.app',
            'job_title' => 'Product Engineering Lead',
            'avatar_path' => 'images/glacier/avatars/profile-main.png',
            'timezone' => 'Asia/Jakarta',
            'bio' => 'Leading delivery across product, design, and engineering.',
            'email_verified_at' => now(),
            'password' => Hash::make('preview-demo'),
        ]);

        $workspace = $workspaceService->createInitialWorkspace($owner, 'Glacier Studio');

        $team = collect([
            ['name' => 'Alex Morgan', 'email' => 'alex@glacier.app', 'job_title' => 'Frontend Engineer', 'avatar_path' => 'images/glacier/avatars/alex.png'],
            ['name' => 'Sarah Chen', 'email' => 'sarah@glacier.app', 'job_title' => 'Product Designer', 'avatar_path' => 'images/glacier/avatars/sarah.png'],
            ['name' => 'Marcus Hale', 'email' => 'marcus@glacier.app', 'job_title' => 'Staff Engineer', 'avatar_path' => 'images/glacier/avatars/marcus.png'],
            ['name' => 'Priya Raman', 'email' => 'priya@glacier.app', 'job_title' => 'Design Systems Lead', 'avatar_path' => 'images/glacier/avatars/priya.png'],
            ['name' => 'Mia Foster', 'email' => 'mia@glacier.app', 'job_title' => 'Technical Writer', 'avatar_path' => 'images/glacier/avatars/mia.png'],
            ['name' => 'Diego Alvarez', 'email' => 'diego@glacier.app', 'job_title' => 'Platform Engineer', 'avatar_path' => 'images/glacier/avatars/diego.png'],
        ])->map(function (array $attributes) use ($workspace): User {
            $user = User::query()->create($attributes + [
                'timezone' => 'Asia/Jakarta',
                'password' => Hash::make('preview-demo'),
                'email_verified_at' => now(),
            ]);

            $workspace->memberships()->create([
                'user_id' => $user->getKey(),
                'role' => 'MEMBER',
                'joined_at' => now(),
            ]);

            return $user;
        })->prepend($owner);

        $projects = collect([
            [
                'key' => 'CORE',
                'name' => 'Core Infrastructure',
                'description' => 'Stabilize the Glacier frontend foundation, local assets, and production-ready design tokens.',
                'lead_user_id' => $team->firstWhere('email', 'marcus@glacier.app')?->getKey(),
            ],
            [
                'key' => 'DSYS',
                'name' => 'Design System',
                'description' => 'Package the glass language into reusable components with clear interaction states.',
                'lead_user_id' => $team->firstWhere('email', 'priya@glacier.app')?->getKey(),
            ],
            [
                'key' => 'ONBD',
                'name' => 'Customer Onboarding',
                'description' => 'Refine first-run flows, account creation, and password recovery moments.',
                'lead_user_id' => $team->firstWhere('email', 'alex@glacier.app')?->getKey(),
            ],
            [
                'key' => 'RELO',
                'name' => 'Release Ops',
                'description' => 'Keep release readiness visible through cleaner dashboards, issue health, and sprint status.',
                'lead_user_id' => $team->firstWhere('email', 'diego@glacier.app')?->getKey(),
            ],
        ])->map(fn (array $project) => $projectService->create($owner, $workspace, $project));

        $labels = Label::query()
            ->where('workspace_id', $workspace->getKey())
            ->get()
            ->keyBy('name');

        $statuses = $workspace->statuses()->get()->keyBy('name');
        $projectIndex = $projects->keyBy('key');

        $issues = collect([
            [
                'project' => 'CORE',
                'title' => 'Implement frozen glass effect on sidebar navigation links',
                'description' => 'Translate the visual treatment into stable Laravel components with motion and contrast tuned for long sessions.',
                'status' => 'In Progress',
                'priority' => 'URGENT',
                'type' => 'FEATURE',
                'assignee' => 'alex@glacier.app',
                'reporter' => 'zahra@glacier.app',
                'label_ids' => [$labels['Feature']->getKey(), $labels['Design']->getKey()],
                'due_date' => now()->endOfDay(),
            ],
            [
                'project' => 'CORE',
                'title' => 'Optimize backdrop-filter performance for tablet breakpoints',
                'description' => 'Reduce excessive blur layers and keep depth cues intact as the layout collapses.',
                'status' => 'Todo',
                'priority' => 'HIGH',
                'type' => 'REFACTOR',
                'assignee' => 'priya@glacier.app',
                'reporter' => 'marcus@glacier.app',
                'label_ids' => [$labels['Design']->getKey()],
                'due_date' => now()->addDay(),
            ],
            [
                'project' => 'DSYS',
                'title' => 'Design system token integration for Tailwind utilities',
                'description' => 'Move the exported color and radius system into local theme tokens.',
                'status' => 'Todo',
                'priority' => 'HIGH',
                'type' => 'FEATURE',
                'assignee' => 'sarah@glacier.app',
                'reporter' => 'priya@glacier.app',
                'label_ids' => [$labels['Feature']->getKey(), $labels['Design']->getKey()],
                'due_date' => now()->addDays(2),
            ],
            [
                'project' => 'RELO',
                'title' => 'Memory leak in real-time updates for dashboard charts',
                'description' => 'Stabilize the dashboard summary widgets before the next stakeholder demo.',
                'status' => 'In Progress',
                'priority' => 'URGENT',
                'type' => 'BUG',
                'assignee' => 'diego@glacier.app',
                'reporter' => 'zahra@glacier.app',
                'label_ids' => [$labels['Bug']->getKey(), $labels['Infra']->getKey()],
                'due_date' => now()->addDay(),
            ],
            [
                'project' => 'ONBD',
                'title' => 'Automated deployment pipeline for onboarding edge functions',
                'description' => 'Finalize the delivery workflow so onboarding surfaces can ship on a predictable cadence.',
                'status' => 'Backlog',
                'priority' => 'MEDIUM',
                'type' => 'INFRA',
                'assignee' => 'marcus@glacier.app',
                'reporter' => 'diego@glacier.app',
                'label_ids' => [$labels['Infra']->getKey()],
                'due_date' => now()->addDays(4),
            ],
            [
                'project' => 'ONBD',
                'title' => 'Reset-password flow should confirm email delivery status',
                'description' => 'Add a clean success state after reset-link requests so users understand what happens next.',
                'status' => 'Done',
                'priority' => 'MEDIUM',
                'type' => 'FEATURE',
                'assignee' => 'mia@glacier.app',
                'reporter' => 'zahra@glacier.app',
                'label_ids' => [$labels['Feature']->getKey()],
                'due_date' => now()->subDay(),
            ],
        ])->map(function (array $issue) use ($issueService, $projectIndex, $statuses, $team): Issue {
            $project = $projectIndex[$issue['project']];
            $assignee = $team->firstWhere('email', $issue['assignee']);
            $reporter = $team->firstWhere('email', $issue['reporter']);

            return $issueService->create($reporter, $project, [
                'title' => $issue['title'],
                'description' => $issue['description'],
                'status_id' => $statuses[$issue['status']]->getKey(),
                'priority' => $issue['priority'],
                'type' => $issue['type'],
                'assignee_id' => $assignee?->getKey(),
                'reporter_id' => $reporter?->getKey(),
                'label_ids' => $issue['label_ids'],
                'due_date' => $issue['due_date'],
            ]);
        });

        $firstIssue = $issues->first();

        if ($firstIssue instanceof Issue) {
            $issueService->addComment(
                $team->firstWhere('email', 'sarah@glacier.app'),
                $firstIssue,
                'The visual treatment is approved. Keep the focus halo subtle on lower-brightness displays.',
            );

            $issueService->addComment(
                $team->firstWhere('email', 'priya@glacier.app'),
                $firstIssue,
                'I can take the tablet breakpoint next. The blur stack still feels too dense there.',
            );
        }
    }
}
