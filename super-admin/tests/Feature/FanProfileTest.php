<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FanProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_fan_can_update_name_and_profile_photo(): void
    {
        Storage::fake('public');
        $fan = User::factory()->create(['is_artist' => false, 'is_super_admin' => false]);

        $response = $this->actingAs($fan)->post(route('fan-api.profile.update'), [
            'name' => 'Updated Fan',
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 300, 300),
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('user.name', 'Updated Fan');

        $fan->refresh();
        $this->assertNotNull($fan->avatar_path);
        Storage::disk('public')->assertExists($fan->avatar_path);
    }

    public function test_email_fan_can_change_password_with_current_password(): void
    {
        $fan = User::factory()->create([
            'password' => 'OldPassword123!',
            'has_password' => true,
            'is_artist' => false,
            'is_super_admin' => false,
        ]);

        $this->actingAs($fan)->putJson(route('fan-api.password.update'), [
            'current_password' => 'OldPassword123!',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertOk();

        $this->assertTrue(Hash::check('NewPassword123!', $fan->fresh()->password));
    }

    public function test_phone_fan_can_set_first_password_without_current_password(): void
    {
        $fan = User::factory()->create([
            'email' => null,
            'phone' => '+256700123456',
            'password' => str()->random(64),
            'has_password' => false,
            'is_artist' => false,
            'is_super_admin' => false,
        ]);

        $this->actingAs($fan)->putJson(route('fan-api.password.update'), [
            'password' => 'FirstPassword123!',
            'password_confirmation' => 'FirstPassword123!',
        ])->assertOk();

        $fan->refresh();
        $this->assertTrue($fan->has_password);
        $this->assertTrue(Hash::check('FirstPassword123!', $fan->password));
    }
}
