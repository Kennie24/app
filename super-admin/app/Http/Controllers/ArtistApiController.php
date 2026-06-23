<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ArtistApiController extends Controller
{
    public function checkEmail(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! $user->is_artist) {
            return response()->json(['message' => 'No artist account was found with that email address.'], 404);
        }

        return response()->json(['name' => $user->artist_name ?: $user->name]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ]);

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], (bool) ($credentials['remember'] ?? false))) {
            return response()->json(['message' => 'The email or password is incorrect.'], 422);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if (! $user || (! $user->is_artist && ! $user->is_super_admin)) {
            Auth::logout();
            $request->session()->invalidate();

            return response()->json(['message' => 'This account does not have artist access.'], 403);
        }

        return response()->json(['user' => $this->userData($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Signed out.']);
    }

    public function me(Request $request): JsonResponse
    {
        $this->ensureArtist($request);

        return response()->json(['user' => $this->userData($request->user())]);
    }

    public function releases(Request $request): JsonResponse
    {
        $this->ensureArtist($request);

        $assets = Asset::query()
            ->with('tracks')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Asset $asset) => $this->releaseData($asset));

        return response()->json(['releases' => $assets]);
    }

    public function storeRelease(Request $request): JsonResponse
    {
        $this->ensureArtist($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'release_type' => ['required', Rule::in(Asset::RELEASE_TYPES)],
            'price' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'status' => ['required', Rule::in(['live', 'scheduled'])],
            'cover' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'track_titles' => ['required', 'array', 'min:1', 'max:30'],
            'track_titles.*' => ['required', 'string', 'max:255'],
            'tracks' => ['required', 'array', 'min:1', 'max:30'],
            'tracks.*' => ['required', 'file', 'mimes:mp3,wav,flac,m4a,mp4,aac,ogg', 'max:1048576'],
            'previews' => ['sometimes', 'array', 'max:30'],
            'previews.*' => ['nullable', 'file', 'mimes:mp3,wav,flac,m4a,aac,ogg', 'max:1048576'],
        ]);

        if (count($validated['track_titles']) !== count($validated['tracks'])) {
            return response()->json(['message' => 'Every audio file must have one matching track title.'], 422);
        }

        if ($validated['release_type'] === 'single' && count($validated['tracks']) !== 1) {
            return response()->json(['message' => 'A single must contain exactly one track.'], 422);
        }

        $coverPath = $request->file('cover')->store('covers', 'public');
        $audioPaths = [];

        try {
            $asset = DB::transaction(function () use ($request, $validated, $coverPath, &$audioPaths) {
                $user = $request->user();
                $asset = Asset::create([
                    'user_id' => $user->id,
                    'title' => $validated['title'],
                    'artist' => $user->artist_name ?: $user->name,
                    'release_type' => $validated['release_type'],
                    'price' => $validated['price'],
                    'redemption_limit' => 10000,
                    'status' => $validated['status'],
                    'cover_path' => $coverPath,
                ]);

                $previewFiles = $request->file('previews') ?? [];

                foreach ($request->file('tracks') as $index => $audio) {
                    $audioPath = $audio->store('audio/'.$asset->id, 'public');
                    $audioPaths[] = $audioPath;

                    $previewPath = null;
                    if (! empty($previewFiles[$index])) {
                        $previewPath = $previewFiles[$index]->store('previews/'.$asset->id, 'public');
                        $audioPaths[] = $previewPath;
                    }

                    $asset->tracks()->create([
                        'title' => $validated['track_titles'][$index],
                        'audio_path' => $audioPath,
                        'preview_path' => $previewPath,
                        'position' => $index + 1,
                    ]);
                }

                return $asset->load('tracks');
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($coverPath);
            Storage::disk('public')->delete($audioPaths);
            throw $exception;
        }

        return response()->json([
            'message' => 'Release saved successfully.',
            'release' => $this->releaseData($asset),
        ], 201);
    }

    public function samplePlayed(Request $request, Asset $asset, int $track): JsonResponse
    {
        $this->ensureArtist($request);
        abort_unless($asset->user_id === $request->user()->id, 403);
        $assetTrack = $asset->tracks()->findOrFail($track);
        $assetTrack->increment('sample_plays');

        return response()->json(['sample_plays' => $assetTrack->fresh()->sample_plays]);
    }

    private function ensureArtist(Request $request): void
    {
        $user = $request->user();
        abort_unless($user && ($user->is_artist || $user->is_super_admin), 403);
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'artist_name' => $user->artist_name ?: $user->name,
        ];
    }

    private function releaseData(Asset $asset): array
    {
        return [
            'id' => (string) $asset->id,
            'title' => $asset->title,
            'type' => $asset->release_type_label,
            'status' => $asset->status === 'live' ? 'Live' : 'Draft',
            'price' => number_format((float) $asset->price, 2, '.', ''),
            'image' => $asset->cover_url,
            'redemptions' => (int) ($asset->redemptions ?? 0),
            'limit' => (int) $asset->redemption_limit,
            'tracks' => $asset->tracks->map(fn ($track) => [
                'id' => (string) $track->id,
                'title' => $track->title,
                'fileName' => basename($track->audio_path),
                'audioUrl' => $track->audio_url,
                'previewUrl' => $track->preview_url,
                'samplePlays' => $track->sample_plays,
            ])->values(),
        ];
    }
}
