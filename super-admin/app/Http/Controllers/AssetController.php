<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function index()
    {
        $stats = [
            ['label' => 'Total Assets', 'value' => number_format(Asset::count()),                       'delta' => '+ this week', 'icon' => 'album'],
            ['label' => 'Active Drops', 'value' => number_format(Asset::where('status', 'live')->count()),     'delta' => 'live',        'icon' => 'campaign'],
            ['label' => 'Scheduled',    'value' => number_format(Asset::where('status', 'scheduled')->count()),'delta' => 'queued',      'icon' => 'schedule'],
            ['label' => 'Archived',     'value' => number_format(Asset::where('status', 'archived')->count()), 'delta' => 'hidden',      'icon' => 'archive'],
        ];

        $assets = Asset::query()->latest()->get();

        return view('super-admin.assets.index', compact('stats', 'assets'));
    }

    public function create()
    {
        return view('super-admin.assets.create', [
            'asset' => new Asset([
                'price' => 9.99,
                'redemption_limit' => 1000,
                'status' => 'scheduled',
            ]),
        ]);
    }

    public function store(StoreAssetRequest $request)
    {
        $data = $request->validated();
        $data['cover_path'] = $this->resolveCover($request, null);

        $asset = Asset::create($data);

        return redirect()
            ->route('super-admin.assets.index')
            ->with('flash', 'Asset “'.$asset->title.'” created.');
    }

    public function edit(Asset $asset)
    {
        return view('super-admin.assets.edit', compact('asset'));
    }

    public function update(StoreAssetRequest $request, Asset $asset)
    {
        $data = $request->validated();
        $data['cover_path'] = $this->resolveCover($request, $asset);

        $asset->update($data);

        return redirect()
            ->route('super-admin.assets.index')
            ->with('flash', 'Asset “'.$asset->title.'” updated.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->cover_path && !str_starts_with($asset->cover_path, 'http')) {
            Storage::disk('public')->delete($asset->cover_path);
        }
        $title = $asset->title;
        $asset->delete();

        return redirect()
            ->route('super-admin.assets.index')
            ->with('flash', 'Asset “'.$title.'” removed.');
    }

    private function resolveCover(Request $request, ?Asset $existing): ?string
    {
        // 1) File upload wins
        if ($request->hasFile('cover')) {
            // Delete old local cover if any
            if ($existing?->cover_path && !str_starts_with($existing->cover_path, 'http')) {
                Storage::disk('public')->delete($existing->cover_path);
            }
            return $request->file('cover')->store('covers', 'public');
        }

        // 2) External URL provided
        if ($url = $request->input('cover_url')) {
            return $url;
        }

        // 3) Keep existing
        return $existing?->cover_path;
    }
}
