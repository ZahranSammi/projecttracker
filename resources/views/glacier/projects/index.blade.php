@extends('layouts.glacier-app')

@section('content')
    <section class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="chip chip-tertiary mb-4">Portfolio</span>
            <h1 class="section-heading">Project List</h1>
            <p class="section-copy mt-3 max-w-2xl">
                A single view for Glacier initiatives, each with health, momentum, and a visual cue for who is carrying the work forward.
            </p>
        </div>
        <a class="btn-secondary" href="{{ $projects ? route('projects.show', $projects[0]['id']) : route('projects.index') }}">
            Featured Project
            <span class="material-symbols-outlined text-base">north_east</span>
        </a>
    </section>

    <section class="grid gap-5 xl:grid-cols-2">
        @forelse ($projects as $project)
            <article class="glass-panel overflow-hidden p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <img class="h-16 w-16 rounded-3xl border border-white/10 object-cover" src="{{ asset($project['logo']) }}" alt="{{ $project['title'] }}">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="chip {{ $project['health'] === 'Shipping' ? 'chip-primary' : ($project['health'] === 'Focused' ? 'chip-tertiary' : 'chip-secondary') }}">{{ $project['health'] }}</span>
                                <span class="chip">{{ $project['timeline'] }}</span>
                            </div>
                            <h2 class="mt-3 text-2xl font-semibold tracking-tight text-white">{{ $project['title'] }}</h2>
                            <p class="mt-3 max-w-xl text-sm leading-7 text-copy-muted">{{ $project['summary'] }}</p>
                        </div>
                    </div>
                    <a class="btn-secondary" href="{{ route('projects.show', $project['id']) }}">Open</a>
                </div>

                <div class="mt-6">
                    <div class="mb-2 flex items-center justify-between text-sm text-copy-muted">
                        <span>Completion</span>
                        <span>{{ $project['progress'] }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-white/8">
                        <div class="h-2 rounded-full bg-primary" style="width: {{ $project['progress'] }}%"></div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Lead</p>
                        <div class="mt-3 flex items-center gap-3">
                            <img class="avatar avatar-sm" src="{{ asset($project['lead']['avatar']) }}" alt="{{ $project['lead']['name'] }}">
                            <div>
                                <p class="font-semibold text-white">{{ $project['lead']['name'] }}</p>
                                <p class="text-sm text-copy-muted">{{ $project['lead']['role'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Team</p>
                        <div class="avatar-stack mt-3">
                            @foreach ($project['members'] as $member)
                                <img class="avatar avatar-sm" src="{{ asset($member['avatar']) }}" alt="{{ $member['name'] }}">
                            @endforeach
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 text-sm text-copy-muted">
                No projects exist yet. Create one through the API or seed a workspace to start planning work.
            </div>
        @endforelse
    </section>
@endsection
