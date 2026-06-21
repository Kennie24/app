<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Models\Asset;
use App\Models\AssetTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ArtistStudioController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && (Auth::user()->is_artist || Auth::user()->is_super_admin)) {
            return redirect()->route('artist-studio.dashboard');
        }

        return view('artist-studio.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes'],
        ]);

        if (! Auth::attempt(
            ['email' => $credentials['email'], 'password' => $credentials['password']],
            (bool) ($credentials['remember'] ?? false),
        )) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'The email or password is incorrect.']);
        }

        $user = $request->user();
        if (! $user->is_artist && ! $user->is_super_admin) {
            Auth::logout();
            $request->session()->invalidate();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'This account does not have artist access.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('artist-studio.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('artist-studio.login');
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();

        $assets = Asset::with('tracks')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $totalRedemptions = (int) $assets->sum('redemptions');
        $totalTracks = (int) $assets->sum(fn ($a) => $a->tracks->count());
        $samplePlays = (int) $assets->sum(fn ($a) => $a->tracks->sum('sample_plays'));
        $totalRevenue = $assets->sum(fn ($a) => ((float) $a->price) * ((int) $a->redemptions));

        $kpis = [
            [
                'label' => 'Total Revenue',
                'value' => '$'.number_format($totalRevenue, 0),
                'icon'  => 'payments',
            ],
            [
                'label' => 'Preview Plays',
                'value' => number_format($samplePlays),
                'icon'  => 'headphones',
            ],
            [
                'label' => 'Code Redemptions',
                'value' => number_format($totalRedemptions),
                'icon'  => 'qr_code_scanner',
            ],
            [
                'label' => 'Conversion Rate',
                'value' => $samplePlays > 0
                    ? number_format(($totalRedemptions / $samplePlays) * 100, 1).'%'
                    : '—',
                'icon'  => 'conversion_path',
            ],
        ];

        // Top releases — derived from artist's own assets
        $topReleases = $assets
            ->sortByDesc('redemptions')
            ->take(4)
            ->values()
            ->map(fn (Asset $a) => [
                'id'    => $a->id,
                'title' => $a->title,
                'sales' => (int) $a->redemptions,
                'image' => $a->cover_url,
                'value' => '$'.number_format(((float) $a->price) * ((int) $a->redemptions), 0),
            ]);

        return view('artist-studio.dashboard', compact(
            'user', 'assets', 'kpis', 'topReleases'
        ));
    }

    public function releases(Request $request)
    {
        $user = $request->user();

        $assets = Asset::with('tracks')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('artist-studio.releases.index', compact('assets'));
    }

    public function createRelease()
    {
        $asset = new Asset([
            'price' => 9.99,
            'redemption_limit' => 1000,
            'status' => 'scheduled',
        ]);

        return view('artist-studio.releases.create', compact('asset'));
    }

    public function storeRelease(StoreAssetRequest $request)
    {
        $data = $this->releaseData($request);
        $data['user_id'] = $request->user()->id;
        $data['artist'] = $request->user()->artist_name ?: $request->user()->name;
        $data['cover_path'] = $this->resolveCover($request, null);

        $storedPaths = [];

        try {
            $asset = DB::transaction(function () use ($request, $data, &$storedPaths) {
                $asset = Asset::create($data);
                $obsoletePaths = [];
                $this->persistTracks($request, $asset, $storedPaths, $obsoletePaths);

                return $asset;
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }

        return redirect()
            ->route('artist-studio.releases.index')
            ->with('flash', 'Release “'.$asset->title.'” created.');
    }

    public function editRelease(Request $request, Asset $asset)
    {
        $this->authorizeAsset($request, $asset);

        return view('artist-studio.releases.edit', compact('asset'));
    }

    public function updateRelease(StoreAssetRequest $request, Asset $asset)
    {
        $this->authorizeAsset($request, $asset);

        $data = $this->releaseData($request);
        $data['cover_path'] = $this->resolveCover($request, $asset);

        $storedPaths = [];
        $obsoletePaths = [];

        try {
            DB::transaction(function () use ($request, $asset, $data, &$storedPaths, &$obsoletePaths) {
                $asset->update($data);
                $this->persistTracks($request, $asset, $storedPaths, $obsoletePaths);
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }

        Storage::disk('public')->delete(array_values(array_unique(array_filter($obsoletePaths))));

        return redirect()
            ->route('artist-studio.releases.index')
            ->with('flash', 'Release “'.$asset->title.'” updated.');
    }

    public function destroyRelease(Request $request, Asset $asset)
    {
        $this->authorizeAsset($request, $asset);

        if ($asset->cover_path && ! str_starts_with($asset->cover_path, 'http')) {
            Storage::disk('public')->delete($asset->cover_path);
        }
        foreach ($asset->tracks as $track) {
            Storage::disk('public')->delete(array_filter([$track->audio_path, $track->preview_path]));
        }
        $title = $asset->title;
        $asset->delete();

        return redirect()
            ->route('artist-studio.releases.index')
            ->with('flash', 'Release “'.$title.'” removed.');
    }

    public function analytics(Request $request)
    {
        return view('artist-studio.placeholder', [
            'eyebrow'  => 'Analytics',
            'title'    => 'Deep performance insights',
            'subtitle' => 'Plays, retention, geography and conversion broken down by release. Live data dashboards land here next.',
            'icon'     => 'leaderboard',
        ]);
    }

    public function payments(Request $request)
    {
        return view('artist-studio.placeholder', [
            'eyebrow'  => 'Payments',
            'title'    => 'Earnings & payouts',
            'subtitle' => 'Track revenue, manage payout methods, and review tax forms once Stripe Connect is wired up.',
            'icon'     => 'payments',
        ]);
    }

    public function settings(Request $request)
    {
        return view('artist-studio.settings', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'artist_name'  => ['nullable', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_avatar'=> ['nullable', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->artist_name = $data['artist_name'] ?: $data['name'];
        $user->email = Str::lower($data['email']);

        if ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->avatar_path = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $request->file('avatar')->store('avatars', 'public');
        }

        $user->save();

        return redirect()
            ->route('artist-studio.settings')
            ->with('flash', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->password = Hash::make($data['password']);
        $user->save();

        $request->session()->regenerate();

        return redirect()
            ->route('artist-studio.settings')
            ->with('flash', 'Password updated.');
    }

    private function resolveCover(Request $request, ?Asset $existing): ?string
    {
        if ($request->hasFile('cover')) {
            if ($existing?->cover_path && ! str_starts_with($existing->cover_path, 'http')) {
                Storage::disk('public')->delete($existing->cover_path);
            }
            return $request->file('cover')->store('covers', 'public');
        }

        if ($url = $request->input('cover_url')) {
            return $url;
        }

        return $existing?->cover_path;
    }

    private function releaseData(StoreAssetRequest $request): array
    {
        return collect($request->validated())
            ->except(['cover', 'cover_url', 'tracks'])
            ->all();
    }

    private function persistTracks(
        StoreAssetRequest $request,
        Asset $asset,
        array &$storedPaths,
        array &$obsoletePaths,
    ): void {
        $keptIds = [];

        foreach ($request->validated('tracks') as $index => $trackData) {
            $track = isset($trackData['id'])
                ? $asset->tracks()->whereKey($trackData['id'])->firstOrFail()
                : new AssetTrack();

            $audio = $request->file("tracks.$index.audio");
            $preview = $request->file("tracks.$index.preview");

            if ($audio) {
                if ($track->exists && $track->audio_path) {
                    $obsoletePaths[] = $track->audio_path;
                }
                $track->audio_path = $audio->store('audio/'.$asset->id, 'public');
                $storedPaths[] = $track->audio_path;
            }

            if ($preview) {
                if ($track->exists && $track->preview_path) {
                    $obsoletePaths[] = $track->preview_path;
                }
                $track->preview_path = $preview->store('previews/'.$asset->id, 'public');
                $storedPaths[] = $track->preview_path;
            }

            $track->title = $trackData['title'];
            $track->position = $index + 1;
            $asset->tracks()->save($track);
            $keptIds[] = $track->id;
        }

        $asset->tracks()
            ->whereNotIn('id', $keptIds)
            ->get()
            ->each(function (AssetTrack $track) use (&$obsoletePaths): void {
                $obsoletePaths[] = $track->audio_path;
                $obsoletePaths[] = $track->preview_path;
                $track->delete();
            });
    }

    private function authorizeAsset(Request $request, Asset $asset): void
    {
        $user = $request->user();
        abort_unless($asset->user_id === $user->id || $user->is_super_admin, 403);
    }
}
