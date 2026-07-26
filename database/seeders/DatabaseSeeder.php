<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Admin accounts. These match the two emails already whitelisted for
        // Google login in AuthController, but they can also log in normally
        // with email + password below.
        User::updateOrCreate(
            ['email' => 'adminlaundry1@gmail.com'],
            [
                'name' => 'Admin Laundry 1',
                'password' => 'admin123',
                'role' => 'admin',
                'phone' => '081234567890',
                'address' => 'Kantor Pusat Laundry Yuk',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'adminlaundry2@gmail.com'],
            [
                'name' => 'Admin Laundry 2',
                'password' => 'admin123',
                'role' => 'admin',
                'phone' => '081234567891',
                'address' => 'Kantor Pusat Laundry Yuk',
                'email_verified_at' => now(),
            ]
        );
    }
}
