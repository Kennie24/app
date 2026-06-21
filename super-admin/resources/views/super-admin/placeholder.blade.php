@extends('layouts.app')

@section('title', $title . ' · Super Admin')
@section('breadcrumb', $breadcrumb)

@section('content')
    <div class="reveal" data-direction="up">
        <div class="flex flex-col items-start gap-sm mb-lg">
            <span class="text-label-md text-secondary uppercase tracking-widest">Super Admin</span>
            <h1 class="text-headline-lg font-bold">{{ $title }}</h1>
            <p class="text-body-lg text-secondary max-w-2xl">
                This module is wired in the router and ready for its UI. Drop your data here when the integration is live.
            </p>
        </div>

        <div class="bento-card rounded-xl px-lg py-xl md:px-xl border border-outline-variant/15 flex flex-col items-center text-center gap-md">
            <span class="material-symbols-outlined text-primary text-[56px]">construction</span>
            <p class="text-headline-md">Coming next</p>
            <p class="text-body-md text-secondary max-w-prose mx-auto leading-relaxed">
                {{ $title }} will surface filters, exports, and detail drawers consistent with the Overview dashboard.
            </p>
            <a href="{{ route('super-admin.dashboard') }}"
               class="mt-sm inline-flex items-center gap-xs px-md py-sm rounded-full bg-primary-container text-on-primary-container font-bold uppercase tracking-widest text-label-md hover:scale-[1.02] transition-transform">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Back to Overview
            </a>
        </div>
    </div>
@endsection
