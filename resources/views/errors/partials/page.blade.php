@php
    $brand = [
        'short' => 'Glacier',
        'tagline' => 'Issue tracking shaped like frozen light.',
    ];

    $statusCode = (string) ($statusCode ?? '500');
    $pageTitle = $pageTitle ?? "Error {$statusCode}";
    $eyebrow = $eyebrow ?? 'System Status';
    $heading = $heading ?? 'Something interrupted the flow.';
    $message = $message ?? 'The page could not be loaded right now. Try heading back to the workspace shell.';
    $supportingCopy = $supportingCopy ?? 'If the problem keeps happening, check the route, refresh the page, or return to the dashboard to continue working.';
    $primaryHref = $primaryHref ?? (auth()->check() ? route('dashboard') : route('login'));
    $primaryLabel = $primaryLabel ?? (auth()->check() ? 'Back to Dashboard' : 'Go to Login');
    $secondaryHref = $secondaryHref ?? url()->previous();
    $secondaryLabel = $secondaryLabel ?? 'Go Back';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle }} | {{ $brand['short'] }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
        @include('partials.theme-init')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="glacier-auth-shell">
            <div class="glacier-orb -right-24 top-0 h-80 w-80 bg-primary/20"></div>
            <div class="glacier-orb -bottom-10 -left-12 h-72 w-72 bg-tertiary/10"></div>
            <div class="mb-6 flex justify-end">
                @include('partials.theme-toggle')
            </div>

            <div class="mx-auto flex min-h-[calc(100vh-5rem)] w-full max-w-6xl items-center">
                <div class="hero-grid w-full items-center">
                    <section class="order-2 max-w-2xl lg:order-1">
                        <div class="mb-8 flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-primary/20 bg-primary/12">
                                <span class="material-symbols-outlined text-primary">ac_unit</span>
                            </div>
                            <div>
                                <p class="text-2xl font-semibold tracking-tight text-white">{{ $brand['short'] }}</p>
                                <p class="text-sm text-copy-muted">{{ $brand['tagline'] }}</p>
                            </div>
                        </div>

                        <span class="chip chip-primary mb-5">{{ $eyebrow }}</span>
                        <h1 class="text-balance text-4xl font-semibold tracking-tight text-white lg:text-6xl">{{ $heading }}</h1>
                        <p class="mt-5 max-w-xl text-lg leading-8 text-copy-muted">{{ $message }}</p>
                        <p class="mt-4 max-w-xl text-sm leading-7 text-copy-muted">{{ $supportingCopy }}</p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a class="btn-primary" href="{{ $primaryHref }}">
                                {{ $primaryLabel }}
                                <span class="material-symbols-outlined text-base">north_east</span>
                            </a>
                            <a class="btn-secondary" href="{{ $secondaryHref }}">
                                {{ $secondaryLabel }}
                                <span class="material-symbols-outlined text-base">undo</span>
                            </a>
                        </div>
                    </section>

                    <section class="order-1 lg:order-2">
                        <div class="glass-panel-elevated mx-auto max-w-md overflow-hidden p-8">
                            <div class="flex items-start justify-between gap-6">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-copy-muted">Error Code</p>
                                    <p class="mt-3 text-7xl font-semibold leading-none text-gradient">{{ $statusCode }}</p>
                                </div>
                                <span class="chip chip-danger">Attention</span>
                            </div>

                            <div class="mt-8 space-y-4">
                                <div class="surface-subtle rounded-2xl p-4">
                                    <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">Status</p>
                                    <p class="mt-2 text-lg font-semibold text-white">{{ $heading }}</p>
                                </div>
                                <div class="surface-subtle rounded-2xl p-4">
                                    <p class="text-xs uppercase tracking-[0.24em] text-copy-muted">What to do next</p>
                                    <p class="mt-2 text-sm leading-6 text-copy-muted">{{ $supportingCopy }}</p>
                                </div>
                            </div>

                            <div class="mt-8 rounded-3xl border border-primary/12 bg-primary/8 p-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-primary/20 bg-primary/10">
                                        <span class="material-symbols-outlined text-primary">stacked_email</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white">Glacier Recovery Path</p>
                                        <p class="text-sm text-copy-muted">Use the buttons to jump back into a stable route.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </body>
</html>
