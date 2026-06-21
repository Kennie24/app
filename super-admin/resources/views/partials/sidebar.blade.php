@php
    $route = Route::currentRouteName() ?? '';
    $nav = [
        ['label' => 'Overview',      'icon' => 'dashboard',          'route' => 'super-admin.dashboard'],
        ['label' => 'Redemptions',   'icon' => 'confirmation_number','route' => 'super-admin.redemptions'],
        ['label' => 'Users',         'icon' => 'group',              'route' => 'super-admin.users'],
        ['label' => 'Assets',        'icon' => 'album',              'route' => 'super-admin.assets.index', 'match' => 'super-admin.assets.'],
        ['label' => 'Revenue',       'icon' => 'payments',           'route' => 'super-admin.revenue'],
        ['label' => 'System Health', 'icon' => 'monitor_heart',      'route' => 'super-admin.system'],
        ['label' => 'Audit Log',     'icon' => 'history',            'route' => 'super-admin.audit'],
        ['label' => 'Settings',      'icon' => 'settings',           'route' => 'super-admin.settings'],
    ];
@endphp

<aside id="sidebar"
       class="fixed lg:sticky lg:translate-x-0 top-0 left-0 z-40 h-screen w-64 shrink-0
              bg-surface-container-lowest border-r border-outline-variant/20
              -translate-x-full transition-transform duration-300 ease-out
              flex flex-col">
    <div class="px-lg py-md flex items-center gap-sm border-b border-outline-variant/15">
        <span class="material-symbols-outlined filled text-primary text-headline-md">graphic_eq</span>
        <div class="flex flex-col leading-tight">
            <span class="font-black text-primary tracking-tighter text-[18px]">SoundRedeem</span>
            <span class="text-[10px] uppercase tracking-widest text-secondary">Super Admin</span>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-sm py-md space-y-xs">
        @foreach ($nav as $item)
            @php
                $match = $item['match'] ?? null;
                $active = $route === $item['route'] || ($match && str_starts_with($route, $match));
            @endphp
            <a href="{{ route($item['route']) }}"
               class="sidebar-link {{ $active ? 'active' : '' }}
                      group relative flex items-center gap-sm rounded-lg px-md py-sm text-[14px] font-medium">
                <span class="nav-rail absolute left-0 top-1/2 -translate-y-1/2 h-5 w-[3px] rounded-r-full"></span>
                <span class="material-symbols-outlined {{ $active ? 'filled' : '' }} text-[20px]">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="m-sm p-md rounded-xl bg-gradient-to-br from-primary-container/20 via-primary/10 to-transparent border border-primary/20">
        <div class="flex items-center gap-sm mb-xs">
            <span class="material-symbols-outlined filled text-primary text-[20px]">shield_person</span>
            <span class="font-bold text-on-surface text-[13px]">Super Admin</span>
        </div>
        <p class="text-label-sm text-secondary leading-snug">
            You have full platform access. Actions are logged to the audit trail.
        </p>
    </div>
</aside>
