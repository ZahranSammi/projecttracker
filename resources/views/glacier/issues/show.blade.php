@extends('layouts.glacier-app')

@section('content')
    <section class="mb-8 flex flex-wrap items-start justify-between gap-4">
        <div class="max-w-3xl">
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="chip chip-danger">{{ $issue['priority'] }}</span>
                <span class="chip chip-secondary">{{ $issue['type'] }}</span>
                <span class="chip">{{ $issue['status'] }}</span>
                <span class="chip">{{ $issue['identifier'] }}</span>
            </div>
            <h1 class="section-heading">Issue detail</h1>
            <p class="mt-4 text-xl font-semibold tracking-tight text-white">{{ $issue['title'] }}</p>
            <p class="section-copy mt-3 max-w-2xl">This page shows what needs to be done, who owns it, what changed recently, and where to continue next.</p>
        </div>
        <div class="glass-panel flex min-w-[18rem] items-center gap-4 p-4">
            <img class="avatar avatar-lg" src="{{ asset($issue['assignee']['avatar']) }}" alt="{{ $issue['assignee']['name'] }}">
            <div>
                <p class="text-sm text-copy-muted">Owner</p>
                <p class="text-lg font-semibold text-white">{{ $issue['assignee']['name'] }}</p>
                <p class="text-sm text-copy-muted">{{ $issue['assignee']['role'] }}</p>
            </div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="space-y-6">
            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">What needs to be done</h2>
                <p class="mt-4 text-sm leading-7 text-copy-muted">
                    {{ $issue['description'] }}
                </p>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl border border-white/8 bg-white/4 p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Project</p>
                        <p class="mt-2 font-semibold text-white">{{ $issue['project'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/8 bg-white/4 p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Due</p>
                        <p class="mt-2 font-semibold text-white">{{ $issue['due'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/8 bg-white/4 p-4">
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Attachments</p>
                        <p class="mt-2 font-semibold text-white">{{ $issue['attachments'] }}</p>
                    </div>
                </div>
            </article>

            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">Recent changes</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($timeline as $event)
                        <div class="rounded-2xl border border-white/8 bg-white/4 p-4">
                            <p class="text-sm font-medium text-white">{{ $event['title'] }}</p>
                            <p class="mt-2 text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $event['time'] }}</p>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="glass-panel p-6">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-semibold text-white">Comments</h2>
                    <span class="chip chip-primary">{{ count($comments) }} Comments</span>
                </div>

                <div class="mt-5 space-y-4">
                    @foreach ($comments as $comment)
                        <div class="rounded-2xl border border-white/8 bg-white/4 p-4">
                            <div class="flex items-center gap-3">
                                <img class="avatar avatar-sm" src="{{ asset($comment['author']['avatar']) }}" alt="{{ $comment['author']['name'] }}">
                                <div>
                                    <p class="font-semibold text-white">{{ $comment['author']['name'] }}</p>
                                    <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $comment['time'] }}</p>
                                </div>
                            </div>
                            <p class="mt-4 text-sm leading-7 text-copy-muted">{{ $comment['body'] }}</p>
                        </div>
                    @endforeach
                </div>

                <form class="mt-5 space-y-3" method="POST" action="{{ route('issues.comments.store', $issue['id']) }}">
                    @csrf
                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Write a comment</span>
                        <textarea class="form-field min-h-28" name="body" placeholder="Add the next update, decision, or fix plan..."></textarea>
                    </label>
                    <button class="btn-primary" type="submit">Post Comment</button>
                </form>
            </article>
        </div>

        <div class="space-y-6">
            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">People following this issue</h2>
                <div class="mt-5 space-y-4">
                    @foreach ($watchers as $watcher)
                        <div class="flex items-center gap-4">
                            <img class="avatar avatar-sm" src="{{ asset($watcher['avatar']) }}" alt="{{ $watcher['name'] }}">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-white">{{ $watcher['name'] }}</p>
                                <p class="truncate text-sm text-copy-muted">{{ $watcher['role'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="glass-panel p-6">
                <h2 class="text-xl font-semibold text-white">Next actions</h2>
                <div class="mt-5 grid gap-3">
                    <a class="btn-secondary w-full" href="{{ route('issues.index') }}">Back to issues</a>
                    <a class="btn-secondary w-full" href="{{ route('kanban', ['project' => $issue['project_id']]) }}">Open board</a>
                    <a class="btn-secondary w-full" href="{{ route('projects.show', $issue['project_id']) }}">Open project</a>
                </div>
            </article>
        </div>
    </section>
@endsection
