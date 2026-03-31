<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Issue;
use App\Models\Project;
use App\Models\TeamMember;
use App\Models\User;
use App\Models\Workspace;
use App\Services\DashboardService;
use App\Services\IssueService;
use App\Services\ProjectService;
use App\Services\WorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class GlacierController extends Controller
{
    public function __construct(
        private readonly WorkspaceService $workspaceService,
        private readonly DashboardService $dashboardService,
        private readonly ProjectService $projectService,
        private readonly IssueService $issueService,
    ) {
    }

    public function login(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return $this->guestPage('glacier.auth.login', [
            'pageTitle' => 'Login',
            'pageHeading' => 'Track issues with crystal clarity',
            'pageCopy' => 'Bring your team into focus with calm dashboards, graceful triage, and a clear path from idea to shipped work.',
            'heroImage' => 'images/glacier/textures/login-glass.png',
        ]);
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $remember = (bool) ($credentials['remember'] ?? false);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function register(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return $this->guestPage('glacier.auth.register', [
            'pageTitle' => 'Register',
            'pageHeading' => 'Create your Glacier workspace',
            'pageCopy' => 'Set up a polished issue hub for engineering, design, and product in just a few clicks.',
            'heroImage' => 'images/glacier/illustrations/workspace-logo.png',
        ]);
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'workspace_name' => ['required', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'job_title' => 'Workspace Admin',
                'avatar_path' => 'images/glacier/avatars/profile-main.png',
                'timezone' => 'Asia/Jakarta',
                'bio' => 'Created from the registration flow.',
                'email_verified_at' => now(),
                'password' => Hash::make($data['password']),
            ]);

            $this->workspaceService->createInitialWorkspace($user, $data['workspace_name']);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function forgotPassword(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return $this->guestPage('glacier.auth.forgot-password', [
            'pageTitle' => 'Forgot Password',
            'pageHeading' => 'Reset access without losing momentum',
            'pageCopy' => 'We will send a recovery link so you can get straight back to sprint planning, reviews, and team updates.',
            'heroImage' => 'images/glacier/illustrations/network-map.png',
        ]);
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status !== Password::RESET_LINK_SENT) {
            return back()->withErrors(['email' => __($status)]);
        }

        return back()->with('status', __($status));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(): View
    {
        $user = $this->currentUser();
        $workspace = $this->workspaceService->currentForUser($user);
        $data = $this->dashboardService->build($user, $workspace);
        $spotlight = $data['spotlightProject'];

        return $this->appPage('glacier.dashboard', $workspace, [
            'pageTitle' => 'Dashboard',
            'activeNav' => 'dashboard',
            'toolbarLinks' => [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => true],
                ['label' => 'Board', 'route' => 'kanban'],
                ['label' => 'Projects', 'route' => 'projects.index'],
            ],
            'topbarSummary' => 'See what needs attention first, who owns it, and what changed recently.',
            'metrics' => $data['metrics'],
            'spotlightProject' => $spotlight ? $this->presentProject($spotlight) : null,
            'priorityIssues' => $data['priorityIssues']->map(fn (Issue $issue): array => $this->presentIssue($issue))->all(),
            'teamMembers' => $data['teamMembers']->map(fn (TeamMember $membership): array => $this->presentMember($membership->user))->all(),
            'activityFeed' => $data['activityFeed']->map(fn (ActivityLog $event): array => $this->presentActivity($event))->all(),
        ]);
    }

    public function issuesIndex(Request $request): View
    {
        $user = $this->currentUser();
        $workspace = $this->workspaceService->currentForUser($user);
        $filters = $this->issueFiltersFromRequest($request);
        $issues = $this->issueService->listForWorkspace($workspace, $filters, $request->string('search')->toString());
        $openCount = $issues->filter(fn (Issue $issue) => $issue->status?->type !== 'DONE')->count();
        $inProgressCount = $issues->filter(fn (Issue $issue) => $issue->status?->type === 'IN_PROGRESS')->count();
        $firstProject = $workspace->projects()->first();

        return $this->appPage('glacier.issues.index', $workspace, [
            'pageTitle' => 'Issues',
            'activeNav' => 'issues',
            'toolbarLinks' => [
                ['label' => 'All Issues', 'route' => 'issues.index', 'active' => true],
                ['label' => 'Board', 'route' => 'kanban'],
                [
                    'label' => 'Project',
                    'route' => $firstProject ? 'projects.show' : 'projects.index',
                    'parameters' => $firstProject ? [$firstProject->getKey()] : [],
                ],
            ],
            'topbarSummary' => 'Start with priority, owner, and due date. Open an issue to see the full context.',
            'issueCounters' => [
                'open' => $openCount,
                'inProgress' => $inProgressCount,
            ],
            'issues' => $issues->map(fn (Issue $issue): array => $this->presentIssue($issue))->all(),
        ]);
    }

    public function issuesShow(Issue $issue): View
    {
        $this->authorize('view', $issue);

        $issue->load([
            'workspace.memberships.user',
            'project',
            'status',
            'assignee',
            'reporter',
            'labels',
            'comments.author',
            'activityLogs.actor',
        ]);

        $workspace = $issue->workspace;

        return $this->appPage('glacier.issues.show', $workspace, [
            'pageTitle' => 'Issue Detail',
            'activeNav' => 'issues',
            'toolbarLinks' => [
                ['label' => 'Issues', 'route' => 'issues.index'],
                ['label' => $issue->identifier, 'route' => 'issues.show', 'parameters' => [$issue->getKey()], 'active' => true],
                ['label' => 'Board', 'route' => 'kanban', 'parameters' => ['project' => $issue->project_id]],
            ],
            'topbarSummary' => 'Use this page to understand the issue, follow recent changes, and leave the next comment.',
            'issue' => $this->presentIssue($issue),
            'watchers' => $workspace->memberships
                ->take(4)
                ->map(fn (TeamMember $membership): array => $this->presentMember($membership->user))
                ->all(),
            'timeline' => $issue->activityLogs
                ->map(fn (ActivityLog $event): array => $this->presentActivity($event))
                ->all(),
            'comments' => $issue->comments
                ->map(fn ($comment): array => [
                    'id' => $comment->getKey(),
                    'author' => $this->presentMember($comment->author),
                    'body' => $comment->body,
                    'time' => $comment->created_at->diffForHumans(),
                ])
                ->all(),
        ]);
    }

    public function addComment(Request $request, Issue $issue): RedirectResponse
    {
        $this->authorize('view', $issue);
        $this->authorize('create', [\App\Models\Comment::class, $issue]);

        $data = $request->validate([
            'body' => ['required', 'string', 'min:3'],
        ]);

        $this->issueService->addComment($this->currentUser(), $issue, $data['body']);

        return redirect()->route('issues.show', $issue)->with('status', 'Comment added.');
    }

    public function projectsIndex(Request $request): View
    {
        $user = $this->currentUser();
        $workspace = $this->workspaceService->currentForUser($user);
        $projects = $this->projectService->listForWorkspace($workspace, $request->string('search')->toString());

        return $this->appPage('glacier.projects.index', $workspace, [
            'pageTitle' => 'Projects',
            'activeNav' => 'projects',
            'toolbarLinks' => [
                ['label' => 'Projects', 'route' => 'projects.index', 'active' => true],
                ['label' => 'Dashboard', 'route' => 'dashboard'],
                ['label' => 'Board', 'route' => 'kanban'],
            ],
            'topbarSummary' => 'Each project shows progress, health, owner, and the team currently involved.',
            'projects' => $projects->map(fn (Project $project): array => $this->presentProject($project))->all(),
        ]);
    }

    public function projectsShow(Project $project): View
    {
        $this->authorize('view', $project);

        $project->load([
            'workspace.memberships.user',
            'lead',
            'issues.status',
            'issues.assignee',
        ]);

        $workspace = $project->workspace;
        $projectMembers = $this->projectMembers($project);

        return $this->appPage('glacier.projects.show', $workspace, [
            'pageTitle' => 'Project Detail',
            'activeNav' => 'projects',
            'toolbarLinks' => [
                ['label' => 'Projects', 'route' => 'projects.index'],
                ['label' => $project->key, 'route' => 'projects.show', 'parameters' => [$project->getKey()], 'active' => true],
                ['label' => 'Issues', 'route' => 'issues.index'],
            ],
            'topbarSummary' => 'See project progress, recent issues, and the people responsible for delivery.',
            'project' => $this->presentProject($project),
            'projectIssues' => $project->issues
                ->sortByDesc('updated_at')
                ->take(4)
                ->map(fn (Issue $issue): array => $this->presentIssue($issue))
                ->all(),
            'teamMembers' => $projectMembers->map(fn (User $member): array => $this->presentMember($member))->all(),
            'milestones' => [
                ['label' => 'Delivery Progress', 'progress' => $this->projectService->progress($project)],
                ['label' => 'In Review Coverage', 'progress' => min(100, $project->issues->where('status.type', 'IN_REVIEW')->count() * 25)],
                ['label' => 'Done Coverage', 'progress' => min(100, $project->issues->where('status.type', 'DONE')->count() * 25)],
            ],
        ]);
    }

    public function kanban(Request $request): View
    {
        $user = $this->currentUser();
        $workspace = $this->workspaceService->currentForUser($user);
        $project = $workspace->projects()
            ->with(['lead', 'issues.status', 'issues.assignee', 'issues.project', 'issues.labels'])
            ->when($request->filled('project'), fn ($query) => $query->whereKey($request->string('project')->toString()))
            ->firstOrFail();

        $this->authorize('view', $project);

        $columns = collect($this->issueService->boardColumns($project))
            ->map(function (array $column): array {
                $status = $column['status'];

                return [
                    'title' => $status->name,
                    'count' => $column['issues']->count(),
                    'tone' => $this->statusTone($status->type),
                    'cards' => $column['issues']->map(fn (Issue $issue): array => $this->presentIssue($issue))->all(),
                ];
            })
            ->all();

        return $this->appPage('glacier.kanban', $workspace, [
            'pageTitle' => 'Kanban',
            'activeNav' => 'kanban',
            'toolbarLinks' => [
                ['label' => 'Issues', 'route' => 'issues.index'],
                ['label' => 'Board', 'route' => 'kanban', 'parameters' => ['project' => $project->getKey()], 'active' => true],
                ['label' => 'Project', 'route' => 'projects.show', 'parameters' => [$project->getKey()]],
            ],
            'topbarSummary' => 'Move across columns from left to right to understand what is next, active, and done.',
            'columns' => $columns,
        ]);
    }

    private function issueFiltersFromRequest(Request $request): array
    {
        return array_filter([
            'includeCompleted' => $request->boolean('includeCompleted'),
        ], static fn ($value) => $value !== null && $value !== false);
    }

    private function currentUser(): User
    {
        /** @var User $user */
        $user = Auth::user();

        return $user;
    }

    private function guestPage(string $view, array $data = []): View
    {
        return view($view, array_merge($this->sharedGuestData(), $data));
    }

    private function appPage(string $view, Workspace $workspace, array $data = []): View
    {
        return view($view, array_merge($this->sharedAppData($workspace), [
            'sidebarItems' => $this->sidebarItems($data['activeNav'] ?? ''),
            'secondaryItems' => $this->secondaryItems(),
            'topbarSummary' => $data['topbarSummary'] ?? 'Stay oriented with the clearest next step on each page.',
            'toolbarLinks' => $data['toolbarLinks'] ?? [],
        ], $data));
    }

    private function sharedGuestData(): array
    {
        return [
            'brand' => [
                'name' => 'Glacier Studio',
                'tagline' => 'Issue Tracker',
                'short' => 'Glacier',
            ],
        ];
    }

    private function sharedAppData(Workspace $workspace): array
    {
        $user = $this->currentUser();

        return [
            'brand' => [
                'name' => $workspace->name,
                'tagline' => strtoupper($workspace->slug),
                'short' => Str::of($workspace->name)->explode(' ')->map(fn ($word) => Str::substr($word, 0, 1))->join(''),
            ],
            'profile' => $this->presentMember($user),
        ];
    }

    private function sidebarItems(string $active): array
    {
        return [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard', 'active' => $active === 'dashboard'],
            ['label' => 'Issues', 'icon' => 'assignment_ind', 'route' => 'issues.index', 'active' => $active === 'issues'],
            ['label' => 'Projects', 'icon' => 'account_tree', 'route' => 'projects.index', 'active' => $active === 'projects'],
            ['label' => 'Board', 'icon' => 'view_kanban', 'route' => 'kanban', 'active' => $active === 'kanban'],
        ];
    }

    private function secondaryItems(): array
    {
        return [];
    }

    private function presentMember(User $user): array
    {
        return [
            'id' => $user->getKey(),
            'name' => $user->name,
            'role' => $user->job_title ?? 'Team Member',
            'avatar' => $user->avatar_path ?? 'images/glacier/avatars/profile-main.png',
            'email' => $user->email,
        ];
    }

    private function presentIssue(Issue $issue): array
    {
        return [
            'id' => $issue->getKey(),
            'identifier' => $issue->identifier,
            'title' => $issue->title,
            'project' => $issue->project?->name ?? 'Unknown Project',
            'project_id' => $issue->project_id,
            'status' => $issue->status?->name ?? 'Backlog',
            'priority' => $this->priorityLabel($issue->priority),
            'type' => Str::headline($issue->type),
            'comments' => $issue->comments_count ?? $issue->comments()->count(),
            'attachments' => 0,
            'due' => $issue->due_date?->format('M j') ?? 'No date',
            'assignee' => $issue->assignee ? $this->presentMember($issue->assignee) : $this->presentMember($this->currentUser()),
            'reporter' => $issue->reporter ? $this->presentMember($issue->reporter) : null,
            'description' => $issue->description ?: 'No description has been added yet.',
            'labels' => $issue->labels->map(fn ($label) => ['name' => $label->name, 'color' => $label->color])->all(),
        ];
    }

    private function presentProject(Project $project): array
    {
        return [
            'id' => $project->getKey(),
            'key' => $project->key,
            'title' => $project->name,
            'summary' => $project->description ?: 'No summary has been added yet.',
            'progress' => $this->projectService->progress($project),
            'health' => $this->projectService->health($project),
            'timeline' => $project->issues->count().' issues',
            'lead' => $project->lead ? $this->presentMember($project->lead) : $this->presentMember($this->currentUser()),
            'members' => $this->projectMembers($project)->take(4)->map(fn (User $member): array => $this->presentMember($member))->all(),
            'logo' => $project->icon_path ?: 'images/glacier/illustrations/workspace-logo.png',
            'network' => $project->network_path ?: 'images/glacier/illustrations/network-map.png',
        ];
    }

    private function projectMembers(Project $project)
    {
        return collect([$project->lead])
            ->merge($project->issues->map->assignee)
            ->filter()
            ->unique(fn (User $user) => $user->getKey())
            ->values();
    }

    private function presentActivity(ActivityLog $event): array
    {
        $title = match ($event->action) {
            'project.created' => ($event->metadata['name'] ?? 'Project').' was created',
            'project.updated' => ($event->metadata['name'] ?? 'Project').' was updated',
            'issue.created' => ($event->metadata['title'] ?? 'Issue').' was created',
            'issue.updated' => 'Issue details were updated',
            'issue.deleted' => ($event->metadata['title'] ?? 'Issue').' was removed',
            'comment.created' => 'A new comment was added to '.($event->metadata['issue'] ?? 'an issue'),
            default => Str::headline(str_replace('.', ' ', $event->action)),
        };

        return [
            'title' => $title,
            'time' => $event->created_at?->diffForHumans() ?? 'Just now',
        ];
    }

    private function priorityLabel(string $priority): string
    {
        return match (strtoupper($priority)) {
            'URGENT' => 'Critical',
            default => Str::headline($priority),
        };
    }

    private function statusTone(string $type): string
    {
        return match ($type) {
            'IN_PROGRESS' => 'primary',
            'DONE' => 'tertiary',
            default => 'secondary',
        };
    }
}
