{{-- Shared release form for Artist Studio. Expects $asset, $action, $method, $submitLabel --}}
@php
    $trackRows = old('tracks');

    if (! is_array($trackRows)) {
        $trackRows = $asset->exists
            ? $asset->tracks->map(fn ($track) => [
                'id' => $track->id,
                'title' => $track->title,
                'audio_url' => $track->audio_url,
                'preview_url' => $track->preview_url,
            ])->values()->all()
            : [];
    }

    if (count($trackRows) === 0) {
        $trackRows = [['id' => null, 'title' => '']];
    }
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-lg">
    @csrf
    @if (($method ?? 'POST') !== 'POST')
        @method($method)
    @endif

    @if ($errors->any())
        <div class="reveal flex items-start gap-sm rounded-xl border border-error/40 bg-error-container/20 p-md text-error">
            <span class="material-symbols-outlined">error</span>
            <div>
                <p class="font-bold mb-xs">Please fix the following:</p>
                <ul class="list-disc list-inside text-label-sm space-y-xs">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-lg">
        {{-- Cover --}}
        <div class="reveal rounded-xl border border-outline-variant/10 bg-surface-container-low p-lg xl:col-span-1">
            <div class="flex items-center gap-sm mb-md">
                <span class="material-symbols-outlined text-primary">image</span>
                <h2 class="font-title-lg text-title-lg font-bold">Cover</h2>
            </div>

            <div class="relative aspect-square w-full rounded-xl overflow-hidden bg-surface-container-high border border-outline-variant/15 mb-md">
                @if ($asset->cover_url)
                    <img id="cover-preview" src="{{ $asset->cover_url }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                @else
                    <div id="cover-placeholder" class="absolute inset-0 flex flex-col items-center justify-center gap-xs text-on-surface-variant">
                        <span class="material-symbols-outlined text-[56px] opacity-60">add_photo_alternate</span>
                        <span class="text-label-sm">No image yet</span>
                    </div>
                    <img id="cover-preview" alt="" class="absolute inset-0 w-full h-full object-cover hidden" />
                @endif
            </div>

            <label class="flex flex-col gap-xs">
                <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Upload Image</span>
                <input id="cover-input" type="file" name="cover" accept="image/jpeg,image/png,image/webp"
                       class="text-body-md text-on-surface file:mr-sm file:py-xs file:px-md file:rounded-full file:border-0 file:bg-primary-container file:text-on-primary-container file:font-bold file:uppercase file:tracking-widest file:text-label-sm hover:file:scale-[1.02]" />
                <span class="text-label-sm text-on-surface-variant">JPG, PNG, or WEBP · up to 5 MB</span>
                <span id="cover-client-error" class="hidden text-label-sm text-error font-bold"></span>
            </label>

            <div class="my-md flex items-center gap-sm">
                <div class="h-px bg-outline-variant/30 flex-1"></div>
                <span class="text-label-sm text-on-surface-variant uppercase tracking-widest">or</span>
                <div class="h-px bg-outline-variant/30 flex-1"></div>
            </div>

            <label class="flex flex-col gap-xs">
                <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Cover URL</span>
                <input type="url" name="cover_url" placeholder="https://…"
                       value="{{ old('cover_url', str_starts_with((string) $asset->cover_path, 'http') ? $asset->cover_path : '') }}"
                       class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
            </label>
        </div>

        {{-- Details --}}
        <div class="reveal xl:col-span-2 space-y-lg">
            <div class="rounded-xl border border-outline-variant/10 bg-surface-container-low p-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-primary">album</span>
                    <h2 class="font-title-lg text-title-lg font-bold">Details</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Title <span class="text-error">*</span></span>
                        <input type="text" name="title" required value="{{ old('title', $asset->title) }}"
                               placeholder="Midnight Echoes"
                               class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Artist <span class="text-error">*</span></span>
                        <input type="text" name="artist" required
                               value="{{ old('artist', $asset->artist ?: (auth()->user()->artist_name ?: auth()->user()->name)) }}"
                               placeholder="Your name"
                               class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Price (USD) <span class="text-error">*</span></span>
                        <div class="flex items-center bg-surface-container-high rounded-lg pl-md focus-within:ring-1 focus-within:ring-primary">
                            <span class="text-on-surface-variant">$</span>
                            <input type="number" name="price" step="0.01" min="0" max="9999.99" required
                                   value="{{ old('price', number_format((float) $asset->price, 2, '.', '')) }}"
                                   placeholder="9.99"
                                   class="bg-transparent flex-1 px-sm py-sm text-body-md text-on-surface outline-none" />
                        </div>
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Redemption Limit <span class="text-error">*</span></span>
                        <input type="number" name="redemption_limit" min="1" max="1000000" required
                               value="{{ old('redemption_limit', $asset->redemption_limit ?: 1000) }}"
                               placeholder="1000"
                               class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs md:col-span-2">
                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Release Type</span>
                        <div class="bg-surface-container-high rounded-full p-1 flex gap-1 w-fit">
                            @foreach (['single' => 'Single', 'ep' => 'EP', 'album' => 'Album'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="release_type" value="{{ $value }}" class="peer sr-only"
                                           {{ old('release_type', $asset->release_type ?? 'single') === $value ? 'checked' : '' }} />
                                    <span class="px-md py-xs rounded-full text-label-sm uppercase tracking-widest inline-block text-on-surface-variant peer-checked:bg-primary-container peer-checked:text-on-primary-container peer-checked:font-bold transition">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </label>
                    <label class="flex flex-col gap-xs md:col-span-2">
                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Status <span class="text-error">*</span></span>
                        <div class="bg-surface-container-high rounded-full p-1 flex gap-1 w-fit">
                            @foreach (['live' => 'Live', 'scheduled' => 'Scheduled', 'archived' => 'Archived'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="{{ $value }}" class="peer sr-only" {{ old('status', $asset->status) === $value ? 'checked' : '' }} />
                                    <span class="px-md py-xs rounded-full text-label-sm uppercase tracking-widest inline-block text-on-surface-variant peer-checked:bg-primary-container peer-checked:text-on-primary-container peer-checked:font-bold transition">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </label>
                    <label class="flex flex-col gap-xs md:col-span-2">
                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">Description</span>
                        <textarea name="description" rows="3" maxlength="2000" placeholder="Optional notes shown on the release page…"
                                  class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary resize-y">{{ old('description', $asset->description) }}</textarea>
                    </label>
                </div>
            </div>

            <div class="rounded-xl border border-outline-variant/10 bg-surface-container-low p-lg">
                <div class="flex flex-wrap items-start justify-between gap-sm mb-md">
                    <div class="flex items-center gap-sm">
                        <span class="material-symbols-outlined text-primary">audio_file</span>
                        <div>
                            <h2 class="font-title-lg text-title-lg font-bold">Tracks & previews</h2>
                            <p class="text-label-sm text-on-surface-variant mt-xs">
                                Upload the full song and a shorter preview for each track.
                            </p>
                        </div>
                    </div>
                    <button id="add-track" type="button"
                            class="inline-flex items-center gap-xs px-md h-9 rounded-full border border-outline-variant text-on-surface hover:border-primary hover:text-primary text-label-sm font-bold uppercase tracking-widest transition-colors">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                        Add track
                    </button>
                </div>

                <div id="track-list" class="space-y-md">
                    @foreach ($trackRows as $index => $track)
                        @php
                            $existingTrack = ! empty($track['id'])
                                ? $asset->tracks->firstWhere('id', (int) $track['id'])
                                : null;
                            $audioUrl = $existingTrack?->audio_url ?? ($track['audio_url'] ?? null);
                            $previewUrl = $existingTrack?->preview_url ?? ($track['preview_url'] ?? null);
                        @endphp
                        <div class="track-row rounded-xl border border-outline-variant/15 bg-surface-container-lowest p-md"
                             data-track-index="{{ $index }}">
                            <input type="hidden" name="tracks[{{ $index }}][id]" value="{{ $track['id'] ?? '' }}">

                            <div class="flex items-center justify-between gap-sm mb-md">
                                <p class="track-number font-bold text-on-surface">Track {{ $index + 1 }}</p>
                                <button type="button"
                                        class="remove-track inline-flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-error-container/30 hover:text-error transition-colors"
                                        aria-label="Remove track">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 gap-md">
                                <label class="flex flex-col gap-xs">
                                    <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">
                                        Track title <span class="text-error">*</span>
                                    </span>
                                    <input type="text" name="tracks[{{ $index }}][title]" required
                                           value="{{ $track['title'] ?? '' }}"
                                           placeholder="Song title"
                                           class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary">
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                                    <label class="flex flex-col gap-xs">
                                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">
                                            Full song {!! $existingTrack ? '' : '<span class="text-error">*</span>' !!}
                                        </span>
                                        <input type="file" name="tracks[{{ $index }}][audio]"
                                               accept=".mp3,.wav,.flac,.m4a,.mp4,.aac,.ogg,audio/*"
                                               {{ $existingTrack ? '' : 'required' }}
                                               class="track-file text-body-md text-on-surface file:mr-sm file:py-xs file:px-md file:rounded-full file:border-0 file:bg-primary-container file:text-on-primary-container file:font-bold file:text-label-sm">
                                        <span class="text-label-sm text-on-surface-variant">
                                            MP3, WAV, FLAC, M4A, AAC, or OGG · up to 25 MB
                                        </span>
                                        @if ($audioUrl)
                                            <audio controls preload="none" class="mt-xs h-10 w-full" src="{{ $audioUrl }}"></audio>
                                            <span class="text-label-sm text-primary">Current full song will be kept unless replaced.</span>
                                        @endif
                                    </label>

                                    <label class="flex flex-col gap-xs">
                                        <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">
                                            Preview {!! $existingTrack ? '' : '<span class="text-error">*</span>' !!}
                                        </span>
                                        <input type="file" name="tracks[{{ $index }}][preview]"
                                               accept=".mp3,.wav,.m4a,.mp4,.aac,.ogg,audio/*"
                                               {{ $existingTrack ? '' : 'required' }}
                                               class="track-file text-body-md text-on-surface file:mr-sm file:py-xs file:px-md file:rounded-full file:border-0 file:bg-primary-container file:text-on-primary-container file:font-bold file:text-label-sm">
                                        <span class="text-label-sm text-on-surface-variant">
                                            Short audio preview · up to 10 MB
                                        </span>
                                        @if ($previewUrl)
                                            <audio controls preload="none" class="mt-xs h-10 w-full" src="{{ $previewUrl }}"></audio>
                                            <span class="text-label-sm text-primary">Current preview will be kept unless replaced.</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p id="single-track-note" class="hidden mt-md text-label-sm text-tertiary">
                    Singles can contain only one track. Choose EP or Album to add more.
                </p>
            </div>

            <div class="flex items-center justify-end gap-sm">
                <a href="{{ route('artist-studio.releases.index') }}"
                   class="px-md h-10 inline-flex items-center rounded-full text-on-surface-variant hover:text-on-surface text-label-md uppercase tracking-widest">Cancel</a>
                <button type="submit"
                        class="flex items-center gap-xs px-lg h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    {{ $submitLabel ?? 'Save release' }}
                </button>
            </div>
        </div>
    </div>
</form>

<template id="track-template">
    <div class="track-row rounded-xl border border-outline-variant/15 bg-surface-container-lowest p-md" data-track-index="__INDEX__">
        <input type="hidden" name="tracks[__INDEX__][id]" value="">
        <div class="flex items-center justify-between gap-sm mb-md">
            <p class="track-number font-bold text-on-surface">Track __NUMBER__</p>
            <button type="button"
                    class="remove-track inline-flex h-8 w-8 items-center justify-center rounded-full text-on-surface-variant hover:bg-error-container/30 hover:text-error transition-colors"
                    aria-label="Remove track">
                <span class="material-symbols-outlined text-[18px]">delete</span>
            </button>
        </div>
        <div class="grid grid-cols-1 gap-md">
            <label class="flex flex-col gap-xs">
                <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">
                    Track title <span class="text-error">*</span>
                </span>
                <input type="text" name="tracks[__INDEX__][title]" required placeholder="Song title"
                       class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary">
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                <label class="flex flex-col gap-xs">
                    <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">
                        Full song <span class="text-error">*</span>
                    </span>
                    <input type="file" name="tracks[__INDEX__][audio]" required
                           accept=".mp3,.wav,.flac,.m4a,.mp4,.aac,.ogg,audio/*"
                           class="track-file text-body-md text-on-surface file:mr-sm file:py-xs file:px-md file:rounded-full file:border-0 file:bg-primary-container file:text-on-primary-container file:font-bold file:text-label-sm">
                    <span class="text-label-sm text-on-surface-variant">Up to 25 MB</span>
                </label>
                <label class="flex flex-col gap-xs">
                    <span class="text-label-lg uppercase tracking-widest text-on-surface-variant">
                        Preview <span class="text-error">*</span>
                    </span>
                    <input type="file" name="tracks[__INDEX__][preview]" required
                           accept=".mp3,.wav,.m4a,.mp4,.aac,.ogg,audio/*"
                           class="track-file text-body-md text-on-surface file:mr-sm file:py-xs file:px-md file:rounded-full file:border-0 file:bg-primary-container file:text-on-primary-container file:font-bold file:text-label-sm">
                    <span class="text-label-sm text-on-surface-variant">Short preview · up to 10 MB</span>
                </label>
            </div>
        </div>
    </div>
</template>

<script>
    (() => {
        const input = document.getElementById('cover-input');
        const preview = document.getElementById('cover-preview');
        const placeholder = document.getElementById('cover-placeholder');
        if (!input || !preview) return;
        input.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            const error = document.getElementById('cover-client-error');
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                input.value = '';
                if (error) {
                    error.textContent = 'This image is too large. Choose a file that is 5 MB or smaller.';
                    error.classList.remove('hidden');
                }
                return;
            }
            error?.classList.add('hidden');
            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.classList.remove('hidden');
            placeholder?.classList.add('hidden');
        });

        const trackList = document.getElementById('track-list');
        const addTrack = document.getElementById('add-track');
        const template = document.getElementById('track-template');
        const singleNote = document.getElementById('single-track-note');

        const releaseType = () => document.querySelector('input[name="release_type"]:checked')?.value ?? 'single';

        const renumberTracks = () => {
            trackList?.querySelectorAll('.track-row').forEach((row, index) => {
                row.dataset.trackIndex = String(index);
                const number = row.querySelector('.track-number');
                if (number) number.textContent = `Track ${index + 1}`;

                row.querySelectorAll('[name]').forEach((field) => {
                    field.name = field.name.replace(/tracks\[\d+\]/, `tracks[${index}]`);
                });
            });

            const isSingle = releaseType() === 'single';
            const hasMultiple = (trackList?.querySelectorAll('.track-row').length ?? 0) > 1;
            singleNote?.classList.toggle('hidden', !(isSingle && hasMultiple));
        };

        addTrack?.addEventListener('click', () => {
            if (!trackList || !template) return;
            if (releaseType() === 'single') {
                singleNote?.classList.remove('hidden');
                return;
            }

            const index = trackList.querySelectorAll('.track-row').length;
            const html = template.innerHTML
                .replaceAll('__INDEX__', String(index))
                .replaceAll('__NUMBER__', String(index + 1));
            trackList.insertAdjacentHTML('beforeend', html);
            renumberTracks();
        });

        trackList?.addEventListener('click', (event) => {
            const button = event.target.closest('.remove-track');
            if (!button) return;

            const rows = trackList.querySelectorAll('.track-row');
            if (rows.length === 1) return;
            button.closest('.track-row')?.remove();
            renumberTracks();
        });

        document.querySelectorAll('input[name="release_type"]').forEach((radio) => {
            radio.addEventListener('change', renumberTracks);
        });

        renumberTracks();
    })();
</script>
