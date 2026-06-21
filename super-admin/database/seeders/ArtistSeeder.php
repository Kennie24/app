<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ArtistSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew([
            'email' => env('ARTIST_EMAIL', 'artist@soundredeem.com'),
        ]);

        $user->name = env('ARTIST_NAME', 'Cyber Echoes');
        $user->artist_name = env('ARTIST_NAME', 'Cyber Echoes');
        $user->is_artist = true;
        $user->is_super_admin = false;
        $user->email_verified_at ??= now();

        if (! $user->exists) {
            $user->password = Hash::make(env('ARTIST_PASSWORD', 'ArtistPass!2026'));
        }

        $user->save();
    }
}
