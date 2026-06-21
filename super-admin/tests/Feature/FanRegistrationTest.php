<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FanRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_fan_can_register_with_email(): void
    {
        $response = $this->postJson(route('fan-api.register'), [
            'method' => 'email',
            'name' => 'New Fan',
            'email' => 'fan@example.com',
            'password' => 'FanPassword123!',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.email', 'fan@example.com')
            ->assertJsonPath('user.phone', null);

        $fan = User::where('email', 'fan@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($fan);
        $this->assertFalse($fan->is_artist);
        $this->assertFalse($fan->is_super_admin);
    }

    public function test_fan_can_register_with_phone(): void
    {
        $response = $this->postJson(route('fan-api.register'), [
            'method' => 'phone',
            'phone' => '+256700123456',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.email', null)
            ->assertJsonPath('user.phone', '+256700123456');

        $fan = User::where('phone', '+256700123456')->firstOrFail();

        $this->assertAuthenticatedAs($fan);
        $this->assertNull($fan->email);
        $this->assertFalse($fan->is_artist);
    }

    public function test_duplicate_fan_identity_is_rejected(): void
    {
        User::factory()->create(['email' => 'fan@example.com']);

        $this->postJson(route('fan-api.register'), [
            'method' => 'email',
            'name' => 'Another Fan',
            'email' => 'fan@example.com',
            'password' => 'FanPassword123!',
        ])->assertUnprocessable();
    }
}
