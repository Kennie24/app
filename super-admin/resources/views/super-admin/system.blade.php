@extends('layouts.app')

@section('title', 'System Health · Super Admin')
@section('breadcrumb', 'System Health')

@php
    $sevClasses = [
        'Minor'    => 'bg-tertiary-container/40 text-tertiary',
        'Major'    => 'bg-error-container/40 text-error',
        'Critical' => 'bg-error-container/60 text-error',
    ];
    $statusClasses = [
        'Investigating' => 'bg-tertiary-container/40 text-tertiary',
        'Monitoring'    => 'bg-primary/15 text-primary',
        'Resolved'      => 'bg-primary/15 text-primary',
    ];
    // SVG path for load chart
    $max = max(1, max($loadSeries)); $w = 800; $h = 160;
    $step = $w / max(1, count($loadSeries) - 1);
    $line = ''; $area = '';
    foreach ($loadSeries as $i => $v) {
        $x = round($i * $step, 2);
        $y = round($h - ($v / $max) * ($h - 10) - 5, 2);
        $line .= ($i === 0 ? "M$x,$y " : "L$x,$y ");
    }
    $area = $line . " L$w,$h L0,$h Z";
@endphp

@section('content')
    @include('partials.page-header', [
        'eyebrow'  => 'Super Admin',
        'title'    => 'System Health',
        'subtitle' => 'Live status, latency, and incident history across every service.',
        'actions'  => '
            <span class="flex items-center gap-xs px-md h-10 rounded-full bg-primary/10 border border-primary/30 text-primary text-label-md font-bold uppercase tracking-widest">
                <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full rounded-full bg-primary opacity-75 animate-ping"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span></span>
                Streaming live
            </span>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md">
                <span class="material-symbols-outlined text-[18px]">refresh</span> Re-check
            </button>
        ',
    ])

    {{-- Metric tiles --}}
    <section class="grid grid-cols-2 xl:grid-cols-4 gap-gutter mb-lg">
        @forelse ($metrics as $i => $m)
            <div class="reveal bento-card rounded-xl p-md border border-outline-variant/10" style="--reveal-delay: {{ $i * 60 }}ms">
                <div class="flex items-center gap-xs text-secondary mb-xs">
                    <span class="material-symbols-outlined text-[18px]">{{ $m['icon'] }}</span>
                    <span class="text-label-md uppercase tracking-widest">{{ $m['label'] }}</span>
                </div>
                <h3 class="text-headline-md font-black text-on-surface">{{ $m['value'] }}</h3>
            </div>
        @empty
            <div class="reveal col-span-full rounded-xl border border-outline-variant/20 bg-surface-container-low p-lg text-body-md text-secondary">
                No system metrics are connected yet.
            </div>
        @endforelse
    </section>

    {{-- Load chart --}}
    <div class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg mb-lg">
        <div class="flex items-center justify-between mb-md">
            <div>
                <h3 class="text-label-md uppercase tracking-widest text-secondary">API Load (last 60 min)</h3>
                <p class="text-headline-md font-bold mt-xs">{{ end($loadSeries) }}K rpm</p>
            </div>
            <span class="text-label-sm text-secondary">Updated every 10 s</span>
        </div>
        <div class="relative h-[160px] w-full">
            <svg viewBox="0 0 800 160" class="w-full h-full" preserveAspectRatio="none">
                @foreach ([0, 40, 80, 120, 160] as $y)
                    <line x1="0" x2="800" y1="{{ $y }}" y2="{{ $y }}" stroke="#282828" stroke-width="1" />
                @endforeach
                <defs>
                    <linearGradient id="load-fill" x1="0" x2="0" y1="0" y2="1">
                        <stop offset="0%" style="stop-color:#1db954;stop-opacity:0.35" />
                        <stop offset="100%" style="stop-color:#1db954;stop-opacity:0" />
                    </linearGradient>
                </defs>
                <path d="{{ $area }}" fill="url(#load-fill)" />
                <path d="{{ $line }}" fill="none" stroke="#53e076" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>
    </div>

    {{-- Services + Incidents --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-gutter mb-lg">
        <div class="reveal xl:col-span-2 bento-card rounded-xl border border-outline-variant/10 overflow-hidden">
            <div class="px-lg py-md flex items-center justify-between">
                <div>
                    <h3 class="text-label-md uppercase tracking-widest text-secondary">Services</h3>
                    <p class="text-body-md text-secondary mt-xs">All regions</p>
                </div>
                <span class="text-label-sm text-secondary">{{ count($services) }} services</span>
            </div>
            <table class="w-full text-left">
                <thead class="border-y border-outline-variant/15 text-label-md uppercase tracking-widest text-secondary">
                    <tr>
                        <th class="px-lg py-sm font-bold">Service</th>
                        <th class="px-lg py-sm font-bold">Status</th>
                        <th class="px-lg py-sm font-bold hidden md:table-cell">Region</th>
                        <th class="px-lg py-sm font-bold hidden md:table-cell">Latency</th>
                        <th class="px-lg py-sm font-bold text-right">Uptime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($services as $s)
                        @php $ok = $s['status'] === 'Operational'; @endphp
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container/60 transition">
                            <td class="px-lg py-md text-body-md font-bold text-on-surface">{{ $s['service'] }}</td>
                            <td class="px-lg py-md">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $ok ? 'bg-primary/15 text-primary' : 'bg-error-container/60 text-error' }}">
                                    <span class="w-2 h-2 rounded-full {{ $ok ? 'bg-primary' : 'bg-error pulse-error' }}"></span>
                                    {{ $s['status'] }}
                                </span>
                            </td>
                            <td class="px-lg py-md text-body-md text-secondary font-mono hidden md:table-cell">{{ $s['region'] }}</td>
                            <td class="px-lg py-md text-body-md text-on-surface hidden md:table-cell">{{ $s['latency'] }}</td>
                            <td class="px-lg py-md text-body-md text-on-surface text-right">{{ $s['uptime'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-lg py-xl text-center text-body-md text-secondary">No service health data yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="reveal" data-direction="left">
            <div class="bento-card rounded-xl border border-outline-variant/10 p-lg h-full">
                <h3 class="text-label-md uppercase tracking-widest text-secondary mb-md">Recent Incidents</h3>
                <ul class="space-y-md">
                    @forelse ($incidents as $inc)
                        <li class="border-l-2 border-outline-variant/30 pl-md">
                            <div class="flex items-center gap-xs mb-xs">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $sevClasses[$inc['severity']] ?? '' }}">
                                    {{ $inc['severity'] }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $statusClasses[$inc['status']] ?? '' }}">
                                    {{ $inc['status'] }}
                                </span>
                            </div>
                            <p class="text-body-md font-bold text-on-surface">{{ $inc['title'] }}</p>
                            <p class="text-label-sm text-secondary">{{ $inc['time'] }}</p>
                        </li>
                    @empty
                        <li class="text-body-md text-secondary">No incidents have been recorded.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </section>
@endsection
