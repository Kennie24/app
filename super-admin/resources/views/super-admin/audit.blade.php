@extends('layouts.app')

@section('title', 'Audit Log · Super Admin')
@section('breadcrumb', 'Audit Log')

@php
    $sevConfig = [
        'info'     => ['icon' => 'info',         'classes' => 'bg-surface-container-high text-secondary', 'dot' => 'bg-secondary'],
        'success'  => ['icon' => 'check_circle', 'classes' => 'bg-primary/15 text-primary',               'dot' => 'bg-primary'],
        'warning'  => ['icon' => 'warning',      'classes' => 'bg-tertiary-container/40 text-tertiary',   'dot' => 'bg-tertiary'],
        'critical' => ['icon' => 'gpp_bad',      'classes' => 'bg-error-container/60 text-error',         'dot' => 'bg-error pulse-error'],
    ];
@endphp

@section('content')
    @include('partials.page-header', [
        'eyebrow'  => 'Super Admin',
        'title'    => 'Audit Log',
        'subtitle' => 'Every super-admin action is signed, timestamped, and immutable.',
        'actions'  => '
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filters
            </button>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                <span class="material-symbols-outlined text-[18px]">download</span> Export
            </button>
        ',
    ])

    {{-- Quick filters --}}
    <section class="reveal mb-lg flex flex-wrap items-center gap-sm">
        <div class="flex items-center gap-xs bg-surface-container rounded-full px-md h-10 w-full md:w-96">
            <span class="material-symbols-outlined text-secondary text-[20px]">search</span>
            <input type="search" placeholder="Search by actor, action, or target…" class="bg-transparent outline-none w-full text-body-md text-on-surface placeholder:text-outline" />
        </div>
        <div class="bg-surface-container-high rounded-full p-1 flex gap-1">
            @foreach (['All','Info','Success','Warning','Critical'] as $i => $label)
                <button class="px-md py-xs rounded-full text-label-md uppercase tracking-widest transition
                    {{ $i === 0 ? 'bg-primary-container text-on-primary-container' : 'text-secondary hover:text-on-surface' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </section>

    {{-- Event timeline --}}
    <div class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg">
        <ol class="relative border-s border-outline-variant/30 ml-2 space-y-lg">
            @foreach ($events as $i => $e)
                @php $cfg = $sevConfig[$e['severity']] ?? $sevConfig['info']; @endphp
                <li class="ms-md relative">
                    <span class="absolute -start-[22px] top-1.5 w-4 h-4 rounded-full bg-background border-2 border-outline-variant/40 flex items-center justify-center">
                        <span class="w-1.5 h-1.5 rounded-full {{ $cfg['dot'] }}"></span>
                    </span>
                    <div class="flex flex-col md:flex-row md:items-center gap-sm">
                        <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest w-fit {{ $cfg['classes'] }}">
                            <span class="material-symbols-outlined text-[14px]">{{ $cfg['icon'] }}</span>
                            {{ strtoupper($e['severity']) }}
                        </span>
                        <p class="text-body-md text-on-surface flex-1 min-w-0">
                            <span class="font-bold">{{ $e['who'] }}</span>
                            <span class="text-secondary">performed</span>
                            <span class="font-mono text-primary">{{ $e['action'] }}</span>
                            <span class="text-secondary">on</span>
                            <span class="font-bold">{{ $e['target'] }}</span>
                        </p>
                        <span class="text-label-sm text-secondary whitespace-nowrap">{{ $e['time'] }}</span>
                    </div>
                    <p class="text-label-sm text-secondary ml-0 md:ml-[112px] mt-xs">{{ $e['meta'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
@endsection
