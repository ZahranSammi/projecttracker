@extends('layouts.glacier-app')

@section('content')
    <section class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="chip chip-primary mb-4">All issues</span>
            <h1 class="section-heading">Issues</h1>
            <p class="section-copy mt-3 max-w-2xl">
                Review every issue in one place. Start with priority, check the owner and due date, then open the detail page when you need more context.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="chip chip-secondary">{{ $issueCounters['open'] }} Open</span>
            <span class="chip chip-tertiary">{{ $issueCounters['inProgress'] }} In Progress</span>
            <a class="btn-secondary" href="{{ route('kanban') }}">Open board</a>
        </div>
    </section>

    <section class="glass-panel overflow-hidden p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-white">Start with these details</h2>
                <p class="mt-2 text-sm text-copy-muted">Each row answers five questions: what is it, how urgent is it, who owns it, when is it due, and what should you open next.</p>
            </div>
            <span class="chip">Sorted for quick scanning</span>
        </div>
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
                        <a class="text-sm font-semibold text-primary" href="{{ route('issues.show', $issue['id']) }}">Open issue</a>
                    </div>
                </article>
            @empty
                <div class="surface-subtle rounded-2xl p-6 text-sm text-copy-muted">
                    No issues yet. Once work is added, this page will become the main list for triage.
                </div>
            @endforelse
        </div>
    </section>
@endsection
