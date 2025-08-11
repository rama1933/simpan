<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterSKPD;

class MasterSKPDSeeder extends Seeder
{
    public function run(): void
    {
        $skpds = [
            [
                'kode_skpd' => 'SKPD001',
                'nama_skpd' => 'Dinas Tenaga Kerja dan Transmigrasi',
                'alamat' => 'Jl. Gatot Subroto No. 1, Jakarta',
                'telepon' => '021-1234567',
                'email' => 'disnakertrans@example.com',
                'deskripsi' => 'Dinas yang menangani ketenagakerjaan dan transmigrasi',
                'is_active' => true
            ],
            [
                'kode_skpd' => 'SKPD002',
                'nama_skpd' => 'Dinas Sosial',
                'alamat' => 'Jl. Gatot Subroto No. 2, Jakarta',
                'telepon' => '021-1234568',
                'email' => 'dinsos@example.com',
                'deskripsi' => 'Dinas yang menangani masalah sosial',
                'is_active' => true
            ],
            [
                'kode_skpd' => 'SKPD003',
                'nama_skpd' => 'Dinas Kesehatan',
                'alamat' => 'Jl. Gatot Subroto No. 3, Jakarta',
                'telepon' => '021-1234569',
                'email' => 'dinkes@example.com',
                'deskripsi' => 'Dinas yang menangani kesehatan masyarakat',
                'is_active' => true
            ],
            [
                'kode_skpd' => 'SKPD004',
                'nama_skpd' => 'Dinas Pendidikan',
                'alamat' => 'Jl. Gatot Subroto No. 4, Jakarta',
                'telepon' => '021-1234570',
                'email' => 'dindik@example.com',
                'deskripsi' => 'Dinas yang menangani pendidikan',
                'is_active' => true
            ],
            [
                'kode_skpd' => 'SKPD005',
                'nama_skpd' => 'Dinas Perhubungan',
                'alamat' => 'Jl. Gatot Subroto No. 5, Jakarta',
                'telepon' => '021-1234571',
                'email' => 'dishub@example.com',
                'deskripsi' => 'Dinas yang menangani perhubungan',
                'is_active' => true
            ]
        ];

        foreach ($skpds as $skpd) {
            MasterSKPD::create($skpd);
        }
    }
}
