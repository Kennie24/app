<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL',    'kennethkenzie48@gmail.com');
        $password = env('ADMIN_PASSWORD', 'SoundRedeem!2026');
        $name     = env('ADMIN_NAME',     'Ken Reyes');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make($password),
                'is_super_admin'    => true,
                'email_verified_at' => now(),
            ],
        );

        $this->command->newLine();
        $this->command->info('───────────────────────────────────────────');
        $this->command->info(' SoundRedeem · Super Admin account ready');
        $this->command->info('───────────────────────────────────────────');
        $this->command->line('  Email    : ' . $email);
        $this->command->line('  Password : ' . $password);
        $this->command->line('  Role     : super_admin');
        $this->command->info('───────────────────────────────────────────');
        $this->command->warn(' Change the password after your first login.');
        $this->command->newLine();
    }
}
