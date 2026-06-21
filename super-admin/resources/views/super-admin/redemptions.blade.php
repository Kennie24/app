@extends('layouts.app')

@section('title', 'Redemptions · Super Admin')
@section('breadcrumb', 'Redemptions')

@php
    $statusClasses = [
        'Success' => 'bg-primary/15 text-primary',
        'Pending' => 'bg-tertiary-container/40 text-tertiary',
        'Invalid' => 'bg-error-container/60 text-error',
        'Refund'  => 'bg-error-container/60 text-error',
    ];
@endphp

@section('content')
    @include('partials.page-header', [
        'eyebrow'  => 'Super Admin',
        'title'    => 'Redemptions',
        'subtitle' => 'Every token activity across all channels — filter, inspect, and intervene in real time.',
        'actions'  => '
            <div class="bg-surface-container-high rounded-full p-1 flex gap-1">
                <button class="px-md py-xs rounded-full bg-primary-container text-on-primary-container text-label-md uppercase tracking-widest">All</button>
                <button class="px-md py-xs rounded-full text-secondary hover:text-on-surface text-label-md uppercase tracking-widest">Success</button>
                <button class="px-md py-xs rounded-full text-secondary hover:text-on-surface text-label-md uppercase tracking-widest">Pending</button>
                <button class="px-md py-xs rounded-full text-secondary hover:text-on-surface text-label-md uppercase tracking-widest">Errors</button>
            </div>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md">
                <span class="material-symbols-outlined text-[18px]">tune</span> Filters
            </button>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                <span class="material-symbols-outlined text-[18px]">download</span> Export CSV
            </button>
        ',
    ])

    {{-- Stats --}}
    <section class="grid grid-cols-2 xl:grid-cols-4 gap-gutter mb-lg">
        @foreach ($stats as $i => $s)
            <div class="reveal bento-card rounded-xl p-md border border-outline-variant/10" style="--reveal-delay: {{ $i * 60 }}ms">
                <div class="flex items-center justify-between mb-xs">
                    <div class="flex items-center gap-xs text-secondary">
                        <span class="material-symbols-outlined text-[18px]">{{ $s['icon'] }}</span>
                        <span class="text-label-md uppercase tracking-widest">{{ $s['label'] }}</span>
                    </div>
                    <span class="text-label-sm font-bold uppercase tracking-widest px-2 py-1 rounded-full
                        {{ str_starts_with($s['delta'], '-') ? 'bg-error-container/40 text-error' : 'bg-primary/15 text-primary' }}">{{ $s['delta'] }}</span>
                </div>
                <h3 class="text-headline-md font-black text-on-surface">{{ $s['value'] }}</h3>
            </div>
        @endforeach
    </section>

    {{-- Table --}}
    <div class="reveal bento-card rounded-xl border border-outline-variant/10 overflow-hidden">
        <div class="px-lg py-md flex flex-col md:flex-row md:items-center md:justify-between gap-sm">
            <div class="flex items-center gap-xs bg-surface-container rounded-full px-md h-10 w-full md:w-80">
                <span class="material-symbols-outlined text-secondary text-[20px]">search</span>
                <input type="search" placeholder="Search by token, user, asset…"
                       class="bg-transparent outline-none w-full text-body-md text-on-surface placeholder:text-outline" />
            </div>
            <div class="flex items-center gap-sm text-label-sm text-secondary">
                Showing <span class="text-on-surface font-bold">{{ count($rows) }}</span> of 38,420
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-y border-outline-variant/15 text-label-md uppercase tracking-widest text-secondary">
                    <tr>
                        <th class="px-lg py-sm font-bold">Token</th>
                        <th class="px-lg py-sm font-bold">User</th>
                        <th class="px-lg py-sm font-bold hidden md:table-cell">Asset</th>
                        <th class="px-lg py-sm font-bold hidden lg:table-cell">Source</th>
                        <th class="px-lg py-sm font-bold">Status</th>
                        <th class="px-lg py-sm font-bold hidden md:table-cell">Value</th>
                        <th class="px-lg py-sm font-bold hidden xl:table-cell">IP</th>
                        <th class="px-lg py-sm font-bold text-right">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $r)
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container/60 transition">
                            <td class="px-lg py-md text-body-md font-bold text-on-surface">{{ $r['id'] }}</td>
                            <td class="px-lg py-md text-body-md text-secondary truncate">{{ $r['user'] }}</td>
                            <td class="px-lg py-md text-body-md text-secondary hidden md:table-cell">{{ $r['asset'] }}</td>
                            <td class="px-lg py-md text-body-md text-secondary hidden lg:table-cell">{{ $r['source'] }}</td>
                            <td class="px-lg py-md">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $statusClasses[$r['status']] ?? 'bg-surface-container-high text-secondary' }}">
                                    {{ $r['status'] }}
                                </span>
                            </td>
                            <td class="px-lg py-md text-body-md hidden md:table-cell {{ str_starts_with($r['value'], '-') ? 'text-error' : 'text-on-surface' }}">{{ $r['value'] }}</td>
                            <td class="px-lg py-md text-label-sm text-secondary font-mono hidden xl:table-cell">{{ $r['ip'] }}</td>
                            <td class="px-lg py-md text-label-sm text-secondary text-right whitespace-nowrap">{{ $r['time'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-lg py-md flex items-center justify-between text-label-sm text-secondary border-t border-outline-variant/10">
            <span>Page 1 of 384</span>
            <div class="flex gap-xs">
                <button class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-secondary"><span class="material-symbols-outlined text-[18px] align-middle">chevron_left</span></button>
                <button class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface"><span class="material-symbols-outlined text-[18px] align-middle">chevron_right</span></button>
            </div>
        </div>
    </div>
@endsection
