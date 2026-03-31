@extends('layouts.glacier-app')

@section('content')
    <section class="mb-8 hero-grid items-start">
        <div>
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="chip chip-primary">{{ $project['health'] }}</span>
                <span class="chip">{{ $project['timeline'] }}</span>
            </div>
            <h1 class="section-heading">Project overview</h1>
            <p class="mt-4 text-2xl font-semibold tracking-tight text-white">{{ $project['title'] }}</p>
            <p class="section-copy mt-3 max-w-2xl">Use this page to understand project progress, current issues, and the people responsible for moving it forward.</p>
        </div>

        <div class="glass-panel overflow-hidden p-4">
            <img class="h-64 w-full rounded-[1.5rem] object-cover" src="{{ asset($project['network']) }}" alt="{{ $project['title'] }} network illustration">
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            <article class="glass-panel p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Progress</h2>
                        <p class="mt-2 text-sm text-copy-muted">These bars show how far the project has moved through delivery and review.</p>
                    </div>
                    <span class="chip chip-primary">{{ $project['progress'] }}% complete</span>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach ($milestones as $milestone)
                        <div class="rounded-2xl border border-white/8 bg-white/4 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <p class="font-semibold text-white">{{ $milestone['label'] }}</p>
                                <span class="text-sm text-copy-muted">{{ $milestone['progress'] }}%</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white/8">
                                <div class="h-2 rounded-full bg-primary" style="width: {{ $milestone['progress'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="glass-panel p-6">
                <div class="mb-5 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Recent issues</h2>
                        <p class="mt-2 text-sm text-copy-muted">Open one of these issues if you want the latest delivery details for this project.</p>
                    </div>
                    <a class="btn-secondary" href="{{ route('issues.index') }}">See all issues</a>
                </div>

                <div class="space-y-3">
                    @foreach ($projectIssues as $issue)
                        <a class="data-table-row md:grid-cols-[minmax(0,1fr)_auto_auto]" href="{{ route('issues.show', $issue['id']) }}">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="chip {{ $issue['priority'] === 'Critical' ? 'chip-danger' : 'chip-primary' }}">{{ $issue['priority'] }}</span>
                                    <span class="text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $issue['type'] }}</span>
                                </div>
                                <p class="mt-3 font-semibold text-white">{{ $issue['title'] }}</p>
                            </div>
                            <div class="text-sm text-copy-muted">{{ $issue['status'] }}</div>
                            <img class="avatar avatar-sm justify-self-start md:justify-self-end" src="{{ asset($issue['assignee']['avatar']) }}" alt="{{ $issue['assignee']['name'] }}">
                        </a>
                    @endforeach
                </div>
            </article>
        </div>

        <div class="space-y-6">
            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">Project Lead</h2>
                <div class="mt-5 flex items-center gap-4">
                    <img class="avatar avatar-lg" src="{{ asset($project['lead']['avatar']) }}" alt="{{ $project['lead']['name'] }}">
                    <div>
                        <p class="text-lg font-semibold text-white">{{ $project['lead']['name'] }}</p>
                        <p class="mt-1 text-sm text-copy-muted">{{ $project['lead']['role'] }}</p>
                    </div>
                </div>
            </article>

            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">Team</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($teamMembers as $member)
                        <div class="flex items-center gap-4">
                            <img class="avatar avatar-sm" src="{{ asset($member['avatar']) }}" alt="{{ $member['name'] }}">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $member['name'] }}</p>
                                <p class="truncate text-sm text-copy-muted">{{ $member['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        </div>
    </section>
@endsection
