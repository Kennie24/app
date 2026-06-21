@extends('layouts.app')

@section('title', 'New Asset · Super Admin')
@section('breadcrumb', 'Assets · New')

@section('content')
    <div class="reveal mb-lg flex items-center gap-sm">
        <a href="{{ route('super-admin.assets.index') }}"
           class="w-10 h-10 rounded-full bg-surface-container hover:bg-surface-container-high flex items-center justify-center"
           aria-label="Back to assets">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <span class="text-label-md text-secondary uppercase tracking-widest">Super Admin · Assets</span>
            <h1 class="text-headline-lg font-bold">New Asset</h1>
        </div>
    </div>

    @include('super-admin.assets._form', [
        'asset'       => $asset,
        'action'      => route('super-admin.assets.store'),
        'method'      => 'POST',
        'submitLabel' => 'Create Asset',
    ])
@endsection
