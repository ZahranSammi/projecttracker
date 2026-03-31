<!DOCTYPE html>
<html class="dark" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $pageTitle ?? 'Glacier' }} | {{ $brand['short'] }}</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="glacier-auth-shell">
            <div class="glacier-orb -right-24 top-0 h-80 w-80 bg-primary/20"></div>
            <div class="glacier-orb -bottom-10 -left-12 h-72 w-72 bg-tertiary/10"></div>

            <div class="mx-auto flex min-h-[calc(100vh-5rem)] w-full max-w-6xl items-center">
                <div class="w-full">
                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-primary/20 bg-primary/10 px-4 py-3 text-sm text-primary">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-danger/20 bg-danger/10 px-4 py-3 text-sm text-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </body>
</html>
