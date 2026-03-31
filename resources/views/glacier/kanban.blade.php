@extends('layouts.glacier-app')

@section('content')
    <section class="mb-8 flex flex-wrap items-end justify-between gap-4">
        <div>
            <span class="chip chip-primary mb-4">Board</span>
            <h1 class="section-heading">Kanban board</h1>
            <p class="section-copy mt-3 max-w-2xl">
                Read the work from left to right. This view is best when you want to see what is waiting, in progress, and done at a glance.
            </p>
        </div>

        <div class="avatar-stack">
            @foreach (array_slice($columns[1]['cards'], 0, 2) as $card)
                <img class="avatar avatar-sm" src="{{ asset($card['assignee']['avatar']) }}" alt="{{ $card['assignee']['name'] }}">
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-3">
        @foreach ($columns as $column)
            <article class="kanban-column">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="h-2.5 w-2.5 rounded-full {{ $column['tone'] === 'primary' ? 'bg-primary' : ($column['tone'] === 'tertiary' ? 'bg-tertiary' : 'bg-secondary') }}"></span>
                        <h2 class="font-semibold text-white">{{ $column['title'] }}</h2>
                    </div>
                    <span class="chip">{{ $column['count'] }}</span>
                </div>

                <div class="glacier-scrollbar space-y-4 overflow-y-auto pr-1">
                    @foreach ($column['cards'] as $card)
                        <a class="kanban-card block" href="{{ route('issues.show', $card['id']) }}">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <span class="chip {{ ($card['priority'] ?? '') === 'Critical' ? 'chip-danger' : 'chip-primary' }}">{{ $card['priority'] }}</span>
                                <span class="text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $card['type'] }}</span>
                            </div>
                            <p class="mt-3 text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $card['identifier'] }}</p>
                            <p class="mt-4 text-base font-semibold leading-7 text-white">{{ $card['title'] }}</p>
                            <p class="mt-2 text-sm text-copy-muted">{{ $card['project'] }}</p>

                            <div class="mt-5 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 text-sm text-copy-muted">
                                    <span>{{ $card['comments'] }} comments</span>
                                    <span>{{ $card['attachments'] }} files</span>
                                </div>
                                <img class="avatar avatar-sm" src="{{ asset($card['assignee']['avatar']) }}" alt="{{ $card['assignee']['name'] }}">
                            </div>
                        </a>
                    @endforeach
                </div>
            </article>
        @endforeach
    </section>
@endsection
