@extends('layouts.app')

@section('title', 'Assets · Super Admin')
@section('breadcrumb', 'Assets')

@php
    $statusClasses = [
        'live'      => 'bg-primary/15 text-primary',
        'scheduled' => 'bg-tertiary-container/40 text-tertiary',
        'archived'  => 'bg-surface-container-high text-secondary',
    ];
@endphp

@section('content')
    @include('partials.page-header', [
        'eyebrow'  => 'Super Admin',
        'title'    => 'Assets',
        'subtitle' => 'Every EP, single, and limited-edition drop on SoundRedeem.',
        'actions'  => '
            <a href="'.route('super-admin.assets.create').'"
               class="flex items-center gap-xs px-md h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                <span class="material-symbols-outlined text-[18px]">add</span> New Asset
            </a>
        ',
    ])

    @if (session('flash'))
        <div class="reveal mb-lg flex items-center gap-sm rounded-xl border border-primary/30 bg-primary/10 px-md py-sm text-primary">
            <span class="material-symbols-outlined">check_circle</span>
            <span class="text-body-md font-bold">{{ session('flash') }}</span>
        </div>
    @endif

    {{-- Stats --}}
    <section class="grid grid-cols-2 xl:grid-cols-4 gap-gutter mb-lg">
        @foreach ($stats as $i => $s)
            <div class="reveal bento-card rounded-xl p-md border border-outline-variant/10" style="--reveal-delay: {{ $i * 60 }}ms">
                <div class="flex items-center gap-xs text-secondary mb-xs">
                    <span class="material-symbols-outlined text-[18px]">{{ $s['icon'] }}</span>
                    <span class="text-label-md uppercase tracking-widest">{{ $s['label'] }}</span>
                </div>
                <h3 class="text-headline-md font-black text-on-surface">{{ $s['value'] }}</h3>
                <p class="text-label-sm text-secondary mt-xs">{{ $s['delta'] }}</p>
            </div>
        @endforeach
    </section>

    @if ($assets->isEmpty())
        <div class="reveal bento-card rounded-xl border border-outline-variant/15 p-xl flex flex-col items-center text-center gap-md">
            <span class="material-symbols-outlined text-primary text-[56px]">album</span>
            <p class="text-headline-md">No assets yet</p>
            <p class="text-body-md text-secondary max-w-prose">Create your first drop — vinyl insert, digital pass, NFC tag, or merch QR.</p>
            <a href="{{ route('super-admin.assets.create') }}"
               class="mt-sm inline-flex items-center gap-xs px-md py-sm rounded-full bg-primary-container text-on-primary-container font-bold uppercase tracking-widest text-label-md hover:scale-[1.02] transition-transform">
                <span class="material-symbols-outlined text-[18px]">add</span>
                New Asset
            </a>
        </div>
    @else
        <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-gutter mb-lg">
            @foreach ($assets as $i => $a)
                <div class="reveal bento-card rounded-xl border border-outline-variant/10 overflow-hidden flex flex-col" style="--reveal-delay: {{ $i * 60 }}ms">
                    <div class="relative aspect-[5/3] overflow-hidden bg-surface-container-high">
                        {{-- Fallback layer behind the image. If <img> 404s, onerror reveals this. --}}
                        <div class="absolute inset-0 flex flex-col items-center justify-center gap-xs text-secondary">
                            <span class="material-symbols-outlined text-[64px] opacity-50">album</span>
                            <span class="text-label-sm uppercase tracking-widest opacity-70">{{ $a->artist }}</span>
                        </div>
                        @if ($a->cover_url)
                            <img src="{{ $a->cover_url }}"
                                 alt="{{ $a->title }}"
                                 loading="lazy"
                                 referrerpolicy="no-referrer"
                                 onerror="this.onerror=null; this.style.display='none';"
                                 class="absolute inset-0 w-full h-full object-cover" />
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-background via-background/40 to-transparent pointer-events-none"></div>
                        <div class="absolute top-md left-md flex items-center gap-xs">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-label-sm font-bold uppercase tracking-widest {{ $statusClasses[$a->status] ?? '' }}">
                                <span class="w-2 h-2 rounded-full {{ $a->status === 'live' ? 'bg-primary animate-pulse' : 'bg-current opacity-60' }}"></span>
                                {{ $a->status_label }}
                            </span>
                        </div>
                        <div class="absolute bottom-md left-md right-md">
                            <p class="text-label-sm text-secondary uppercase tracking-widest">{{ $a->artist }}</p>
                            <h3 class="text-headline-md font-black text-on-surface leading-tight">{{ $a->title }}</h3>
                        </div>
                    </div>
                    <div class="p-md space-y-sm flex-1 flex flex-col">
                        <div class="flex items-center justify-between text-label-md">
                            <span class="text-secondary uppercase tracking-widest">Redemptions</span>
                            <span class="text-on-surface font-bold">{{ number_format($a->redemptions) }} / {{ number_format($a->redemption_limit) }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-surface-container-high overflow-hidden">
                            <div class="h-full rounded-full bg-primary" style="width: {{ $a->progress_percent }}%"></div>
                        </div>
                        <div class="flex items-center justify-between pt-xs mt-auto">
                            <span class="text-label-md text-secondary uppercase tracking-widest">Price · <span class="text-on-surface">{{ $a->price_formatted }}</span></span>
                            <div class="flex gap-xs">
                                <a href="{{ route('super-admin.assets.edit', $a) }}" title="Edit" class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-secondary hover:text-primary transition flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                                <form action="{{ route('super-admin.assets.destroy', $a) }}" method="POST"
                                      onsubmit="return confirm('Delete “{{ $a->title }}”? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button title="Delete" class="w-8 h-8 rounded-full bg-surface-container hover:bg-error-container/40 text-secondary hover:text-error transition flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
    @endif
@endsection
