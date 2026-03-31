<aside class="glacier-sidebar">
    <div class="flex items-center gap-3 px-1">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-primary/20 bg-primary/12">
            <span class="material-symbols-outlined text-primary">ac_unit</span>
        </div>
        <div>
            <p class="text-lg font-semibold tracking-tight text-white">{{ $brand['name'] }}</p>
            <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">{{ $brand['tagline'] }}</p>
        </div>
    </div>

    <a class="btn-secondary w-full" href="{{ route('issues.index') }}">
        <span class="material-symbols-outlined text-base">list_alt</span>
        View Issues
    </a>

    <div>
        <p class="px-3 pb-2 text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Main menu</p>
        <nav class="space-y-1">
        @foreach ($sidebarItems as $item)
            @php($classes = 'nav-link' . ($item['active'] ? ' nav-link-active' : '') . (! empty($item['disabled']) ? ' nav-link-muted' : ''))
            @if (! empty($item['disabled']))
                <span class="{{ $classes }}">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </span>
            @else
                <a
                    class="{{ $classes }}"
                    href="{{ route($item['route']) }}"
                    @if ($item['active']) aria-current="page" @endif
                >
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
        </nav>
    </div>

    @if (! empty($secondaryItems))
        <div class="mt-auto space-y-1 border-t border-white/8 pt-4">
            @foreach ($secondaryItems as $item)
                <span class="nav-link nav-link-muted">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    {{ $item['label'] }}
                </span>
            @endforeach
        </div>
    @endif

    <div class="glass-panel mt-auto flex items-center gap-3 rounded-2xl p-3">
        <img class="avatar avatar-sm" src="{{ asset($profile['avatar']) }}" alt="{{ $profile['name'] }}">
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-white">{{ $profile['name'] }}</p>
            <p class="truncate text-xs text-copy-muted">{{ $profile['role'] }}</p>
        </div>
    </div>
</aside>
