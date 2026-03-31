<header class="glacier-topbar">
    <div class="flex min-w-0 flex-1 items-center gap-3 lg:gap-6">
        <a class="text-lg font-semibold tracking-tight text-white md:hidden" href="{{ route('dashboard') }}">
            {{ $brand['short'] }}
        </a>

        <div class="hidden min-w-0 md:block">
            <p class="text-sm font-semibold text-white">{{ $pageTitle ?? 'Workspace' }}</p>
            <p class="truncate text-xs text-copy-muted">{{ $topbarSummary }}</p>
        </div>

        @if (! empty($toolbarLinks))
            <nav class="hidden items-center gap-1 lg:flex">
                @foreach ($toolbarLinks as $item)
                    @php($classes = 'toolbar-link' . (! empty($item['active']) ? ' toolbar-link-active' : ''))
                    <a class="{{ $classes }}" href="{{ route($item['route'], $item['parameters'] ?? []) }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        @endif
    </div>

    <div class="flex items-center gap-2 lg:gap-4">
        <div class="hidden text-right lg:block">
            <p class="text-sm font-semibold text-white">{{ $profile['name'] }}</p>
            <p class="text-xs text-copy-muted">{{ $profile['role'] }}</p>
        </div>
        <img class="avatar avatar-sm" src="{{ asset($profile['avatar']) }}" alt="{{ $profile['name'] }}">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn-secondary px-4 py-2.5" type="submit" title="Sign out">
                Sign out
                <span class="material-symbols-outlined text-[18px]">logout</span>
            </button>
        </form>
    </div>
</header>
