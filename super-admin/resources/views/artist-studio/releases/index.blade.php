@extends('layouts.artist')

@section('title', 'Releases · Artist Studio')
@section('page-title', 'Your Releases')
@section('page-subtitle', 'Singles, albums and previews you publish to the store.')

@section('actions')
    <a href="{{ route('artist-studio.releases.create') }}"
       class="flex items-center gap-2 px-md py-3 rounded-xl bg-primary-container text-on-primary-container font-bold text-label-lg hover:brightness-105 active:scale-95 transition-all shadow-sm">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span class="hidden sm:inline">Create New Release</span>
        <span class="sm:hidden">New</span>
    </a>
@endsection

@section('content')
    @if ($assets->isEmpty())
        <div class="reveal rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
            <div class="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
                <span class="material-symbols-outlined text-[36px]">album</span>
            </div>
            <h3 class="font-headline-md text-headline-md font-bold mb-xs">No releases yet</h3>
            <p class="text-body-md text-on-surface-variant max-w-[28rem] mx-auto mb-lg">
                Upload your first single or album. Add cover artwork, tracks, and a 30-second preview so listeners can sample before they redeem.
            </p>
            <a href="{{ route('artist-studio.releases.create') }}"
               class="inline-flex items-center gap-2 px-lg py-3 rounded-xl bg-primary-container text-on-primary-container font-bold uppercase tracking-widest hover:brightness-105">
                <span class="material-symbols-outlined">add</span> Create your first release
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-gutter">
            @foreach ($assets as $i => $asset)
                @php
                    $percent = $asset->redemption_limit > 0
                        ? min(100, (int) round(($asset->redemptions / $asset->redemption_limit) * 100))
                        : 0;
                    $statusPill = match (strtolower($asset->status)) {
                        'live'      => 'bg-primary/15 text-primary',
                        'scheduled' => 'bg-tertiary-container/30 text-tertiary',
                        default     => 'bg-surface-container-high text-on-surface-variant',
                    };
                @endphp
                <article class="reveal group rounded-xl border border-outline-variant/10 bg-surface-container-low overflow-hidden flex flex-col"
                         style="--reveal-delay: {{ $i * 50 }}ms">
                    <div class="relative aspect-square overflow-hidden bg-surface-container">
                        @if ($asset->cover_url)
                            <img src="{{ $asset->cover_url }}" alt="{{ $asset->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 onerror="this.replaceWith(Object.assign(document.createElement('div'), {className:'w-full h-full flex items-center justify-center bg-surface-container-high text-on-surface-variant', innerHTML:'<span class=\'material-symbols-outlined text-[48px]\'>album</span>'}))">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-[48px]">album</span>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 px-2 py-1 rounded-full text-label-sm font-bold uppercase tracking-widest {{ $statusPill }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </div>
                    <div class="p-md flex-1 flex flex-col gap-sm">
                        <div>
                            <p class="text-label-sm uppercase tracking-widest text-on-surface-variant">
                                {{ ucfirst($asset->release_type ?: 'release') }} · ${{ number_format((float) $asset->price, 2) }}
                            </p>
                            <h3 class="font-title-lg text-title-lg font-bold text-on-surface truncate">{{ $asset->title }}</h3>
                        </div>
                        <div class="flex justify-between text-label-sm text-on-surface-variant">
                            <span>{{ $asset->tracks->count() }} TRACK{{ $asset->tracks->count() === 1 ? '' : 'S' }}</span>
                            <span>{{ number_format($asset->redemptions) }} / {{ number_format($asset->redemption_limit) }}</span>
                        </div>
                        <div class="h-1.5 overflow-hidden rounded-full bg-surface-container-high">
                            <div class="h-full bg-primary rounded-full" style="width: {{ $percent }}%"></div>
                        </div>

                        <div class="mt-auto pt-sm flex items-center gap-xs">
                            <a href="{{ route('artist-studio.releases.edit', $asset) }}"
                               class="flex-1 inline-flex items-center justify-center gap-1 px-md h-9 rounded-lg bg-surface-container-high text-on-surface hover:bg-primary-container hover:text-on-primary-container transition-colors text-label-sm font-bold uppercase tracking-widest">
                                <span class="material-symbols-outlined text-[16px]">edit</span> Edit
                            </a>
                            <form method="POST" action="{{ route('artist-studio.releases.destroy', $asset) }}"
                                  onsubmit="return confirm('Remove “{{ $asset->title }}”? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-surface-container-high text-on-surface-variant hover:bg-error-container/40 hover:text-error transition-colors"
                                        aria-label="Delete release">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
