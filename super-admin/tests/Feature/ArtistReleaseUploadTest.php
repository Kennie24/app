<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtistReleaseUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_artist_can_create_a_release_with_full_audio_and_preview(): void
    {
        Storage::fake('public');

        $artist = User::factory()->create([
            'is_artist' => true,
            'artist_name' => 'Test Artist',
        ]);

        $this
            ->actingAs($artist)
            ->get(route('artist-studio.releases.create'))
            ->assertOk()
            ->assertSeeText('Tracks & previews')
            ->assertSee('name="tracks[0][audio]"', false)
            ->assertSee('name="tracks[0][preview]"', false);

        $response = $this
            ->actingAs($artist)
            ->post(route('artist-studio.releases.store'), [
                'title' => 'New Single',
                'artist' => 'Test Artist',
                'price' => '4.99',
                'redemption_limit' => 1000,
                'status' => 'scheduled',
                'release_type' => 'single',
                'tracks' => [
                    [
                        'title' => 'Main Track',
                        'audio' => UploadedFile::fake()->create('song.mp3', 512, 'audio/mpeg'),
                        'preview' => UploadedFile::fake()->create('preview.mp3', 128, 'audio/mpeg'),
                    ],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('artist-studio.releases.index'));

        $asset = Asset::with('tracks')->where('title', 'New Single')->firstOrFail();
        $track = $asset->tracks->sole();

        $this->assertSame($artist->id, $asset->user_id);
        $this->assertSame('Main Track', $track->title);
        $this->assertNotNull($track->preview_path);
        Storage::disk('public')->assertExists($track->audio_path);
        Storage::disk('public')->assertExists($track->preview_path);
    }
}
