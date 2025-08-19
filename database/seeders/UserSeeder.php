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
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }

        // Get SKPD data for user creation
        $skpds = \App\Models\MasterSKPD::all();
        
        // Create SKPD users
        foreach ($skpds as $skpd) {
            $email = strtolower(str_replace(['.', ' '], ['', ''], $skpd->kode_skpd)) . '@simpan.go.id';
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'Admin ' . $skpd->nama_skpd,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'skpd_id' => $skpd->id,
                ]
            );
            if (!$user->hasRole('User SKPD')) {
                $user->assignRole('User SKPD');
            }
        }
    }
}
