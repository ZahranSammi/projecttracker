@extends('layouts.glacier-app')

@section('content')
    <section class="mb-8 hero-grid items-start">
        <div>
            <span class="chip chip-primary mb-4">Overview</span>
            <h1 class="section-heading">Dashboard</h1>
            <p class="section-copy mt-3 max-w-2xl">
                A calm operating surface for the Glacier preview. Track sprint health, follow the highest-risk issues, and keep the team oriented without digging through noisy menus.
            </p>
        </div>

        <div class="glass-panel flex flex-wrap items-center justify-between gap-4 p-5">
            <div>
                <p class="text-sm font-semibold text-white">Spotlight project</p>
                @if ($spotlightProject)
                    <p class="mt-1 text-xl font-semibold text-gradient">{{ $spotlightProject['title'] }}</p>
                    <p class="mt-2 max-w-md text-sm leading-6 text-copy-muted">{{ $spotlightProject['summary'] }}</p>
                @else
                    <p class="mt-1 text-xl font-semibold text-gradient">No projects yet</p>
                    <p class="mt-2 max-w-md text-sm leading-6 text-copy-muted">Create your first project to start tracking work across the workspace.</p>
                @endif
            </div>
            @if ($spotlightProject)
                <a class="btn-secondary" href="{{ route('projects.show', $spotlightProject['id']) }}">
                    Open Project
                    <span class="material-symbols-outlined text-base">north_east</span>
                </a>
            @endif
        </div>
    </section>

    <section class="mb-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($metrics as $metric)
            <article class="metric-card">
                <p class="text-sm text-copy-muted">{{ $metric['label'] }}</p>
                <p class="mt-4 text-4xl font-semibold tracking-tight text-white">{{ $metric['value'] }}</p>
                <p class="mt-3 text-sm {{ $metric['tone'] === 'tertiary' ? 'text-tertiary' : ($metric['tone'] === 'secondary' ? 'text-secondary' : 'text-primary') }}">
                    {{ $metric['delta'] }}
                </p>
            </article>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            <article class="glass-panel overflow-hidden p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <span class="chip chip-secondary">Current Sprint</span>
                        <h2 class="mt-4 text-2xl font-semibold tracking-tight text-white">{{ $spotlightProject['title'] ?? 'No active project' }}</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-copy-muted">{{ $spotlightProject['summary'] ?? 'Create a project to start tracking delivery.' }}</p>
                    </div>
                    @if ($spotlightProject)
                        <img class="h-20 w-20 rounded-3xl border border-white/10 object-cover" src="{{ asset($spotlightProject['logo']) }}" alt="{{ $spotlightProject['title'] }}">
                    @endif
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Health</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $spotlightProject['health'] ?? 'Ready' }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Progress</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $spotlightProject['progress'] ?? 0 }}%</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Timeline</p>
                        <p class="mt-2 text-lg font-semibold text-white">{{ $spotlightProject['timeline'] ?? 'No issues yet' }}</p>
                    </div>
                </div>
            </article>

            <article class="glass-panel p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Priority Queue</h2>
                        <p class="mt-2 text-sm text-copy-muted">The most visible issues across Glacier right now.</p>
                    </div>
                    <a class="btn-secondary" href="{{ route('issues.index') }}">View All Issues</a>
                </div>

                <div class="space-y-3">
                    @forelse ($priorityIssues as $issue)
                        <a class="data-table-row md:grid-cols-[minmax(0,1fr)_auto_auto]" href="{{ route('issues.show', $issue['id']) }}">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="chip {{ $issue['priority'] === 'Critical' ? 'chip-danger' : 'chip-primary' }}">{{ $issue['priority'] }}</span>
                                    <span class="text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $issue['type'] }}</span>
                                </div>
                                <p class="mt-3 text-base font-semibold text-white">{{ $issue['title'] }}</p>
                                <p class="mt-1 text-sm text-copy-muted">{{ $issue['project'] }}</p>
                            </div>
                            <div class="text-sm text-copy-muted">{{ $issue['status'] }}</div>
                            <img class="avatar avatar-sm justify-self-start md:justify-self-end" src="{{ asset($issue['assignee']['avatar']) }}" alt="{{ $issue['assignee']['name'] }}">
                        </a>
                    @empty
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-copy-muted">
                            No issues yet. Create a project and start tracking work.
                        </div>
                    @endforelse
                </div>
            </article>
        </div>

        <div class="space-y-6">
            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">Team Signal</h2>
                <p class="mt-2 text-sm text-copy-muted">A quick read on the people carrying the current sprint.</p>

                <div class="mt-5 space-y-4">
                    @foreach ($teamMembers as $member)
                        <div class="flex items-center gap-4">
                            <img class="avatar avatar-sm" src="{{ asset($member['avatar']) }}" alt="{{ $member['name'] }}">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-semibold text-white">{{ $member['name'] }}</p>
                                <p class="truncate text-sm text-copy-muted">{{ $member['role'] }}</p>
                            </div>
                            <span class="chip chip-primary">Focused</span>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">Recent Activity</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($activityFeed as $event)
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-sm font-medium text-white">{{ $event['title'] }}</p>
                            <p class="mt-2 text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $event['time'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>
        </div>
    </section>
@endsection
