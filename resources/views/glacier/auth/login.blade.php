@extends('layouts.glacier-guest')

@section('content')
    <div class="hero-grid w-full items-center">
        <section class="order-2 max-w-xl lg:order-1">
            <div class="mb-8 flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-primary/20 bg-primary/12">
                    <span class="material-symbols-outlined text-primary">ac_unit</span>
                </div>
                <div>
                    <p class="text-2xl font-semibold tracking-tight text-white">{{ $brand['short'] }}</p>
                    <p class="text-sm text-copy-muted">Issue tracking shaped like frozen light.</p>
                </div>
            </div>

            <span class="chip chip-primary mb-5">Frontend Demo</span>
            <h1 class="text-balance text-4xl font-semibold tracking-tight text-white lg:text-6xl">{{ $pageHeading }}</h1>
            <p class="mt-5 max-w-lg text-lg leading-8 text-copy-muted">{{ $pageCopy }}</p>

            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="metric-card">
                    <p class="text-3xl font-semibold text-white">9</p>
                    <p class="mt-2 text-sm text-copy-muted">Stitch screens now mapped into Laravel routes.</p>
                </div>
                <div class="metric-card">
                    <p class="text-3xl font-semibold text-white">13.2</p>
                    <p class="mt-2 text-sm text-copy-muted">Running on the latest Laravel 13 patch line.</p>
                </div>
                <div class="metric-card">
                    <p class="text-3xl font-semibold text-white">100%</p>
                    <p class="mt-2 text-sm text-copy-muted">Local theme tokens, assets, and glass styling.</p>
                </div>
            </div>
        </section>

        <section class="order-1 lg:order-2">
            <div class="glass-panel-elevated mx-auto max-w-md overflow-hidden p-8">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-lg font-semibold text-white">Sign in</p>
                        <p class="text-sm text-copy-muted">Use the Glacier preview workspace.</p>
                    </div>
                    <span class="chip chip-primary">Private Beta</span>
                </div>

                <form class="space-y-5" method="POST" action="{{ route('login.attempt') }}">
                    @csrf
                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Email Address</span>
                        <input class="form-field" name="email" type="email" placeholder="name@company.com" value="{{ old('email', 'zahra@glacier.app') }}">
                    </label>

                    <label class="block">
                        <span class="mb-2 flex items-center justify-between text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">
                            Password
                            <a class="text-primary normal-case tracking-normal" href="{{ route('password.request') }}">Forgot password?</a>
                        </span>
                        <div class="relative">
                            <input class="form-field pr-16" id="login-password" name="password" type="password" value="preview-demo">
                            <button
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-copy-muted"
                                type="button"
                                data-password-toggle="login-password"
                                aria-pressed="false"
                            >
                                Show
                            </button>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 text-sm text-copy-muted">
                        <input class="h-4 w-4 rounded border-outline bg-surface text-primary" name="remember" type="checkbox" value="1" checked>
                        Keep me signed in
                    </label>

                    <button class="btn-primary w-full" type="submit">
                        Enter Glacier
                        <span class="material-symbols-outlined text-base">login</span>
                    </button>
                </form>

                <div class="my-6 flex items-center gap-4">
                    <div class="h-px flex-1 bg-white/8"></div>
                    <span class="text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">or continue with</span>
                    <div class="h-px flex-1 bg-white/8"></div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <button class="btn-secondary">
                        <span class="material-symbols-outlined text-base">language</span>
                        Google
                    </button>
                    <button class="btn-secondary">
                        <span class="material-symbols-outlined text-base">hub</span>
                        GitHub
                    </button>
                </div>

                <div class="mt-8 glass-panel overflow-hidden rounded-3xl">
                    <img class="h-40 w-full object-cover" src="{{ asset($heroImage) }}" alt="Abstract glass texture">
                </div>

                <p class="mt-6 text-center text-sm text-copy-muted">
                    New here?
                    <a class="font-semibold text-primary" href="{{ route('register') }}">Create a workspace</a>
                </p>
            </div>
        </section>
    </div>
@endsection
