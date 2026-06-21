@extends('layouts.app')

@section('title', 'Overview · Super Admin')
@section('breadcrumb', 'Overview')

@php
    // Helpers for SVG charting (kept inline so the view is self-contained)
    $sparkPath = function (array $points, int $w = 120, int $h = 32): string {
        if (empty($points)) return '';
        $min = min($points); $max = max($points);
        $range = max(1, $max - $min);
        $step = $w / max(1, count($points) - 1);
        $d = '';
        foreach ($points as $i => $p) {
            $x = $i * $step;
            $y = $h - (($p - $min) / $range) * $h;
            $d .= ($i === 0 ? 'M' : 'L') . round($x, 2) . ',' . round($y, 2) . ' ';
        }
        return trim($d);
    };

    $linePath = function (array $points, int $w = 400, int $h = 150): string {
        if (empty($points)) return '';
        $min = 0; $max = max($points); $range = max(1, $max - $min);
        $step = $w / max(1, count($points) - 1);
        $d = '';
        foreach ($points as $i => $p) {
            $x = $i * $step;
            $y = $h - (($p - $min) / $range) * ($h - 10) - 5;
            $d .= ($i === 0 ? 'M' : 'L') . round($x, 2) . ',' . round($y, 2) . ' ';
        }
        return trim($d);
    };

    $areaPath = function (array $points, int $w = 400, int $h = 150) use ($linePath): string {
        $line = $linePath($points, $w, $h);
        return $line . " L{$w},{$h} L0,{$h} Z";
    };

    $statusColor = function (string $status): string {
        return match (strtolower($status)) {
            'success', 'operational' => 'success',
            'pending'                => 'warning',
            'invalid', 'refund', 'degraded' => 'error',
            default                  => 'muted',
        };
    };
@endphp

