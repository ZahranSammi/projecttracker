@extends('layouts.glacier-guest')

@section('content')
    <div class="hero-grid w-full items-center">
        <section class="max-w-xl">
            <span class="chip chip-primary mb-5">Account Recovery</span>
            <h1 class="text-balance text-4xl font-semibold tracking-tight text-white lg:text-6xl">{{ $pageHeading }}</h1>
            <p class="mt-5 max-w-lg text-lg leading-8 text-copy-muted">{{ $pageCopy }}</p>

            <div class="glass-panel mt-10 overflow-hidden p-4">
                <img class="h-56 w-full rounded-[1.25rem] object-cover" src="{{ asset($heroImage) }}" alt="Abstract system map">
            </div>
        </section>

        <section>
            <div class="glass-panel-elevated mx-auto max-w-md p-8">
                <div class="mb-6">
                    <p class="text-lg font-semibold text-white">Forgot Password</p>
                    <p class="mt-2 text-sm text-copy-muted">Enter the email you used for Glacier and we will send a reset link.</p>
                </div>

                <form class="space-y-5" method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <label class="block">
                        <span class="mb-2 block text-xs font-semibold uppercase tracking-[0.24em] text-copy-muted">Work Email</span>
                        <input class="form-field" name="email" type="email" placeholder="zahra@glacier.app" value="{{ old('email', 'zahra@glacier.app') }}">
                    </label>

                    <button class="btn-primary w-full" type="submit">
                        Send Recovery Link
                        <span class="material-symbols-outlined text-base">mail</span>
                    </button>
                </form>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-sm font-semibold text-white">Fast handoff</p>
                        <p class="mt-2 text-sm text-copy-muted">Laravel will generate a reset link and log it through the configured mailer for local review.</p>
                    </div>
                    <div class="glass-panel rounded-2xl p-4">
                        <p class="text-sm font-semibold text-white">Real backend flow</p>
                        <p class="mt-2 text-sm text-copy-muted">The reset request now goes through Laravel instead of a static preview handoff.</p>
                    </div>
                </div>

                <p class="mt-6 text-center text-sm text-copy-muted">
                    Remembered your password?
                    <a class="font-semibold text-primary" href="{{ route('login') }}">Return to sign in</a>
                </p>
            </div>
        </section>
    </div>
@endsection
