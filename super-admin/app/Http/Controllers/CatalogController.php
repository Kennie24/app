<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    /**
     * Public list of live releases for the fan-facing store / library.
     */
    public function index(): JsonResponse
    {
        $releases = Asset::query()
            ->with('user:id,name,artist_name')
            ->withCount('tracks')
            ->where('status', 'live')
            ->whereHas('user', fn ($query) => $query->where('is_artist', true))
            ->latest()
            ->get()
            ->map(fn (Asset $a) => $this->summary($a));

        return response()->json(['releases' => $releases]);
    }

    /**
     * Single release detail, addressable by numeric id or slug.
     */
    public function show(string $key): JsonResponse
    {
        $asset = Asset::with(['tracks', 'user:id,name,artist_name'])
            ->where('status', 'live')
            ->whereHas('user', fn ($query) => $query->where('is_artist', true))
            ->where(function ($q) use ($key) {
                $q->where('id', is_numeric($key) ? (int) $key : 0)
                  ->orWhere('slug', $key);
            })
            ->firstOrFail();

        $related = Asset::query()
            ->with('user:id,name,artist_name')
            ->withCount('tracks')
            ->where('status', 'live')
            ->whereHas('user', fn ($query) => $query->where('is_artist', true))
            ->where('id', '!=', $asset->id)
            ->where(function ($q) use ($asset) {
                $q->where('user_id', $asset->user_id)
                  ->orWhere('release_type', $asset->release_type);
            })
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (Asset $a) => $this->summary($a));

        return response()->json([
            'release' => array_merge($this->summary($asset), [
                'description' => $asset->description,
                'tracks' => $asset->tracks->map(fn ($t) => [
                    'id'           => (string) $t->id,
                    'position'     => (int) $t->position,
                    'title'        => $t->title,
                    'duration'     => $this->formatDuration($t),
                    // Never expose the full purchased master in the public catalog.
                    'audio_url'    => $t->preview_url,
                    'sample_plays' => (int) $t->sample_plays,
                    'popular'      => (int) $t->sample_plays > 100,
                ])->values(),
            ]),
            'related' => $related,
        ]);
    }

    private function summary(Asset $asset): array
    {
        return [
            'id'           => (string) $asset->id,
            'slug'         => $asset->slug,
            'title'        => $asset->title,
            'artist'       => $asset->user?->artist_name ?: $asset->user?->name ?: $asset->artist,
            'type'         => $asset->release_type === 'album' ? 'Album' : 'Single',
            'price'        => number_format((float) $asset->price, 2, '.', ''),
            'image'        => $asset->cover_url,
            'release_date' => optional($asset->created_at)->format('M j, Y'),
            'track_count'  => ($asset->tracks_count ?? $asset->tracks->count()) ?: null,
        ];
    }

    private function formatDuration($track): string
    {
        // Fallback when we don't yet store a duration column.
        $seconds = (int) ($track->duration_seconds ?? 0);
        if ($seconds <= 0) return '—';
        return sprintf('%d:%02d', intdiv($seconds, 60), $seconds % 60);
    }
}
