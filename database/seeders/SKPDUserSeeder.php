<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MasterSKPD;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SKPDUserSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role User SKPD sudah ada
        $userSKPDRole = Role::firstOrCreate(['name' => 'User SKPD']);
        
        // Ambil semua SKPD yang aktif
        $skpds = MasterSKPD::where('is_active', true)->get();
        
        foreach ($skpds as $skpd) {
            // Buat email berdasarkan kode SKPD
            $email = $this->generateEmailFromSKPD($skpd->kode_skpd);
            
            // Buat user untuk setiap SKPD
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $this->generateNameFromSKPD($skpd->nama_skpd),
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'skpd_id' => $skpd->id,
                ]
            );
            
            // Assign role jika belum ada
            if (!$user->hasRole('User SKPD')) {
                $user->assignRole('User SKPD');
            }
        }
        
        $this->command->info('SKPD users seeded successfully!');
        $this->command->info('Total users created: ' . $skpds->count());
    }
    
    /**
     * Generate email dari kode SKPD
     */
    private function generateEmailFromSKPD(string $kodeSKPD): string
    {
        // Bersihkan kode SKPD dan buat email
        $cleanCode = strtolower(str_replace(['.', ' ', '_'], '', $kodeSKPD));
        return $cleanCode . '@hss.go.id';
    }
    
    /**
     * Generate nama user dari nama SKPD
     */
    private function generateNameFromSKPD(string $namaSKPD): string
    {
        return 'Admin ' . $namaSKPD;
    }
}