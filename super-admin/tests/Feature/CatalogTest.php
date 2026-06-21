<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_only_returns_live_releases_owned_by_artist_accounts(): void
    {
        $artist = User::factory()->create(['is_artist' => true, 'artist_name' => 'Real Artist']);
        $fan = User::factory()->create(['is_artist' => false]);

        $liveArtistRelease = Asset::create([
            'user_id' => $artist->id,
            'title' => 'Artist Upload',
            'artist' => 'Real Artist',
            'release_type' => 'single',
            'price' => 4.99,
            'status' => 'live',
            'redemption_limit' => 100,
            'redemptions' => 0,
        ]);

        Asset::create([
            'title' => 'Unowned Demo Release',
            'artist' => 'Demo Artist',
            'release_type' => 'single',
            'price' => 9.99,
            'status' => 'live',
            'redemption_limit' => 100,
            'redemptions' => 0,
        ]);

        Asset::create([
            'user_id' => $fan->id,
            'title' => 'Fan Owned Release',
            'artist' => $fan->name,
            'release_type' => 'single',
            'price' => 9.99,
            'status' => 'live',
            'redemption_limit' => 100,
            'redemptions' => 0,
        ]);

        Asset::create([
            'user_id' => $artist->id,
            'title' => 'Artist Draft',
            'artist' => 'Real Artist',
            'release_type' => 'album',
            'price' => 12.99,
            'status' => 'draft',
            'redemption_limit' => 100,
            'redemptions' => 0,
        ]);

        $this->getJson('/api/catalog')
            ->assertOk()
            ->assertJsonCount(1, 'releases')
            ->assertJsonPath('releases.0.id', (string) $liveArtistRelease->id)
            ->assertJsonPath('releases.0.artist', 'Real Artist');
    }

    public function test_unowned_demo_release_is_not_publicly_accessible(): void
    {
        $release = Asset::create([
            'title' => 'Unowned Demo Release',
            'artist' => 'Demo Artist',
            'release_type' => 'single',
            'price' => 9.99,
            'status' => 'live',
            'redemption_limit' => 100,
            'redemptions' => 0,
        ]);

        $this->getJson('/api/catalog/'.$release->id)->assertNotFound();
    }
}
