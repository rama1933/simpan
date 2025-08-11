<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Knowledge;
use App\Models\Category;
use App\Models\MasterSKPD;
use App\Models\Tag;
use App\Models\MasterTag;
use Illuminate\Support\Str;

class SKPDKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user dengan role User SKPD
        $skpdUsers = User::whereHas('roles', function ($q) {
            $q->where('name', 'User SKPD');
        })->get();

        if ($skpdUsers->isEmpty()) {
            $this->command?->warn('Tidak ada user dengan role "User SKPD". Jalankan UserSeeder dulu.');
            return;
        }

        // Ambil kategori aktif atau buat default jika kosong
        $category = Category::where('is_active', true)->first();
        if (!$category) {
            $category = Category::create(['name' => 'Umum', 'is_active' => true, 'color' => '#3B82F6']);
        }

        // Ambil SKPD aktif
        $skpd = MasterSKPD::where('is_active', true)->first();
        if (!$skpd) {
            $this->command?->warn('Tidak ada Master SKPD aktif. Jalankan seeder Master SKPD terlebih dahulu.');
            return;
        }

        // Helper buat/tag ids
        $upsertTags = function (array $names) {
            $ids = [];
            foreach ($names as $name) {
                $name = trim((string) $name);
                if ($name === '') continue;
                $slug = Str::slug($name);
                $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
                MasterTag::firstOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
                $ids[] = $tag->id;
            }
            return $ids;
        };

        $examples = [
            [
                'title' => 'Panduan Penyusunan SOP Layanan Publik',
                'description' => 'Ringkasan tahapan penyusunan SOP di lingkungan SKPD.',
                'content' => "# SOP Layanan Publik\n\nBerikut langkah-langkah penyusunan SOP: \n1. Identifikasi proses\n2. Pemetaan stakeholders\n3. Penyusunan draft\n4. Sosialisasi dan evaluasi.",
                'tags' => ['SOP', 'Layanan Publik', 'Administrasi'],
            ],
            [
                'title' => 'Best Practice Pengelolaan Arsip Digital',
                'description' => 'Tips implementasi arsip digital yang aman dan mudah dicari.',
                'content' => "**Arsip digital** memerlukan standar penamaan, struktur folder, dan kontrol akses. Gunakan backup berkala dan enkripsi.",
                'tags' => ['Arsip', 'Digital', 'Keamanan'],
            ],
            [
                'title' => 'Checklist Monitoring Program Kegiatan',
                'description' => 'Daftar pengecekan untuk memonitor capaian program.',
                'content' => "Checklist monitoring meliputi indikator, target, realisasi, kendala, dan rencana tindak lanjut.",
                'tags' => ['Monitoring', 'Evaluasi', 'Program'],
            ],
        ];

        foreach ($skpdUsers as $user) {
            foreach ($examples as $ex) {
                $knowledge = Knowledge::create([
                    'title' => $ex['title'],
                    'description' => $ex['description'],
                    'content' => $ex['content'],
                    'tags' => $ex['tags'],
                    'category_id' => $category->id,
                    'skpd_id' => $skpd->id,
                    'author_id' => $user->id,
                    'status' => 'draft',
                    'verification_status' => 'pending',
                ]);
                // Sinkron tag relasional
                $tagIds = $upsertTags($ex['tags']);
                $knowledge->tagsRelation()->sync($tagIds);
            }
        }

        $this->command?->info('Dummy knowledge dari User SKPD berhasil dibuat (status verifikasi pending).');
    }
}
