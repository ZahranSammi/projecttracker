<header class="glacier-topbar">
    <div class="flex min-w-0 flex-1 items-center gap-3 lg:gap-6">
        <a class="text-lg font-semibold tracking-tight text-white md:hidden" href="{{ route('dashboard') }}">
            {{ $brand['short'] }}
        </a>

        <div class="relative hidden max-w-xl flex-1 md:block">
            <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-copy-muted">search</span>
            <input class="form-field pl-12" type="text" placeholder="{{ $searchPlaceholder }}">
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
        <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/8 bg-white/5 text-copy-muted">
            <span class="material-symbols-outlined text-[20px]">notifications</span>
        </button>
        <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/8 bg-white/5 text-copy-muted">
            <span class="material-symbols-outlined text-[20px]">help</span>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="flex h-10 w-10 items-center justify-center rounded-full border border-white/8 bg-white/5 text-copy-muted" type="submit" title="Sign out">
                <span class="material-symbols-outlined text-[20px]">logout</span>
            </button>
        </form>
        <img class="avatar avatar-sm" src="{{ asset($profile['avatar']) }}" alt="{{ $profile['name'] }}">
    </div>
</header>
