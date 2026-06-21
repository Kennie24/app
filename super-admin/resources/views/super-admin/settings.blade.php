@extends('layouts.app')

@section('title', 'Settings · Super Admin')
@section('breadcrumb', 'Settings')

@section('content')
    @include('partials.page-header', [
        'eyebrow'  => 'Super Admin',
        'title'    => 'Platform Settings',
        'subtitle' => 'General, branding, integrations, API keys, and dangerous operations.',
    ])

    {{-- Side nav anchors --}}
    <div class="grid grid-cols-1 xl:grid-cols-[200px_1fr] gap-lg">
        <aside class="reveal hidden xl:block">
            <nav class="sticky top-20 space-y-xs text-label-md uppercase tracking-widest">
                @foreach ([
                    'general'      => ['icon' => 'tune',           'label' => 'General'],
                    'branding'     => ['icon' => 'palette',        'label' => 'Branding'],
                    'integrations' => ['icon' => 'extension',      'label' => 'Integrations'],
                    'api-keys'     => ['icon' => 'vpn_key',        'label' => 'API Keys'],
                    'security'     => ['icon' => 'shield',         'label' => 'Security'],
                    'danger'       => ['icon' => 'warning',        'label' => 'Danger Zone'],
                ] as $anchor => $item)
                    <a href="#{{ $anchor }}" class="flex items-center gap-sm px-md py-sm rounded-lg text-secondary hover:text-on-surface hover:bg-surface-container transition">
                        <span class="material-symbols-outlined text-[18px]">{{ $item['icon'] }}</span>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="space-y-lg">
            {{-- General --}}
            <section id="general" class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-primary">tune</span>
                    <h2 class="text-headline-md font-bold">General</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Platform Name</span>
                        <input type="text" value="SoundRedeem" class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Support Email</span>
                        <input type="email" value="support@soundredeem.io" class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Default Currency</span>
                        <select class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary">
                            <option>USD</option><option>EUR</option><option>GBP</option><option>JPY</option>
                        </select>
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Time Zone</span>
                        <select class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary">
                            <option>UTC</option><option>America/New_York</option><option>America/Los_Angeles</option><option>Europe/London</option>
                        </select>
                    </label>
                </div>
            </section>

            {{-- Branding --}}
            <section id="branding" class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-primary">palette</span>
                    <h2 class="text-headline-md font-bold">Branding</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-md">
                    <div class="md:col-span-2 flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Theme</span>
                        <div class="bg-surface-container-high rounded-lg p-1 flex gap-1 w-fit">
                            <button class="px-md py-sm rounded-md bg-primary-container text-on-primary-container text-label-md uppercase tracking-widest">Sonic Spotify</button>
                            <button class="px-md py-sm rounded-md text-secondary hover:text-on-surface text-label-md uppercase tracking-widest">Midnight</button>
                            <button class="px-md py-sm rounded-md text-secondary hover:text-on-surface text-label-md uppercase tracking-widest">High Contrast</button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Primary Color</span>
                        <div class="flex items-center gap-sm bg-surface-container-high rounded-lg px-md py-sm">
                            <span class="w-6 h-6 rounded-full bg-primary border border-outline-variant"></span>
                            <span class="text-body-md text-on-surface font-mono">#53E076</span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Integrations --}}
            <section id="integrations" class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-primary">extension</span>
                    <h2 class="text-headline-md font-bold">Integrations</h2>
                </div>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    @foreach ($integrations as $int)
                        <li class="flex items-center gap-md p-md rounded-xl bg-surface-container border border-outline-variant/10">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary">{{ $int['icon'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-body-md font-bold text-on-surface">{{ $int['name'] }}</p>
                                <p class="text-label-sm text-secondary truncate">{{ $int['desc'] }}</p>
                            </div>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" {{ $int['status'] ? 'checked' : '' }}>
                                <span class="w-11 h-6 rounded-full bg-surface-container-high peer-checked:bg-primary-container relative transition">
                                    <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-background peer-checked:translate-x-5 transition"></span>
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- API Keys --}}
            <section id="api-keys" class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
                <div class="flex items-center justify-between mb-md">
                    <div class="flex items-center gap-sm">
                        <span class="material-symbols-outlined text-primary">vpn_key</span>
                        <h2 class="text-headline-md font-bold">API Keys</h2>
                    </div>
                    <button class="flex items-center gap-xs px-md h-9 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                        <span class="material-symbols-outlined text-[18px]">add</span> New Key
                    </button>
                </div>
                <ul class="space-y-sm">
                    @foreach ($apiKeys as $k)
                        <li class="flex flex-col md:flex-row md:items-center gap-sm p-md rounded-xl bg-surface-container border border-outline-variant/10">
                            <div class="flex items-center gap-sm flex-1 min-w-0">
                                <span class="material-symbols-outlined text-secondary">key</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-body-md font-bold text-on-surface">{{ $k['label'] }}</p>
                                    <p class="text-label-sm text-secondary font-mono truncate">{{ $k['masked'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-sm">
                                <span class="text-label-sm text-secondary uppercase tracking-widest">{{ $k['scopes'] }}</span>
                                <span class="text-label-sm text-secondary">{{ $k['created'] }}</span>
                                <div class="flex gap-xs">
                                    <button title="Rotate" class="w-8 h-8 rounded-full bg-surface-container-high hover:bg-surface-container-highest text-secondary hover:text-primary transition">
                                        <span class="material-symbols-outlined text-[18px] align-middle">refresh</span>
                                    </button>
                                    <button title="Revoke" class="w-8 h-8 rounded-full bg-surface-container-high hover:bg-error-container/40 text-secondary hover:text-error transition">
                                        <span class="material-symbols-outlined text-[18px] align-middle">delete</span>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Security --}}
            <section id="security" class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-primary">shield</span>
                    <h2 class="text-headline-md font-bold">Security</h2>
                </div>
                <ul class="space-y-sm">
                    @foreach ([
                        ['Enforce 2FA',                 'Require all admins to set up TOTP',          true],
                        ['Session timeout · 30 min',    'Auto sign-out inactive admin sessions',      true],
                        ['IP allow-list',               'Restrict sign-in to corporate IP ranges',    false],
                        ['Webhook signature verification','Reject unsigned webhook deliveries',       true],
                    ] as $row)
                        <li class="flex items-center justify-between p-md rounded-xl bg-surface-container border border-outline-variant/10">
                            <div class="min-w-0 mr-md">
                                <p class="text-body-md font-bold text-on-surface">{{ $row[0] }}</p>
                                <p class="text-label-sm text-secondary">{{ $row[1] }}</p>
                            </div>
                            <label class="inline-flex items-center cursor-pointer shrink-0">
                                <input type="checkbox" class="sr-only peer" {{ $row[2] ? 'checked' : '' }}>
                                <span class="w-11 h-6 rounded-full bg-surface-container-high peer-checked:bg-primary-container relative transition">
                                    <span class="absolute top-0.5 left-0.5 w-5 h-5 rounded-full bg-background peer-checked:translate-x-5 transition"></span>
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Danger zone --}}
            <section id="danger" class="reveal bento-card rounded-xl border border-error/40 p-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-error">warning</span>
                    <h2 class="text-headline-md font-bold text-error">Danger Zone</h2>
                </div>
                <p class="text-body-md text-secondary mb-md max-w-prose">
                    These actions are permanent. They will be recorded in the audit log and require an additional confirmation step.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-md">
                    <button class="flex flex-col items-start gap-xs p-md rounded-xl bg-error-container/20 border border-error/30 text-error hover:bg-error-container/30 transition">
                        <span class="material-symbols-outlined">delete_forever</span>
                        <span class="font-bold">Purge sandbox data</span>
                        <span class="text-label-sm opacity-80">Wipe all test redemptions</span>
                    </button>
                    <button class="flex flex-col items-start gap-xs p-md rounded-xl bg-error-container/20 border border-error/30 text-error hover:bg-error-container/30 transition">
                        <span class="material-symbols-outlined">key_off</span>
                        <span class="font-bold">Revoke all sessions</span>
                        <span class="text-label-sm opacity-80">Sign out every admin globally</span>
                    </button>
                    <button class="flex flex-col items-start gap-xs p-md rounded-xl bg-error-container/20 border border-error/30 text-error hover:bg-error-container/30 transition">
                        <span class="material-symbols-outlined">power_settings_new</span>
                        <span class="font-bold">Enable maintenance mode</span>
                        <span class="text-label-sm opacity-80">Block all user traffic</span>
                    </button>
                </div>
            </section>

            {{-- Save bar --}}
            <div class="reveal sticky bottom-lg flex justify-end gap-sm p-md rounded-2xl bg-background/70 backdrop-blur-xl border border-outline-variant/30">
                <button class="px-md h-10 rounded-full text-secondary hover:text-on-surface text-label-md uppercase tracking-widest">Discard</button>
                <button class="flex items-center gap-xs px-md h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                    <span class="material-symbols-outlined text-[18px]">check</span> Save Changes
                </button>
            </div>
        </div>
    </div>
@endsection
