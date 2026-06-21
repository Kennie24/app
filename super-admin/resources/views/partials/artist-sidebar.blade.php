@php
    $route = Route::currentRouteName() ?? '';
    $user = auth()->user();
    $nav = [
        ['label' => 'Dashboard', 'icon' => 'dashboard',   'route' => 'artist-studio.dashboard'],
        ['label' => 'Releases',  'icon' => 'album',       'route' => 'artist-studio.releases.index', 'match' => 'artist-studio.releases.'],
        ['label' => 'Analytics', 'icon' => 'leaderboard', 'route' => 'artist-studio.analytics'],
        ['label' => 'Payments',  'icon' => 'payments',    'route' => 'artist-studio.payments'],
        ['label' => 'Settings',  'icon' => 'settings',    'route' => 'artist-studio.settings'],
    ];
@endphp

<aside id="sidebar"
       class="fixed lg:fixed top-0 left-0 z-40 h-screen w-64 shrink-0
              bg-surface-container-lowest border-r border-outline-variant/20
              -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out
              flex flex-col py-lg">

    <div class="px-md mb-xl flex items-center gap-sm">
        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
            <span class="material-symbols-outlined filled">graphic_eq</span>
        </span>
        <div class="leading-tight">
            <h1 class="font-headline-md text-headline-md font-bold text-primary tracking-tight">SoundRedeem</h1>
            <p class="text-[10px] uppercase tracking-widest text-on-surface-variant">Artist Studio</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto">
        @foreach ($nav as $item)
            @php
                $match = $item['match'] ?? null;
                $active = $route === $item['route'] || ($match && str_starts_with($route, $match));
            @endphp
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 mx-2 px-4 py-3 rounded-lg transition-all duration-150 active:translate-x-1
                      {{ $active
                            ? 'bg-primary-container text-on-primary-container font-bold'
                            : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                <span class="material-symbols-outlined {{ $active ? 'filled' : '' }}">{{ $item['icon'] }}</span>
                <span class="font-label-lg text-label-lg">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-md mt-auto pt-lg border-t border-outline-variant/30 space-y-md">
        <a href="{{ route('artist-studio.releases.create') }}"
           class="block text-center w-full bg-primary-container text-on-primary-container font-bold text-label-lg py-3 rounded-xl shadow-sm hover:brightness-110 active:scale-95 transition-all">
            <span class="material-symbols-outlined align-middle text-[18px]">add</span>
            Create Release
        </a>

        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary font-bold uppercase border border-outline-variant/30 shrink-0">
                {{ collect(explode(' ', $user->artist_name ?: $user->name))->map(fn ($p) => $p[0] ?? '')->take(2)->join('') }}
            </div>
            <div class="overflow-hidden min-w-0">
                <p class="font-label-lg text-label-lg truncate text-on-surface">{{ $user->artist_name ?: $user->name }}</p>
                <p class="text-[10px] text-on-surface-variant truncate uppercase tracking-wider font-bold">Artist account</p>
            </div>
        </div>

        <form method="POST" action="{{ route('artist-studio.logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-md py-2 rounded-lg text-on-surface-variant hover:text-error hover:bg-surface-container transition-colors text-label-sm font-bold uppercase tracking-widest">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Sign out
            </button>
        </form>
    </div>
</aside>
