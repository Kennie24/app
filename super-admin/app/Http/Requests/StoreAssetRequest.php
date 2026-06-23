<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // gate via middleware later if needed
    }

    public function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'max:255'],
            'artist'           => ['required', 'string', 'max:255'],
            'price'            => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'redemption_limit' => ['required', 'integer', 'min:1', 'max:1000000'],
            'status'           => ['required', Rule::in(['live', 'scheduled', 'archived'])],
            'release_type'     => ['nullable', Rule::in(Asset::RELEASE_TYPES)],
            'description'      => ['nullable', 'string', 'max:2000'],
            'cover'            => [
                $this->isMethod('post') ? 'nullable' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120', // 5 MB
            ],
            'cover_url'        => ['nullable', 'url', 'max:2048'],
            'tracks'           => ['required', 'array', 'min:1', 'max:30'],
            'tracks.*.id'      => ['nullable', 'integer', 'exists:asset_tracks,id'],
            'tracks.*.title'   => ['required', 'string', 'max:255'],
            'tracks.*.audio'   => ['nullable', 'file', 'mimes:mp3,wav,flac,m4a,mp4,aac,ogg', 'max:1048576'],
            'tracks.*.preview' => ['nullable', 'file', 'mimes:mp3,wav,m4a,mp4,aac,ogg', 'max:1048576'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $tracks = $this->input('tracks', []);

                if ($this->input('release_type', 'single') === 'single' && count($tracks) !== 1) {
                    $validator->errors()->add('tracks', 'A single must contain exactly one track. Choose EP or Album to add more.');
                }

                foreach ($tracks as $index => $track) {
                    $isExisting = ! empty($track['id']);

                    if (! $isExisting && ! $this->hasFile("tracks.$index.audio")) {
                        $validator->errors()->add("tracks.$index.audio", 'Upload the full song file.');
                    }

                    if (! $isExisting && ! $this->hasFile("tracks.$index.preview")) {
                        $validator->errors()->add("tracks.$index.preview", 'Upload a preview file.');
                    }
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'cover.max' => 'Cover image must be 5 MB or smaller.',
            'tracks.required' => 'Add at least one track.',
            'tracks.*.audio.max' => 'Full song files must be 1 GB or smaller.',
            'tracks.*.preview.max' => 'Preview files must be 1 GB or smaller.',
        ];
    }
}
