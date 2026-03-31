@extends('layouts.glacier-app')

@section('content')
    <section class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="chip chip-primary mb-4">Issue Tracking</span>
            <h1 class="section-heading">Issue List</h1>
            <p class="section-copy mt-3 max-w-2xl">
                Review everything in one place with visual priority, clear ownership, and quick links into the current sprint.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="chip chip-secondary">{{ $issueCounters['open'] }} Open</span>
            <span class="chip chip-tertiary">{{ $issueCounters['inProgress'] }} In Progress</span>
            <a class="btn-secondary" href="{{ route('kanban') }}">Open Board</a>
        </div>
    </section>

    <section class="glass-panel overflow-hidden p-6">
        <div class="grid gap-3">
            @forelse ($issues as $issue)
                <article class="data-table-row md:grid-cols-[minmax(0,1.4fr)_minmax(0,0.8fr)_auto_auto_auto]">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="chip {{ $issue['priority'] === 'Critical' ? 'chip-danger' : 'chip-primary' }}">{{ $issue['priority'] }}</span>
                            <span class="chip chip-secondary">{{ $issue['type'] }}</span>
                            <span class="text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $issue['identifier'] }}</span>
                        </div>
                        <p class="mt-3 text-base font-semibold text-white">{{ $issue['title'] }}</p>
                        <p class="mt-2 text-sm text-copy-muted">{{ $issue['description'] }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-white">{{ $issue['project'] }}</p>
                        <p class="mt-2 text-sm text-copy-muted">Due {{ $issue['due'] }}</p>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Status</p>
                        <p class="mt-2 text-sm font-semibold text-white">{{ $issue['status'] }}</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-sm text-copy-muted">{{ $issue['comments'] }} comments</span>
                        <span class="text-sm text-copy-muted">{{ $issue['attachments'] }} files</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <img class="avatar avatar-sm" src="{{ asset($issue['assignee']['avatar']) }}" alt="{{ $issue['assignee']['name'] }}">
                        <a class="text-sm font-semibold text-primary" href="{{ route('issues.show', $issue['id']) }}">View</a>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-sm text-copy-muted">
                    No issues match the current workspace yet.
                </div>
            @endforelse
        </div>
    </section>
@endsection
