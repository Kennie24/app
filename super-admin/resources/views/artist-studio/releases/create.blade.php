@extends('layouts.artist')

@section('title', 'New Release · Artist Studio')
@section('page-title', 'Create a new release')
@section('page-subtitle', 'Upload artwork, full songs, and preview audio for a single or album.')

@section('actions')
    <a href="{{ route('artist-studio.releases.index') }}"
       class="flex items-center gap-2 px-md py-3 rounded-xl border border-outline-variant text-on-surface font-label-lg hover:bg-surface-container transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back
    </a>
@endsection

@section('content')
    @include('artist-studio.releases._form', [
        'action' => route('artist-studio.releases.store'),
        'method' => 'POST',
        'submitLabel' => 'Create release',
    ])
@endsection
