{{-- Shared create + edit form. Expects: $asset (model), $action (url), $method (POST|PUT), $submitLabel --}}
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
        <div class="reveal bento-card rounded-xl border border-outline-variant/10 p-lg xl:col-span-1">
            <div class="flex items-center gap-sm mb-md">
                <span class="material-symbols-outlined text-primary">image</span>
                <h2 class="text-headline-md font-bold">Cover</h2>
            </div>

            <div class="relative aspect-square w-full rounded-xl overflow-hidden bg-surface-container-high border border-outline-variant/15 mb-md" id="cover-preview-wrap">
                @if ($asset->cover_url)
                    <img id="cover-preview" src="{{ $asset->cover_url }}" alt="" class="absolute inset-0 w-full h-full object-cover" />
                @else
                    <div id="cover-placeholder" class="absolute inset-0 flex flex-col items-center justify-center gap-xs text-secondary">
                        <span class="material-symbols-outlined text-[56px] opacity-60">add_photo_alternate</span>
                        <span class="text-label-sm">No image yet</span>
                    </div>
                    <img id="cover-preview" alt="" class="absolute inset-0 w-full h-full object-cover hidden" />
                @endif
            </div>

            <label class="flex flex-col gap-xs">
                <span class="text-label-md uppercase tracking-widest text-secondary">Upload Image</span>
                <input id="cover-input" type="file" name="cover" accept="image/jpeg,image/png,image/webp"
                       class="text-body-md text-on-surface file:mr-sm file:py-xs file:px-md file:rounded-full file:border-0 file:bg-primary-container file:text-on-primary-container file:font-bold file:uppercase file:tracking-widest file:text-label-sm hover:file:scale-[1.02]" />
                <span class="text-label-sm text-secondary">JPG, PNG, or WEBP · up to 5 MB</span>
                <span id="cover-client-error" class="hidden text-label-sm text-error font-bold"></span>
            </label>

            <div class="my-md flex items-center gap-sm">
                <div class="h-px bg-outline-variant/30 flex-1"></div>
                <span class="text-label-sm text-secondary uppercase tracking-widest">or</span>
                <div class="h-px bg-outline-variant/30 flex-1"></div>
            </div>

            <label class="flex flex-col gap-xs">
                <span class="text-label-md uppercase tracking-widest text-secondary">Cover URL</span>
                <input type="url" name="cover_url" placeholder="https://…"
                       value="{{ old('cover_url', str_starts_with((string) $asset->cover_path, 'http') ? $asset->cover_path : '') }}"
                       class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
            </label>
        </div>

        {{-- Details --}}
        <div class="reveal xl:col-span-2 space-y-lg" data-direction="left">
            <div class="bento-card rounded-xl border border-outline-variant/10 p-lg">
                <div class="flex items-center gap-sm mb-md">
                    <span class="material-symbols-outlined text-primary">album</span>
                    <h2 class="text-headline-md font-bold">Details</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-md">
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Title <span class="text-error">*</span></span>
                        <input type="text" name="title" required value="{{ old('title', $asset->title) }}"
                               placeholder="Synthetic Horizons"
                               class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Artist <span class="text-error">*</span></span>
                        <input type="text" name="artist" required value="{{ old('artist', $asset->artist) }}"
                               placeholder="Cyber Echoes"
                               class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Price (USD) <span class="text-error">*</span></span>
                        <div class="flex items-center bg-surface-container-high rounded-lg pl-md focus-within:ring-1 focus-within:ring-primary">
                            <span class="text-secondary">$</span>
                            <input type="number" name="price" step="0.01" min="0" max="9999.99" required
                                   value="{{ old('price', number_format((float) $asset->price, 2, '.', '')) }}"
                                   placeholder="9.99"
                                   class="bg-transparent flex-1 px-sm py-sm text-body-md text-on-surface outline-none" />
                        </div>
                    </label>
                    <label class="flex flex-col gap-xs">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Redemption Limit <span class="text-error">*</span></span>
                        <input type="number" name="redemption_limit" min="1" max="1000000" required
                               value="{{ old('redemption_limit', $asset->redemption_limit ?: 1000) }}"
                               placeholder="10000"
                               class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary" />
                    </label>
                    <label class="flex flex-col gap-xs md:col-span-2">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Status <span class="text-error">*</span></span>
                        <div class="bg-surface-container-high rounded-full p-1 flex gap-1 w-fit">
                            @foreach (['live' => 'Live', 'scheduled' => 'Scheduled', 'archived' => 'Archived'] as $value => $label)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="{{ $value }}" class="peer sr-only" {{ old('status', $asset->status) === $value ? 'checked' : '' }} />
                                    <span class="px-md py-xs rounded-full text-label-md uppercase tracking-widest inline-block text-secondary peer-checked:bg-primary-container peer-checked:text-on-primary-container transition">
                                        {{ $label }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </label>
                    <label class="flex flex-col gap-xs md:col-span-2">
                        <span class="text-label-md uppercase tracking-widest text-secondary">Description</span>
                        <textarea name="description" rows="3" maxlength="2000" placeholder="Optional notes shown on the asset page…"
                                  class="bg-surface-container-high rounded-lg px-md py-sm text-body-md text-on-surface outline-none focus:ring-1 focus:ring-primary resize-y">{{ old('description', $asset->description) }}</textarea>
                    </label>
                </div>
            </div>

            {{-- Submit bar --}}
            <div class="flex items-center justify-end gap-sm">
                <a href="{{ route('super-admin.assets.index') }}"
                   class="px-md h-10 inline-flex items-center rounded-full text-secondary hover:text-on-surface text-label-md uppercase tracking-widest">Cancel</a>
                <button type="submit"
                        class="flex items-center gap-xs px-lg h-10 rounded-full bg-primary-container text-on-primary-container font-bold text-label-md uppercase tracking-widest hover:scale-[1.02] transition">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    {{ $submitLabel ?? 'Save Asset' }}
                </button>
            </div>
        </div>
    </div>
</form>

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
    })();
</script>
