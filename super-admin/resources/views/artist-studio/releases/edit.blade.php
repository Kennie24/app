@extends('layouts.artist')

@section('title', 'Edit Release · Artist Studio')
@section('page-title', 'Edit ' . $asset->title)
@section('page-subtitle', 'Update artwork, songs, previews, pricing, and release status.')

@section('actions')
    <a href="{{ route('artist-studio.releases.index') }}"
       class="flex items-center gap-2 px-md py-3 rounded-xl border border-outline-variant text-on-surface font-label-lg hover:bg-surface-container transition-colors">
        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
        Back
    </a>
@endsection

@section('content')
    @include('artist-studio.releases._form', [
        'action' => route('artist-studio.releases.update', $asset),
        'method' => 'PUT',
        'submitLabel' => 'Save changes',
    ])
@endsection
