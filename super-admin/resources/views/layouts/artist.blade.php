<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SoundRedeem · Artist Studio')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .artist-shell { font-family: 'Manrope', 'Inter', system-ui, sans-serif; }
    </style>

    @stack('styles')
</head>
<body class="artist-shell min-h-screen overflow-x-hidden bg-background text-on-background flex">
    <div class="scroll-progress"></div>

    @include('partials.artist-sidebar')

    <div id="sidebar-backdrop" class="hidden fixed inset-0 z-30 bg-black/60 lg:hidden"></div>

    <main class="lg:ml-64 flex-1 min-w-0 flex flex-col min-h-screen bg-background">
        @include('partials.artist-topbar')

        <div class="p-gutter md:p-lg space-y-lg max-w-[1400px] w-full min-w-0 mx-auto">
            @if (session('flash'))
                <div class="reveal flex items-center gap-sm rounded-xl border border-primary/30 bg-primary/10 p-md text-primary">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span class="text-body-md font-bold">{{ session('flash') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
