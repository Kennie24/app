@extends('layouts.app')

@section('title', 'Revenue · Super Admin')
@section('breadcrumb', 'Revenue')

@php
    $max = max($bars['values']);
    $statusClasses = ['Processed' => 'bg-primary/15 text-primary', 'Pending' => 'bg-tertiary-container/40 text-tertiary'];
@endphp

@section('content')
    @include('partials.page-header', [
        'eyebrow'  => 'Super Admin',
        'title'    => 'Revenue',
        'subtitle' => 'Top-line, payouts, region splits, and per-asset earnings.',
        'actions'  => '
            <div class="bg-surface-container-high rounded-full p-1 flex gap-1">
                <button class="px-md py-xs rounded-full text-secondary text-label-md uppercase tracking-widest">7D</button>
                <button class="px-md py-xs rounded-full text-secondary text-label-md uppercase tracking-widest">30D</button>
                <button class="px-md py-xs rounded-full bg-primary-container text-on-primary-container text-label-md uppercase tracking-widest">YTD</button>
                <button class="px-md py-xs rounded-full text-secondary text-label-md uppercase tracking-widest">ALL</button>
            </div>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                <span class="material-symbols-outlined text-[18px]">download</span> Report
            </button>
        ',
    ])

    {{-- KPI cards --}}
    <section class="grid grid-cols-2 xl:grid-cols-4 gap-gutter mb-lg">
        @foreach ($kpis as $i => $k)
            <div class="reveal bento-card rounded-xl p-md border border-outline-variant/10" style="--reveal-delay: {{ $i * 60 }}ms">
                <div class="flex items-center justify-between mb-xs">
                    <div class="flex items-center gap-xs text-secondary">
                        <span class="material-symbols-outlined text-[18px]">{{ $k['icon'] }}</span>
                        <span class="text-label-md uppercase tracking-widest">{{ $k['label'] }}</span>
                    </div>
                    <span class="text-label-sm font-bold uppercase tracking-widest px-2 py-1 rounded-full
                        {{ $k['trend'] === 'up' ? 'bg-primary/15 text-primary' : 'bg-error-container/40 text-error' }}">
                        <span class="material-symbols-outlined align-middle text-[14px]">{{ $k['trend'] === 'up' ? 'trending_up' : 'trending_down' }}</span>
                        {{ $k['delta'] }}
                    </span>
                </div>
                <h3 class="text-headline-md font-black text-on-surface">{{ $k['value'] }}</h3>
            </div>
        @endforeach
    </section>

    {{-- Monthly bars + Region donut --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-gutter mb-lg">
        <div class="reveal xl:col-span-2 bento-card rounded-xl border border-outline-variant/10 p-lg">
            <div class="flex items-center justify-between mb-md">
                <div>
                    <h3 class="text-label-md uppercase tracking-widest text-secondary">Monthly Revenue</h3>
                    <p class="text-headline-md font-bold mt-xs">$2.84M YTD</p>
                </div>
                <span class="text-label-sm text-secondary">In thousands USD</span>
            </div>
            <div class="flex items-end gap-xs h-56">
                @foreach ($bars['values'] as $i => $v)
                    @php $h = round(($v / $max) * 100); $isLast = $i === count($bars['values']) - 1; @endphp
                    <div class="flex-1 flex flex-col items-center gap-xs group">
                        <div class="w-full rounded-t-md transition-colors {{ $isLast ? 'bg-primary' : 'bg-primary/40 group-hover:bg-primary' }}"
                             style="height: {{ $h }}%"></div>
                        <span class="text-label-sm text-secondary">{{ $bars['labels'][$i] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="reveal" data-direction="left">
            <div class="bento-card rounded-xl border border-outline-variant/10 p-lg h-full">
                <h3 class="text-label-md uppercase tracking-widest text-secondary mb-md">By Region</h3>
                @php
                    $r = 60; $cx = 80; $cy = 80; $cum = 0;
                    $circ = 2 * M_PI * $r;
                    $colors = ['#53e076','#72fe8f','#cfc4c4','#ffb3b3'];
                @endphp
                <div class="flex items-center justify-center mb-md">
                    <svg viewBox="0 0 160 160" class="w-44 h-44">
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none" stroke="#1c1b1b" stroke-width="18" />
                        @foreach ($byRegion as $i => $rg)
                            @php
                                $frac = $rg['pct'] / 100;
                                $len = $frac * $circ;
                                $offset = -$cum;
                                $cum += $len;
                            @endphp
                            <circle cx="{{ $cx }}" cy="{{ $cy }}" r="{{ $r }}" fill="none"
                                    stroke="{{ $colors[$i] ?? '#53e076' }}" stroke-width="18"
                                    stroke-dasharray="{{ round($len, 2) }} {{ round($circ - $len, 2) }}"
                                    stroke-dashoffset="{{ round($offset, 2) }}"
                                    transform="rotate(-90 {{ $cx }} {{ $cy }})" />
                        @endforeach
                        <text x="{{ $cx }}" y="{{ $cy + 4 }}" text-anchor="middle" class="fill-on-surface" style="font-size:14px;font-weight:700">100%</text>
                    </svg>
                </div>
                <ul class="space-y-xs">
                    @foreach ($byRegion as $i => $rg)
                        <li class="flex items-center justify-between text-body-md">
                            <span class="flex items-center gap-xs min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background: {{ $colors[$i] ?? '#53e076' }}"></span>
                                <span class="truncate">{{ $rg['region'] }}</span>
                            </span>
                            <span class="font-bold text-on-surface whitespace-nowrap">{{ $rg['value'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Top Earners + Payouts --}}
    <section class="grid grid-cols-1 xl:grid-cols-2 gap-gutter mb-lg">
        <div class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
            <h3 class="text-label-md uppercase tracking-widest text-secondary mb-md">Top Earners</h3>
            <ul class="space-y-md">
                @foreach ($topEarners as $i => $e)
                    <li>
                        <div class="flex items-center gap-sm mb-xs">
                            <span class="text-body-md font-black text-secondary w-5 text-right">{{ $i + 1 }}</span>
                            <img src="{{ $e['img'] }}" alt="" class="w-10 h-10 rounded-lg object-cover" />
                            <div class="flex-1 min-w-0">
                                <p class="text-body-md font-bold text-on-surface truncate">{{ $e['title'] }}</p>
                                <p class="text-label-sm text-secondary truncate">{{ $e['artist'] }}</p>
                            </div>
                            <span class="text-body-md font-bold text-primary whitespace-nowrap">{{ $e['revenue'] }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-surface-container-high overflow-hidden ml-9">
                            <div class="h-full rounded-full bg-primary" style="width: {{ $e['pct'] }}%"></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="reveal" data-direction="left">
            <div class="bento-card rounded-xl border border-outline-variant/10 overflow-hidden">
                <div class="px-lg py-md flex items-center justify-between">
                    <div>
                        <h3 class="text-label-md uppercase tracking-widest text-secondary">Recent Payouts</h3>
                        <p class="text-body-md text-secondary mt-xs">Settled to bank · Stripe</p>
                    </div>
                    <button class="text-primary text-label-md font-bold uppercase tracking-widest hover:underline">View All</button>
                </div>
                <table class="w-full text-left">
                    <thead class="border-y border-outline-variant/15 text-label-md uppercase tracking-widest text-secondary">
                        <tr>
                            <th class="px-lg py-sm font-bold">Date</th>
                            <th class="px-lg py-sm font-bold hidden md:table-cell">Method</th>
                            <th class="px-lg py-sm font-bold">Amount</th>
                            <th class="px-lg py-sm font-bold text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payouts as $p)
                            <tr class="border-b border-outline-variant/10">
                                <td class="px-lg py-md text-body-md text-on-surface">{{ $p['date'] }}</td>
                                <td class="px-lg py-md text-body-md text-secondary hidden md:table-cell">{{ $p['method'] }}</td>
                                <td class="px-lg py-md text-body-md font-bold text-on-surface">{{ $p['amount'] }}</td>
                                <td class="px-lg py-md text-right">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $statusClasses[$p['status']] ?? '' }}">
                                        {{ $p['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
