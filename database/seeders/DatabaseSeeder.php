<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole('Admin');

        // Create sample user SKPD
        $user = User::factory()->create([
            'name' => 'User SKPD',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('User SKPD');
    }
}
