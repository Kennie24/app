@extends('layouts.artist')

@section('title', $title . ' · Artist Studio')
@section('page-title', $title)
@section('page-subtitle', $subtitle)

@section('content')
    <div class="reveal rounded-2xl border border-dashed border-outline-variant/40 bg-surface-container-low p-xl text-center">
        <div class="mx-auto mb-md flex h-16 w-16 items-center justify-center rounded-full bg-primary/15 text-primary">
            <span class="material-symbols-outlined text-[36px]">{{ $icon ?? 'construction' }}</span>
        </div>
        <span class="text-label-lg uppercase tracking-widest text-primary">{{ $eyebrow ?? 'Coming soon' }}</span>
        <h3 class="font-headline-md text-headline-md font-bold mt-xs">{{ $title }}</h3>
        <p class="text-body-md text-on-surface-variant max-w-[36rem] mx-auto mt-sm">{{ $subtitle }}</p>
        <a href="{{ route('artist-studio.dashboard') }}"
           class="mt-lg inline-flex items-center gap-2 px-lg py-3 rounded-xl bg-primary-container text-on-primary-container font-bold uppercase tracking-widest hover:brightness-105">
            <span class="material-symbols-outlined">arrow_back</span> Back to dashboard
        </a>
    </div>
@endsection
