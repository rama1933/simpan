<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SKPDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create User SKPD role
        $skpdRole = Role::firstOrCreate(['name' => 'User SKPD']);

        $skpds = [
            [
                'name' => 'Dinas Pendidikan',
                'email' => 'dinas.pendidikan@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Dinas Kesehatan',
                'email' => 'dinas.kesehatan@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Dinas Perhubungan',
                'email' => 'dinas.perhubungan@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Dinas Pariwisata',
                'email' => 'dinas.pariwisata@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Dinas Lingkungan Hidup',
                'email' => 'dinas.lingkungan@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Dinas Sosial',
                'email' => 'dinas.sosial@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Dinas Pertanian',
                'email' => 'dinas.pertanian@example.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'Dinas Perindustrian',
                'email' => 'dinas.perindustrian@example.com',
                'password' => bcrypt('password'),
            ]
        ];

        foreach ($skpds as $skpdData) {
            $user = User::firstOrCreate(
                ['email' => $skpdData['email']],
                [
                    'name' => $skpdData['name'],
                    'password' => $skpdData['password'],
                    'email_verified_at' => now(),
                ]
            );

            // Assign SKPD role
            $user->assignRole($skpdRole);
        }

        $this->command->info('SKPD users have been seeded successfully!');
    }
}
