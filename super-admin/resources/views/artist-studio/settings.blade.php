@extends('layouts.artist')

@section('title', 'Settings · Artist Studio')
@section('page-title', 'Account settings')
@section('page-subtitle', 'Manage your artist profile, sign-in security, and workspace identity.')

@php
    $initials = collect(explode(' ', trim($user->name ?? '')))
        ->filter()
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

@push('styles')
<style>
    /* Settings-specific micro-interactions */
    .settings-section {
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
                    box-shadow 0.35s cubic-bezier(0.22, 1, 0.36, 1),
                    border-color 0.35s ease;
    }
    .settings-section:hover {
        transform: translateY(-2px);
        border-color: rgb(var(--md-sys-color-primary, 83 224 118) / 0.4);
        box-shadow: 0 18px 40px -22px rgba(29, 185, 84, 0.45);
    }
    @media (max-width: 640px) {
        .settings-section:hover { transform: none; box-shadow: none; }
    }

    .field-input {
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }
    .field-input:focus {
        box-shadow: 0 0 0 3px rgb(29 185 84 / 0.18);
    }

    .pill-link {
        transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }
    .pill-link:hover { transform: translateY(-1px); }
    .pill-link:active { transform: translateY(0); }

    .cta-button {
        transition: transform 0.18s ease, filter 0.2s ease, box-shadow 0.25s ease;
    }
    .cta-button:hover { transform: translateY(-1px); box-shadow: 0 12px 30px -14px rgba(29, 185, 84, 0.55); }
    .cta-button:active { transform: translateY(0) scale(0.98); }

    /* Avatar zoom on hover, replace-overlay */
    .avatar-wrap { transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1); }
    .avatar-wrap:hover { transform: scale(1.04); }
    .avatar-overlay {
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .avatar-wrap:hover .avatar-overlay { opacity: 1; }

    /* Stagger inputs inside reveal cards */
    .stagger > * { opacity: 0; transform: translateY(10px); transition: opacity 0.5s ease, transform 0.5s ease; }
    .is-visible .stagger > * { opacity: 1; transform: translateY(0); }
    .is-visible .stagger > *:nth-child(1) { transition-delay: 80ms; }
    .is-visible .stagger > *:nth-child(2) { transition-delay: 140ms; }
    .is-visible .stagger > *:nth-child(3) { transition-delay: 200ms; }
    .is-visible .stagger > *:nth-child(4) { transition-delay: 260ms; }
    .is-visible .stagger > *:nth-child(5) { transition-delay: 320ms; }

    @media (prefers-reduced-motion: reduce) {
        .settings-section, .pill-link, .cta-button, .avatar-wrap, .stagger > * { transition: none !important; }
        .settings-section:hover, .pill-link:hover, .cta-button:hover, .avatar-wrap:hover { transform: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="mx-auto w-full min-w-0 max-w-3xl space-y-md md:space-y-lg">

        {{-- Quick jump nav (horizontal scroll on mobile) --}}
        <nav class="reveal -mx-gutter overflow-x-auto px-gutter md:mx-0 md:overflow-visible md:px-0" style="scrollbar-width:none;">
            <div class="inline-flex w-max items-center gap-xs whitespace-nowrap rounded-full border border-outline-variant/20 bg-surface-container-low p-xs">
                <a href="#profile"
                   class="pill-link inline-flex items-center gap-sm rounded-full px-md py-sm text-label-md text-on-surface hover:bg-primary-container/20 hover:text-primary">
                    <span class="material-symbols-outlined text-[20px]">person</span> Profile
                </a>
                <a href="#security"
                   class="pill-link inline-flex items-center gap-sm rounded-full px-md py-sm text-label-md text-on-surface hover:bg-primary-container/20 hover:text-primary">
                    <span class="material-symbols-outlined text-[20px]">lock</span> Password
                </a>
                <a href="#session"
                   class="pill-link inline-flex items-center gap-sm rounded-full px-md py-sm text-label-md text-on-surface hover:bg-primary-container/20 hover:text-primary">
                    <span class="material-symbols-outlined text-[20px]">logout</span> Session
                </a>
            </div>
        </nav>

        {{-- Validation summary --}}
        @if ($errors->any())
            <div class="reveal flex items-start gap-sm rounded-xl border border-error/40 bg-error-container/20 p-md text-error" data-direction="scale">
                <span class="material-symbols-outlined">error</span>
                <div>
                    <p class="font-bold text-body-md">There were a few problems with your changes.</p>
                    <ul class="mt-xs list-disc pl-lg text-body-sm">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Profile --}}
        <section id="profile"
                 class="reveal settings-section min-w-0 scroll-mt-24 overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low p-md sm:p-lg md:p-xl"
                 style="--reveal-delay: 80ms;">
            <header class="mb-lg">
                <p class="text-label-md uppercase tracking-widest text-primary">Profile</p>
                <h2 class="mt-xs font-headline-md text-headline-md font-bold leading-tight">Your public artist identity</h2>
                <p class="mt-xs text-body-md text-secondary">Shown to fans on every release page and across the SoundRedeem catalog.</p>
            </header>

            <form action="{{ route('artist-studio.settings.profile') }}" method="POST" enctype="multipart/form-data" class="stagger space-y-lg">
                @csrf

                {{-- Avatar row --}}
                <div class="flex flex-col items-center gap-md text-center sm:flex-row sm:items-center sm:text-left">
                    <div class="avatar-wrap relative h-20 w-20 shrink-0 overflow-hidden rounded-full border border-outline-variant/30 bg-surface-container-high sm:h-24 sm:w-24">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center font-headline-md text-headline-md font-extrabold text-primary">
                                {{ $initials ?: '🎵' }}
                            </div>
                        @endif
                        <div class="avatar-overlay absolute inset-0 flex items-center justify-center bg-black/55 text-white">
                            <span class="material-symbols-outlined text-[28px]">photo_camera</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0 space-y-xs">
                        <label for="avatar-input" class="block text-label-md uppercase tracking-widest text-secondary">Avatar</label>
                        <input id="avatar-input" type="file" name="avatar" accept="image/png,image/jpeg,image/webp"
                               class="field-input block w-full min-w-0 max-w-full rounded-lg border border-outline-variant/30 bg-surface-container-high p-sm text-body-sm text-on-surface file:mr-md file:cursor-pointer file:rounded-md file:border-0 file:bg-primary-container file:px-md file:py-xs file:font-bold file:text-on-primary-container hover:file:brightness-110">
                        <p class="text-label-sm text-secondary">PNG, JPG, or WebP — up to 5 MB.</p>
                        @if ($user->avatar_path)
                            <label class="inline-flex items-center gap-sm pt-xs text-label-sm text-secondary">
                                <input type="checkbox" name="remove_avatar" value="1" class="h-4 w-4 accent-primary">
                                Remove current avatar
                            </label>
                        @endif
                    </div>
                </div>

                {{-- Name fields --}}
                <div class="grid grid-cols-1 gap-md md:grid-cols-2">
                    <label class="block space-y-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Display name <span class="text-error">*</span></span>
                        <input type="text" name="name" required maxlength="255"
                               value="{{ old('name', $user->name) }}"
                               class="field-input w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-md py-sm text-body-md text-on-surface outline-none focus:border-primary">
                    </label>

                    <label class="block space-y-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Artist / stage name</span>
                        <input type="text" name="artist_name" maxlength="255"
                               value="{{ old('artist_name', $user->artist_name) }}"
                               placeholder="{{ $user->name }}"
                               class="field-input w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-md py-sm text-body-md text-on-surface outline-none focus:border-primary">
                    </label>
                </div>

                <label class="block space-y-xs">
                    <span class="text-label-md uppercase tracking-widest text-secondary">Email <span class="text-error">*</span></span>
                    <input type="email" name="email" required maxlength="255"
                           value="{{ old('email', $user->email) }}"
                           class="field-input w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-md py-sm text-body-md text-on-surface outline-none focus:border-primary">
                    <span class="block text-label-sm text-secondary">Used for sign-in and payout notifications.</span>
                </label>

                <div class="flex flex-col-reverse gap-sm border-t border-outline-variant/20 pt-md sm:flex-row sm:items-center sm:justify-end">
                    <button type="submit"
                            class="cta-button inline-flex w-full items-center justify-center gap-sm rounded-full bg-primary-container px-lg py-sm font-bold uppercase tracking-widest text-on-primary-container sm:w-auto">
                        <span class="material-symbols-outlined text-[20px]">save</span> Save profile
                    </button>
                </div>
            </form>
        </section>

        {{-- Password --}}
        <section id="security"
                 class="reveal settings-section min-w-0 scroll-mt-24 overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low p-md sm:p-lg md:p-xl"
                 style="--reveal-delay: 160ms;">
            <header class="mb-lg">
                <p class="text-label-md uppercase tracking-widest text-primary">Security</p>
                <h2 class="mt-xs font-headline-md text-headline-md font-bold leading-tight">Change password</h2>
                <p class="mt-xs text-body-md text-secondary">Use at least 8 characters. Your session will stay active on this device after a successful change.</p>
            </header>

            <form action="{{ route('artist-studio.settings.password') }}" method="POST" class="stagger space-y-md">
                @csrf
                @method('PUT')

                <label class="block space-y-xs">
                    <span class="text-label-md uppercase tracking-widest text-secondary">Current password</span>
                    <input type="password" name="current_password" required autocomplete="current-password"
                           class="field-input w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-md py-sm text-body-md text-on-surface outline-none focus:border-primary">
                </label>

                <div class="grid grid-cols-1 gap-md md:grid-cols-2">
                    <label class="block space-y-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">New password</span>
                        <input type="password" name="password" required minlength="8" autocomplete="new-password"
                               class="field-input w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-md py-sm text-body-md text-on-surface outline-none focus:border-primary">
                    </label>

                    <label class="block space-y-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Confirm new password</span>
                        <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                               class="field-input w-full rounded-lg border border-outline-variant/30 bg-surface-container-high px-md py-sm text-body-md text-on-surface outline-none focus:border-primary">
                    </label>
                </div>

                <div class="flex flex-col-reverse gap-sm border-t border-outline-variant/20 pt-md sm:flex-row sm:items-center sm:justify-end">
                    <button type="submit"
                            class="cta-button inline-flex w-full items-center justify-center gap-sm rounded-full bg-primary-container px-lg py-sm font-bold uppercase tracking-widest text-on-primary-container sm:w-auto">
                        <span class="material-symbols-outlined text-[20px]">lock_reset</span> Update password
                    </button>
                </div>
            </form>
        </section>

        {{-- Session --}}
        <section id="session"
                 class="reveal settings-section min-w-0 scroll-mt-24 overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low p-md sm:p-lg md:p-xl"
                 style="--reveal-delay: 240ms;">
            <header class="mb-md">
                <p class="text-label-md uppercase tracking-widest text-primary">Session</p>
                <h2 class="mt-xs font-headline-md text-headline-md font-bold leading-tight">Sign out</h2>
                <p class="mt-xs text-body-md text-secondary">Ends your active Artist Studio session on this device.</p>
            </header>
            <form action="{{ route('artist-studio.logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="cta-button inline-flex w-full items-center justify-center gap-sm rounded-full border border-error/40 bg-error-container/10 px-lg py-sm font-bold uppercase tracking-widest text-error hover:bg-error-container/20 sm:w-auto">
                    <span class="material-symbols-outlined text-[20px]">logout</span> Sign out
                </button>
            </form>
        </section>
    </div>
@endsection
