@extends('layouts.app')

@section('title', 'Users · Super Admin')
@section('breadcrumb', 'Users')

@php
    $statusClasses = [
        'Active'    => 'bg-primary/15 text-primary',
        'Suspended' => 'bg-error-container/60 text-error',
        'Inactive'  => 'bg-surface-container-high text-secondary',
    ];
@endphp

@section('content')
    @include('partials.page-header', [
        'eyebrow'  => 'Super Admin',
        'title'    => 'Users',
        'subtitle' => 'Manage accounts, tiers, suspensions, and impersonation across the platform.',
        'actions'  => '
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md">
                <span class="material-symbols-outlined text-[18px]">tune</span> Filters
            </button>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface text-label-md">
                <span class="material-symbols-outlined text-[18px]">download</span> Export
            </button>
            <button class="flex items-center gap-xs px-md h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                <span class="material-symbols-outlined text-[18px]">person_add</span> Invite User
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
                        {{ $s['trend'] === 'up' ? 'bg-primary/15 text-primary' : 'bg-error-container/40 text-error' }}">
                        <span class="material-symbols-outlined align-middle text-[14px]">{{ $s['trend'] === 'up' ? 'trending_up' : 'trending_down' }}</span>
                        {{ $s['delta'] }}
                    </span>
                </div>
                <h3 class="text-headline-md font-black text-on-surface">{{ $s['value'] }}</h3>
            </div>
        @endforeach
    </section>

    {{-- Toolbar + table --}}
    <div class="reveal bento-card rounded-xl border border-outline-variant/10 overflow-hidden">
        <div class="px-lg py-md flex flex-col md:flex-row md:items-center md:justify-between gap-sm">
            <div class="flex items-center gap-xs bg-surface-container rounded-full px-md h-10 w-full md:w-80">
                <span class="material-symbols-outlined text-secondary text-[20px]">search</span>
                <input type="search" placeholder="Search users by name, email…" class="bg-transparent outline-none w-full text-body-md text-on-surface placeholder:text-outline" />
            </div>
            <div class="bg-surface-container-high rounded-full p-1 flex gap-1">
                @foreach (['All','Premium','Free','Suspended'] as $i => $label)
                    <button class="px-md py-xs rounded-full text-label-md uppercase tracking-widest transition
                        {{ $i === 0 ? 'bg-primary-container text-on-primary-container' : 'text-secondary hover:text-on-surface' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-y border-outline-variant/15 text-label-md uppercase tracking-widest text-secondary">
                    <tr>
                        <th class="px-lg py-sm font-bold">User</th>
                        <th class="px-lg py-sm font-bold">Tier</th>
                        <th class="px-lg py-sm font-bold hidden md:table-cell">Redemptions</th>
                        <th class="px-lg py-sm font-bold hidden lg:table-cell">Country</th>
                        <th class="px-lg py-sm font-bold">Status</th>
                        <th class="px-lg py-sm font-bold hidden md:table-cell">Joined</th>
                        <th class="px-lg py-sm font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $u)
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container/60 transition">
                            <td class="px-lg py-md">
                                <div class="flex items-center gap-sm">
                                    <div class="w-9 h-9 rounded-full bg-surface-container-high flex items-center justify-center text-primary font-bold uppercase">
                                        {{ collect(explode(' ', $u['name']))->map(fn ($p) => $p[0] ?? '')->join('') }}
                                    </div>
                                    <div class="leading-tight min-w-0">
                                        <p class="text-body-md font-bold text-on-surface">{{ $u['name'] }}</p>
                                        <p class="text-label-sm text-secondary truncate">{{ $u['email'] }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-lg py-md text-body-md text-secondary">{{ $u['tier'] }}</td>
                            <td class="px-lg py-md text-body-md text-on-surface hidden md:table-cell">{{ $u['redemptions'] }}</td>
                            <td class="px-lg py-md text-body-md text-secondary hidden lg:table-cell">{{ $u['country'] }}</td>
                            <td class="px-lg py-md">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $statusClasses[$u['status']] ?? '' }}">
                                    {{ $u['status'] }}
                                </span>
                            </td>
                            <td class="px-lg py-md text-label-sm text-secondary hidden md:table-cell">{{ $u['joined'] }}</td>
                            <td class="px-lg py-md text-right">
                                <div class="flex justify-end gap-xs">
                                    <button title="Impersonate" class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-secondary hover:text-primary transition">
                                        <span class="material-symbols-outlined text-[18px] align-middle">visibility</span>
                                    </button>
                                    <button title="More" class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-secondary hover:text-on-surface transition">
                                        <span class="material-symbols-outlined text-[18px] align-middle">more_horiz</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
