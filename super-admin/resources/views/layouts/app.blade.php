<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SoundRedeem · Super Admin')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background text-on-background">
    <div class="scroll-progress"></div>

    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <div id="sidebar-backdrop" class="hidden fixed inset-0 z-30 bg-black/60 lg:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0">
            @include('partials.topbar')

            <main class="flex-1 px-container-margin py-lg max-w-[1400px] w-full mx-auto">
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>
</body>
</html>