@section('content')
    {{-- Header --}}
    <section class="reveal mb-lg flex flex-col md:flex-row md:items-end md:justify-between gap-md">
        <div>
            <span class="text-label-md text-secondary uppercase tracking-widest">Super Admin</span>
            <h1 class="text-headline-lg font-bold mt-xs">Platform Overview</h1>
            <p class="text-body-md text-secondary mt-xs">Real-time signals across redemptions, users, revenue, and infrastructure.</p>
        </div>
        <div class="flex flex-wrap items-center gap-sm">
            <div class="bg-surface-container-high rounded-full p-1 flex gap-1">
                @foreach (['24H','7D','30D','90D','YTD'] as $range)
                    <button class="px-md py-xs rounded-full text-label-md uppercase tracking-widest transition
                                  {{ $range === '7D' ? 'bg-primary-container text-on-primary-container' : 'text-secondary hover:text-on-surface' }}">
                        {{ $range }}
                    </button>
                @endforeach
            </div>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md">
                <span class="material-symbols-outlined text-[18px]">download</span> Export
            </button>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                <span class="material-symbols-outlined text-[18px]">flash_on</span> New Drop
            </button>
        </div>
    </section>

    {{-- KPI Bento --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-gutter mb-lg">
        @foreach ($kpis as $i => $kpi)
            <div class="reveal bento-card rounded-xl p-md border border-outline-variant/10 flex flex-col gap-sm relative overflow-hidden"
                 style="--reveal-delay: {{ $i * 60 }}ms">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-xs text-secondary">
                        <span class="material-symbols-outlined text-[18px]">{{ $kpi['icon'] }}</span>
                        <span class="text-label-md uppercase tracking-widest">{{ $kpi['label'] }}</span>
                    </div>
                    <span class="text-label-sm font-bold uppercase tracking-widest px-2 py-1 rounded-full
                                 {{ $kpi['trend'] === 'up' ? 'bg-primary/15 text-primary' : 'bg-error-container/40 text-error' }}">
                        <span class="material-symbols-outlined align-middle text-[14px]">{{ $kpi['trend'] === 'up' ? 'trending_up' : 'trending_down' }}</span>
                        {{ $kpi['delta'] }}
                    </span>
                </div>
                <h3 class="text-headline-lg font-black text-on-surface">{{ $kpi['value'] }}</h3>
                <svg viewBox="0 0 120 32" class="w-full h-8" preserveAspectRatio="none">
                    <path d="{{ $sparkPath($kpi['spark']) }}"
                          fill="none"
                          stroke="{{ $kpi['trend'] === 'up' ? '#53e076' : '#ffb4ab' }}"
                          stroke-width="2"
                          stroke-linecap="round"
                          stroke-linejoin="round" />
                </svg>
            </div>
        @endforeach
    </section>

    {{-- Revenue chart + Source breakdown --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-gutter mb-lg">
        <div class="reveal xl:col-span-2 bento-card rounded-xl p-lg border border-outline-variant/10">
            <div class="flex items-center justify-between mb-md">
                <div>
                    <h3 class="text-label-md uppercase tracking-widest text-secondary">Revenue Trend</h3>
                    <p class="text-headline-md font-bold mt-xs">$284,920</p>
                </div>
                <div class="flex items-center gap-md text-label-sm text-secondary">
                    <span class="flex items-center gap-xs"><span class="w-2 h-2 rounded-full bg-primary"></span>This week</span>
                    <span class="flex items-center gap-xs"><span class="w-2 h-2 rounded-full bg-surface-bright"></span>Previous</span>
                </div>
            </div>

            <div class="relative h-[220px] w-full">
                <svg viewBox="0 0 400 150" class="w-full h-full" preserveAspectRatio="none">
                    @foreach ([0, 50, 100, 150] as $y)
                        <line x1="0" x2="400" y1="{{ $y }}" y2="{{ $y }}" stroke="#282828" stroke-width="1" />
                    @endforeach
                    <defs>
                        <linearGradient id="rev-fill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%"   style="stop-color:#1db954;stop-opacity:0.35" />
                            <stop offset="100%" style="stop-color:#1db954;stop-opacity:0" />
                        </linearGradient>
                    </defs>
                    <path d="{{ $areaPath($revenueSeries['current']) }}" fill="url(#rev-fill)" />
                    <path d="{{ $linePath($revenueSeries['previous']) }}" fill="none" stroke="#393939" stroke-width="2" stroke-dasharray="4 4" />
                    <path d="{{ $linePath($revenueSeries['current']) }}"  fill="none" stroke="#53e076" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="flex justify-between text-label-sm text-secondary pt-sm">
                @foreach ($revenueSeries['labels'] as $l)
                    <span>{{ $l }}</span>
                @endforeach
            </div>
        </div>

        <div class="reveal" data-direction="left">
            <div class="bento-card rounded-xl p-lg border border-outline-variant/10 h-full">
                <h3 class="text-label-md uppercase tracking-widest text-secondary mb-md">Redemption Sources</h3>
                @php $total = array_sum(array_column($sourceBreakdown, 'value')); @endphp
                <div class="flex items-center justify-center mb-md">
                    @php
                        $r = 60; $cx = 80; $cy = 80; $cum = 0;
                        $circ = 2 * M_PI * $r;
                    @endphp
                    <svg viewBox="0 0 160 160" class="w-44 h-44">
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="#1c1b1b" stroke-width="18" />
                        @foreach ($sourceBreakdown as $s)
                            @php
                                $frac = $s['value'] / max(1, $total);
                                $len = $frac * $circ;
                                $offset = -$cum;
                                $cum += $len;
                            @endphp
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}"
                                    fill="none"
                                    stroke="{{ $s['color'] }}"
                                    stroke-width="18"
                                    stroke-dasharray="{{ round($len, 2) }} {{ round($circ - $len, 2) }}"
                                    stroke-dashoffset="{{ round($offset, 2) }}"
                                    transform="rotate(-90 {{ $cx }} {{ $cy }})" />
                        @endforeach
                        <text x="{{ $cx }}" y="{{ $cy - 4 }}" text-anchor="middle"
                              class="fill-on-surface" style="font-size:22px;font-weight:900">{{ $total }}%</text>
                        <text x="{{ $cx }}" y="{{ $cy + 16 }}" text-anchor="middle"
                              class="fill-current text-secondary" style="font-size:9px;letter-spacing:0.15em;text-transform:uppercase">Sources</text>
                    </svg>
                </div>
                <ul class="space-y-xs">
                    @foreach ($sourceBreakdown as $s)
                        <li class="flex items-center justify-between text-body-md">
                            <span class="flex items-center gap-xs">
                                <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $s['color'] }}"></span>
                                {{ $s['label'] }}
                            </span>
                            <span class="font-bold text-on-surface">{{ $s['value'] }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Recent Redemptions + Live Feed --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-gutter mb-lg">
        <div class="reveal xl:col-span-2 bento-card rounded-xl border border-outline-variant/10 overflow-hidden">
            <div class="px-lg py-md flex items-center justify-between">
                <div>
                    <h3 class="text-label-md uppercase tracking-widest text-secondary">Recent Redemptions</h3>
                    <p class="text-body-md text-secondary mt-xs">Latest token activity across all channels</p>
                </div>
                <a href="{{ route('super-admin.redemptions') }}" class="text-primary text-label-md font-bold uppercase tracking-widest hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-y border-outline-variant/15 text-label-md uppercase tracking-widest text-secondary">
                        <tr>
                            <th class="px-lg py-sm font-bold">Token</th>
                            <th class="px-lg py-sm font-bold">User</th>
                            <th class="px-lg py-sm font-bold hidden md:table-cell">Asset</th>
                            <th class="px-lg py-sm font-bold">Status</th>
                            <th class="px-lg py-sm font-bold hidden md:table-cell">Value</th>
                            <th class="px-lg py-sm font-bold text-right">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentRedemptions as $r)
                            @php
                                $variant = $statusColor($r['status']);
                                $variantClasses = [
                                    'success' => 'bg-primary/15 text-primary',
                                    'warning' => 'bg-tertiary-container/40 text-tertiary',
                                    'error'   => 'bg-error-container/60 text-error',
                                    'muted'   => 'bg-surface-container-high text-secondary',
                                ][$variant];
                            @endphp
                            <tr class="border-b border-outline-variant/10 hover:bg-surface-container/60 transition">
                                <td class="px-lg py-md text-body-md font-bold text-on-surface">{{ $r['id'] }}</td>
                                <td class="px-lg py-md text-body-md text-secondary truncate">{{ $r['user'] }}</td>
                                <td class="px-lg py-md text-body-md text-secondary hidden md:table-cell">{{ $r['asset'] }}</td>
                                <td class="px-lg py-md">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $variantClasses }}">
                                        {{ $r['status'] }}
                                    </span>
                                </td>
                                <td class="px-lg py-md text-body-md hidden md:table-cell {{ str_starts_with($r['value'], '-') ? 'text-error' : 'text-on-surface' }}">{{ $r['value'] }}</td>
                                <td class="px-lg py-md text-label-sm text-secondary text-right">{{ $r['time'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="reveal" data-direction="left">
            <div class="bento-card rounded-xl border border-outline-variant/10 h-full p-lg">
                <div class="flex items-center justify-between mb-md">
                    <h3 class="text-label-md uppercase tracking-widest text-secondary">Live Feed</h3>
                    <span class="flex items-center gap-xs text-primary text-label-sm font-bold uppercase tracking-widest">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-primary opacity-75 animate-ping"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        Live
                    </span>
                </div>
                <ol class="relative border-s border-outline-variant/30 ml-2 space-y-md">
                    @foreach ([
                        ['icon' => 'block',          'title' => 'Token rejected',    'meta' => '#TK-88217 · already redeemed','time' => '6 min ago', 'color' => 'text-error'],
                        ['icon' => 'payments',       'title' => 'Payment captured',  'meta' => '$9.99 · stripe',             'time' => '8 min ago', 'color' => 'text-primary'],
                        ['icon' => 'cloud_upload',   'title' => 'New asset published','meta' => 'Cyber Echoes · "Skyline"',  'time' => '22 min ago','color' => 'text-on-surface'],
                    ] as $event)
                        <li class="ms-md relative">
                            <span class="absolute -start-[22px] top-1 w-4 h-4 rounded-full bg-background border-2 border-outline-variant/40 flex items-center justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            </span>
                            <div class="flex items-start gap-sm">
                                <span class="material-symbols-outlined text-[20px] {{ $event['color'] }}">{{ $event['icon'] }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-body-md font-bold text-on-surface">{{ $event['title'] }}</p>
                                    <p class="text-label-sm text-secondary truncate">{{ $event['meta'] }}</p>
                                </div>
                                <span class="text-label-sm text-secondary whitespace-nowrap">{{ $event['time'] }}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    {{-- Top Artists + Users --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-gutter mb-lg">
        <div class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
            <div class="flex items-center justify-between mb-md">
                <h3 class="text-label-md uppercase tracking-widest text-secondary">Top Artists</h3>
                <span class="text-label-sm text-secondary">last 7 days</span>
            </div>
            <ul class="space-y-sm">
                @foreach ($topArtists as $i => $a)
                    <li class="flex items-center gap-sm group">
                        <span class="text-body-md font-black text-secondary w-5 text-right">{{ $i + 1 }}</span>
                        <img src="{{ $a['img'] }}" alt="{{ $a['name'] }}" class="w-10 h-10 rounded-lg object-cover" />
                        <div class="flex-1 min-w-0">
                            <p class="text-body-md font-bold text-on-surface truncate">{{ $a['name'] }}</p>
                            <p class="text-label-sm text-secondary">{{ $a['plays'] }} plays</p>
                        </div>
                        <span class="text-label-sm font-bold text-primary">{{ $a['change'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="reveal xl:col-span-2 bento-card rounded-xl border border-outline-variant/10 overflow-hidden">
            <div class="px-lg py-md flex items-center justify-between">
                <div>
                    <h3 class="text-label-md uppercase tracking-widest text-secondary">Users</h3>
                    <p class="text-body-md text-secondary mt-xs">Most active accounts</p>
                </div>
                <a href="{{ route('super-admin.users') }}" class="text-primary text-label-md font-bold uppercase tracking-widest hover:underline">Manage</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-y border-outline-variant/15 text-label-md uppercase tracking-widest text-secondary">
                        <tr>
                            <th class="px-lg py-sm font-bold">User</th>
                            <th class="px-lg py-sm font-bold">Tier</th>
                            <th class="px-lg py-sm font-bold hidden md:table-cell">Redemptions</th>
                            <th class="px-lg py-sm font-bold">Status</th>
                            <th class="px-lg py-sm font-bold hidden md:table-cell">Joined</th>
                            <th class="px-lg py-sm font-bold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            @php
                                $variant = $statusColor($u['status']);
                                $variantClasses = [
                                    'success' => 'bg-primary/15 text-primary',
                                    'warning' => 'bg-tertiary-container/40 text-tertiary',
                                    'error'   => 'bg-error-container/60 text-error',
                                    'muted'   => 'bg-surface-container-high text-secondary',
                                ][$variant];
                            @endphp
                            <tr class="border-b border-outline-variant/10 hover:bg-surface-container/60 transition">
                                <td class="px-lg py-md">
                                    <div class="flex items-center gap-sm">
                                        <div class="w-9 h-9 rounded-full bg-surface-container-high flex items-center justify-center text-primary font-bold uppercase">
                                            {{ collect(explode(' ', $u['name']))->map(fn ($p) => $p[0] ?? '')->join('') }}
                                        </div>
                                        <div class="leading-tight">
                                            <p class="text-body-md font-bold text-on-surface">{{ $u['name'] }}</p>
                                            <p class="text-label-sm text-secondary">{{ $u['email'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-lg py-md text-body-md text-secondary">{{ $u['tier'] }}</td>
                                <td class="px-lg py-md text-body-md text-on-surface hidden md:table-cell">{{ $u['redemptions'] }}</td>
                                <td class="px-lg py-md">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $variantClasses }}">
                                        {{ $u['status'] }}
                                    </span>
                                </td>
                                <td class="px-lg py-md text-label-sm text-secondary hidden md:table-cell">{{ $u['joined'] }}</td>
                                <td class="px-lg py-md text-right">
                                    <button class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-secondary hover:text-primary transition">
                                        <span class="material-symbols-outlined text-[18px] align-middle">more_horiz</span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- System Health --}}
    <section class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg mb-xl">
        <div class="flex items-center justify-between mb-md">
            <div>
                <h3 class="text-label-md uppercase tracking-widest text-secondary">System Health</h3>
                <p class="text-body-md text-secondary mt-xs">Real-time status of platform services</p>
            </div>
            <a href="{{ route('super-admin.system') }}" class="text-primary text-label-md font-bold uppercase tracking-widest hover:underline">Status Page</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-gutter">
            @foreach ($systemHealth as $service)
                @php
                    $isOk = strtolower($service['status']) === 'operational';
                @endphp
                <div class="rounded-xl p-md border {{ $isOk ? 'border-primary/20 bg-primary/5' : 'border-error/30 bg-error-container/10' }}">
                    <div class="flex items-center justify-between mb-xs">
                        <span class="text-body-md font-bold text-on-surface">{{ $service['service'] }}</span>
                        <span class="flex items-center gap-xs text-label-sm font-bold uppercase tracking-widest {{ $isOk ? 'text-primary' : 'text-error' }}">
                            <span class="w-2 h-2 rounded-full {{ $isOk ? 'bg-primary' : 'bg-error pulse-error' }}"></span>
                            {{ $service['status'] }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-label-sm text-secondary">
                        <span>Latency · <span class="text-on-surface font-bold">{{ $service['latency'] }}</span></span>
                        <span>Uptime · <span class="text-on-surface font-bold">{{ $service['uptime'] }}</span></span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
