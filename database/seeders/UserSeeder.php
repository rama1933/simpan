<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('Admin');

        // Create SKPD Users
        $skpdUsers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@skpd.go.id',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@skpd.go.id',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Ahmad Rizki',
                'email' => 'ahmad.rizki@skpd.go.id',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Dewi Sartika',
                'email' => 'dewi.sartika@skpd.go.id',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($skpdUsers as $userData) {
            $user = User::create([
                ...$userData,
                'email_verified_at' => now(),
            ]);
            $user->assignRole('User SKPD');
        }
    }
}
