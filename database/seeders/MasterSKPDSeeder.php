<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class MasterSKPDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create User SKPD role
        $skpdRole = Role::firstOrCreate(['name' => 'User SKPD']);

        $masterSkpds = [
            [
                'name' => 'Dinas Pendidikan',
                'email' => 'dinas.pendidikan@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas pengembangan pendidikan di daerah',
                'code' => 'DISDIK',
                'address' => 'Jl. Pendidikan No. 1, Kota Example',
                'phone' => '021-1234567',
                'website' => 'https://dinas-pendidikan.example.com'
            ],
            [
                'name' => 'Dinas Kesehatan',
                'email' => 'dinas.kesehatan@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas pelayanan kesehatan masyarakat',
                'code' => 'DINKES',
                'address' => 'Jl. Kesehatan No. 2, Kota Example',
                'phone' => '021-1234568',
                'website' => 'https://dinas-kesehatan.example.com'
            ],
            [
                'name' => 'Dinas Perhubungan',
                'email' => 'dinas.perhubungan@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas transportasi dan lalu lintas',
                'code' => 'DISHUB',
                'address' => 'Jl. Perhubungan No. 3, Kota Example',
                'phone' => '021-1234569',
                'website' => 'https://dinas-perhubungan.example.com'
            ],
            [
                'name' => 'Dinas Pariwisata',
                'email' => 'dinas.pariwisata@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas pengembangan pariwisata daerah',
                'code' => 'DISPAR',
                'address' => 'Jl. Pariwisata No. 4, Kota Example',
                'phone' => '021-1234570',
                'website' => 'https://dinas-pariwisata.example.com'
            ],
            [
                'name' => 'Dinas Lingkungan Hidup',
                'email' => 'dinas.lingkungan@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas kelestarian lingkungan hidup',
                'code' => 'DLH',
                'address' => 'Jl. Lingkungan No. 5, Kota Example',
                'phone' => '021-1234571',
                'website' => 'https://dinas-lingkungan.example.com'
            ],
            [
                'name' => 'Dinas Sosial',
                'email' => 'dinas.sosial@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas kesejahteraan sosial masyarakat',
                'code' => 'DINSOS',
                'address' => 'Jl. Sosial No. 6, Kota Example',
                'phone' => '021-1234572',
                'website' => 'https://dinas-sosial.example.com'
            ],
            [
                'name' => 'Dinas Pertanian',
                'email' => 'dinas.pertanian@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas pengembangan sektor pertanian',
                'code' => 'DISTAN',
                'address' => 'Jl. Pertanian No. 7, Kota Example',
                'phone' => '021-1234573',
                'website' => 'https://dinas-pertanian.example.com'
            ],
            [
                'name' => 'Dinas Perindustrian',
                'email' => 'dinas.perindustrian@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas pengembangan industri daerah',
                'code' => 'DISPERIN',
                'address' => 'Jl. Perindustrian No. 8, Kota Example',
                'phone' => '021-1234574',
                'website' => 'https://dinas-perindustrian.example.com'
            ],
            [
                'name' => 'Dinas Perdagangan',
                'email' => 'dinas.perdagangan@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas pengembangan perdagangan',
                'code' => 'DISDAG',
                'address' => 'Jl. Perdagangan No. 9, Kota Example',
                'phone' => '021-1234575',
                'website' => 'https://dinas-perdagangan.example.com'
            ],
            [
                'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'email' => 'dinas.dukcapil@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas administrasi kependudukan',
                'code' => 'DUKCAPIL',
                'address' => 'Jl. Dukcapil No. 10, Kota Example',
                'phone' => '021-1234576',
                'website' => 'https://dukcapil.example.com'
            ],
            [
                'name' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'email' => 'dinas.pupr@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas infrastruktur dan tata ruang',
                'code' => 'PUPR',
                'address' => 'Jl. PUPR No. 11, Kota Example',
                'phone' => '021-1234577',
                'website' => 'https://pupr.example.com'
            ],
            [
                'name' => 'Dinas Komunikasi dan Informatika',
                'email' => 'dinas.kominfo@example.com',
                'password' => bcrypt('password'),
                'description' => 'Bertanggung jawab atas teknologi informasi dan komunikasi',
                'code' => 'KOMINFO',
                'address' => 'Jl. Kominfo No. 12, Kota Example',
                'phone' => '021-1234578',
                'website' => 'https://kominfo.example.com'
            ]
        ];

        foreach ($masterSkpds as $skpdData) {
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

            // Store additional SKPD information in user metadata
            $user->update([
                'metadata' => [
                    'skpd_code' => $skpdData['code'],
                    'skpd_description' => $skpdData['description'],
                    'skpd_address' => $skpdData['address'],
                    'skpd_phone' => $skpdData['phone'],
                    'skpd_website' => $skpdData['website'],
                    'is_skpd' => true
                ]
            ]);
        }

        $this->command->info('Master SKPD data has been seeded successfully!');
        $this->command->info('Total SKPD created: ' . count($masterSkpds));
    }
}
