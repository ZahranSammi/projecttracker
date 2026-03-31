@extends('layouts.glacier-guest')

@section('content')
    <div class="hero-grid w-full items-center">
        <section class="max-w-xl">
            <span class="chip chip-tertiary mb-5">Workspace Setup</span>
            <h1 class="text-balance text-4xl font-semibold tracking-tight text-white lg:text-6xl">{{ $pageHeading }}</h1>
            <p class="mt-5 max-w-lg text-lg leading-8 text-copy-muted">{{ $pageCopy }}</p>

            <div class="mt-10 grid gap-4 sm:grid-cols-2">
                <div class="glass-panel overflow-hidden p-5">
                    <p class="text-sm font-semibold text-white">Demo-ready navigation</p>
                    <p class="mt-2 text-sm leading-6 text-copy-muted">Auth, dashboard, issues, projects, and kanban routes are all stitched together for presentation and review.</p>
                </div>
                <div class="glass-panel overflow-hidden p-5">
                    <p class="text-sm font-semibold text-white">Local visual system</p>
                    <p class="mt-2 text-sm leading-6 text-copy-muted">All theme tokens and key illustration assets are now served from the Laravel app instead of the raw export.</p>
                </div>
            </div>
        </section>

        <section>
            <div class="glass-panel-elevated mx-auto max-w-xl p-8">
                <div class="mb-6">
                    <p class="text-lg font-semibold text-white">Create account</p>
                    <p class="mt-2 text-sm text-copy-muted">Create a real Laravel-backed workspace and sign in immediately.</p>
                </div>

                <form class="grid gap-5 md:grid-cols-2" method="POST" action="{{ route('register.store') }}">
                    @csrf
                    <label class="block md:col-span-2">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Full Name</span>
                        <input class="form-field" name="name" type="text" placeholder="Zahra Tan" value="{{ old('name') }}">
                    </label>

                    <label class="block md:col-span-2">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Work Email</span>
                        <input class="form-field" name="email" type="email" placeholder="name@company.com" value="{{ old('email') }}">
                    </label>

                    <label class="block md:col-span-1">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Password</span>
                        <div class="relative">
                            <input class="form-field pr-16" id="register-password" name="password" type="password" value="preview-demo">
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-copy-muted" type="button" data-password-toggle="register-password" aria-pressed="false">Show</button>
                        </div>
                    </label>

                    <label class="block md:col-span-1">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Confirm Password</span>
                        <div class="relative">
                            <input class="form-field pr-16" id="register-password-confirm" name="password_confirmation" type="password" value="preview-demo">
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-medium text-copy-muted" type="button" data-password-toggle="register-password-confirm" aria-pressed="false">Show</button>
                        </div>
                    </label>

                    <label class="block md:col-span-2">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Workspace Name</span>
                        <input class="form-field" name="workspace_name" type="text" placeholder="Glacier Studio" value="{{ old('workspace_name') }}">
                    </label>

                    <div class="md:col-span-2 flex flex-col gap-3 pt-2 sm:flex-row">
                        <button class="btn-primary flex-1" type="submit">
                            Create Workspace
                            <span class="material-symbols-outlined text-base">north_east</span>
                        </button>
                        <a class="btn-secondary flex-1" href="{{ route('login') }}">Back to Sign In</a>
                    </div>
                </form>

                <div class="mt-8 glass-panel grid gap-4 rounded-3xl p-4 md:grid-cols-[auto_1fr] md:items-center">
                    <img class="avatar avatar-lg" src="{{ asset($heroImage) }}" alt="Workspace mark">
                    <div>
                        <p class="text-sm font-semibold text-white">What you get in this preview</p>
                        <p class="mt-2 text-sm leading-6 text-copy-muted">A real Laravel-backed workspace with seeded statuses, labels, and a polished Glacier shell ready for project and issue tracking.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
