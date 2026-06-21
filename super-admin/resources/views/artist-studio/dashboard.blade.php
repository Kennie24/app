@extends('layouts.artist')

@section('title', 'Dashboard · Artist Studio')
@section('page-title', 'Welcome back, ' . ($user->artist_name ?: $user->name))
@section('page-subtitle', "Here's what's happening with your discography today.")

@section('actions')
    <a href="{{ route('artist-studio.releases.create') }}"
       class="flex items-center gap-2 px-md py-3 rounded-xl bg-primary-container text-on-primary-container font-bold text-label-lg hover:brightness-105 active:scale-95 transition-all shadow-sm">
        <span class="material-symbols-outlined text-[18px]">add</span>
        <span class="hidden sm:inline">Create New Release</span>
        <span class="sm:hidden">New</span>
    </a>
@endsection

@section('content')
    {{-- Metrics row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
        @foreach ($kpis as $i => $kpi)
            <div class="reveal bg-surface-container-low p-6 rounded-xl border border-outline-variant/10 shadow-lg"
                 style="--reveal-delay: {{ $i * 60 }}ms">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <span class="material-symbols-outlined">{{ $kpi['icon'] }}</span>
                    </div>
                </div>
                <p class="font-label-lg text-label-lg text-on-surface-variant">{{ $kpi['label'] }}</p>
                <p class="font-headline-md text-headline-md font-extrabold text-on-surface mt-xs">{{ $kpi['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Main grid: chart + table on the left, top releases on the right --}}
    <div class="grid grid-cols-12 gap-lg">
        {{-- Left column --}}
        <div class="col-span-12 lg:col-span-8 space-y-lg">
            {{-- Sales performance chart --}}
            <section class="reveal bg-surface-container-low p-md rounded-xl shadow-lg border border-outline-variant/10">
                <div class="flex justify-between items-center mb-base">
                    <h3 class="font-title-lg text-title-lg font-bold text-on-surface">Sales Performance</h3>
                </div>
                <div class="h-64 w-full bg-surface-container-lowest rounded-lg border border-outline-variant/5">
                    <div class="h-full flex flex-col items-center justify-center text-center text-on-surface-variant px-md">
                        <span class="material-symbols-outlined text-[48px] mb-sm">monitoring</span>
                        <p class="font-bold text-on-surface">No sales history yet</p>
                        <p class="mt-xs text-body-md">Performance will appear when dated transaction records are available.</p>
                    </div>
                </div>
            </section>

            {{-- Recent orders --}}
            <section class="reveal bg-surface-container-low rounded-xl shadow-lg border border-outline-variant/10 overflow-hidden">
                <div class="p-md border-b border-outline-variant/10 flex justify-between items-center">
                    <h3 class="font-title-lg text-title-lg font-bold text-on-surface">Recent Orders</h3>
                    <a href="{{ route('artist-studio.releases.index') }}"
                       class="text-primary font-label-lg text-label-lg hover:underline underline-offset-4">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-surface-container-lowest">
                            <tr>
                                <th class="px-md py-4 font-label-lg text-label-lg text-on-surface-variant">Date</th>
                                <th class="px-md py-4 font-label-lg text-label-lg text-on-surface-variant">Customer</th>
                                <th class="px-md py-4 font-label-lg text-label-lg text-on-surface-variant">Item</th>
                                <th class="px-md py-4 font-label-lg text-label-lg text-on-surface-variant">Amount</th>
                                <th class="px-md py-4 font-label-lg text-label-lg text-on-surface-variant text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            <tr>
                                <td colspan="5" class="px-md py-lg text-center text-on-surface-variant">
                                    No order records are available.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Right column: Top releases --}}
        <div class="col-span-12 lg:col-span-4">
            <section class="reveal bg-surface-container-low p-md rounded-xl shadow-lg border border-outline-variant/10 h-full flex flex-col">
                <div class="flex justify-between items-center mb-lg">
                    <h3 class="font-title-lg text-title-lg font-bold text-on-surface">Top Releases</h3>
                    <a href="{{ route('artist-studio.releases.index') }}"
                       class="material-symbols-outlined text-on-surface-variant cursor-pointer hover:text-primary transition-colors">more_horiz</a>
                </div>

                <div class="space-y-gutter flex-1">
                    @forelse ($topReleases as $release)
                        <a href="{{ route('artist-studio.releases.edit', $release['id']) }}"
                           class="flex items-center gap-4 group">
                            <div class="relative w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden border border-outline-variant/10 bg-surface-container">
                                @if ($release['image'])
                                    <img src="{{ $release['image'] }}" alt="{{ $release['title'] }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                         onerror="this.replaceWith(Object.assign(document.createElement('div'), {className:'w-full h-full flex items-center justify-center bg-surface-container-high text-on-surface-variant', innerHTML:'<span class=\'material-symbols-outlined\'>album</span>'}))">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-surface-container-high text-on-surface-variant">
                                        <span class="material-symbols-outlined">album</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-label-lg text-label-lg truncate group-hover:text-primary transition-colors text-on-surface">
                                    {{ $release['title'] }}
                                </p>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $release['sales'] }} Redemptions</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-label-lg text-label-lg text-on-surface">{{ $release['value'] }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="text-center text-on-surface-variant py-lg">
                            <span class="material-symbols-outlined text-[42px] mb-sm">album</span>
                            <p class="text-body-md">No releases yet.</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('artist-studio.releases.create') }}"
                   class="mt-lg p-base bg-surface-container-lowest rounded-xl border border-dashed border-outline-variant block">
                    <span class="w-full py-4 flex flex-col items-center justify-center gap-2 text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-[32px]">add_circle</span>
                        <span class="font-label-lg text-label-lg">Promote New Content</span>
                    </span>
                </a>
            </section>
        </div>
    </div>
@endsection
