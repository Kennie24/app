<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Artist Sign In · SoundRedeem</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-background text-on-background relative overflow-hidden"
      style="font-family: 'Manrope', system-ui, sans-serif;">

    <div class="pointer-events-none fixed inset-0">
        <div class="absolute -left-32 top-1/4 h-[520px] w-[520px] rounded-full bg-primary/10 blur-[140px]"></div>
        <div class="absolute -bottom-48 right-0 h-[460px] w-[460px] rounded-full bg-primary-container/10 blur-[120px]"></div>
    </div>

    <header class="relative z-10 px-container-margin h-16 flex items-center justify-between border-b border-outline-variant/15">
        <div class="flex items-center gap-sm">
            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
                <span class="material-symbols-outlined filled">graphic_eq</span>
            </span>
            <div class="leading-tight">
                <p class="font-headline-md text-headline-md font-bold text-primary tracking-tight">SoundRedeem</p>
                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant">For Artists</p>
            </div>
        </div>
        <a href="{{ url('/') }}" class="text-on-surface-variant hover:text-primary text-label-md uppercase tracking-widest">
            Admin login
        </a>
    </header>

    <main class="relative z-10 mx-auto grid min-h-[calc(100vh-4rem)] max-w-6xl grid-cols-1 items-center gap-lg px-container-margin py-lg lg:grid-cols-2">
        <section class="hidden lg:block">
            <span class="text-label-lg uppercase tracking-[0.24em] text-primary">Artist workspace</span>
            <h1 class="mt-md font-headline-lg text-[56px] leading-[1.05] font-extrabold tracking-[-0.03em]">
                Your music.<br>Your audience.<br><span class="text-primary">Your control.</span>
            </h1>
            <p class="mt-lg max-w-[32rem] font-body-lg text-body-lg text-on-surface-variant">
                Upload releases, publish 30-second previews, track redemptions, and manage your earnings from one workspace.
            </p>
            <div class="mt-lg grid max-w-[32rem] grid-cols-3 gap-gutter">
                @foreach ([
                    ['audio_file',  'Upload',  'Singles & albums'],
                    ['play_circle', 'Preview', '30-second samples'],
                    ['monitoring',  'Analyze', 'Real-time insights'],
                ] as [$icon, $title, $sub])
                    <div class="rounded-xl border border-outline-variant/15 bg-surface-container-low p-md">
                        <span class="material-symbols-outlined text-primary mb-sm">{{ $icon }}</span>
                        <p class="font-body-md font-bold">{{ $title }}</p>
                        <p class="text-label-sm text-on-surface-variant mt-xs">{{ $sub }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="mx-auto w-full max-w-[28rem] rounded-2xl border border-outline-variant/20 bg-surface-container p-lg shadow-2xl">
            <div class="mb-lg">
                <div class="mb-md flex h-12 w-12 items-center justify-center rounded-full bg-primary/15 text-primary">
                    <span class="material-symbols-outlined filled">person</span>
                </div>
                <h2 class="font-headline-lg text-headline-lg font-extrabold">Artist sign in</h2>
                <p class="mt-xs font-body-md text-body-md text-on-surface-variant">
                    Welcome back. Continue to your artist workspace.
                </p>
            </div>

            <form method="POST" action="{{ route('artist-studio.login.submit') }}" class="space-y-md">
                @csrf

                <label class="block space-y-xs">
                    <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Email address</span>
                    <div class="flex h-14 items-center gap-sm rounded-xl border border-transparent bg-surface-container-high px-md transition-all focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
                        <span class="material-symbols-outlined text-on-surface-variant text-[20px]">mail</span>
                        <input required autofocus type="email" name="email" autocomplete="email"
                               value="{{ old('email') }}"
                               placeholder="artist@example.com"
                               class="min-w-0 flex-1 bg-transparent text-body-lg outline-none placeholder:text-outline">
                    </div>
                </label>

                <label class="block space-y-xs">
                    <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Password</span>
                    <div class="flex h-14 items-center gap-sm rounded-xl border border-transparent bg-surface-container-high px-md transition-all focus-within:border-primary focus-within:ring-1 focus-within:ring-primary">
                        <span class="material-symbols-outlined text-on-surface-variant text-[20px]">lock</span>
                        <input required type="password" name="password" autocomplete="current-password" minlength="8"
                               placeholder="Enter your password"
                               class="min-w-0 flex-1 bg-transparent text-body-lg outline-none placeholder:text-outline">
                    </div>
                </label>

                <div class="flex items-center justify-between py-xs">
                    <label class="flex cursor-pointer items-center gap-sm text-body-md text-on-surface-variant">
                        <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-outline bg-transparent text-primary focus:ring-primary">
                        Remember me
                    </label>
                    <a href="#" class="text-label-lg text-primary hover:underline">Forgot password?</a>
                </div>

                @if ($errors->any())
                    <div role="alert" class="flex items-start gap-sm rounded-xl border border-error/30 bg-error-container/20 p-md text-error">
                        <span class="material-symbols-outlined">error</span>
                        <span class="text-body-md">{{ $errors->first() }}</span>
                    </div>
                @endif

                <button type="submit"
                        class="flex w-full items-center justify-center gap-sm rounded-xl bg-primary-container text-on-primary-container py-md font-bold text-label-lg uppercase tracking-widest shadow-lg hover:brightness-105 active:scale-[0.98] transition-all">
                    Sign in to workspace
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>

            <p class="mt-lg text-center font-body-md text-body-md text-on-surface-variant">
                Not an artist yet?
                <a href="#" class="font-bold text-primary hover:underline">Apply for artist access</a>
            </p>
        </section>
    </main>
</body>
</html>
