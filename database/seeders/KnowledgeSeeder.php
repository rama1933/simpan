<?php

namespace Database\Seeders;

use App\Models\Knowledge;
use App\Models\Category;
use App\Models\User;
use App\Models\MasterSKPD;
use Illuminate\Database\Seeder;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user
        $user = User::where('email', 'admin@example.com')->first();
        if (!$user) {
            $this->command->error('Admin user not found. Please run UserSeeder first.');
            return;
        }

        // Get all categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Get all SKPDs
        $skpds = MasterSKPD::all();
        if ($skpds->isEmpty()) {
            $this->command->error('No SKPDs found. Please run MasterSKPDSeeder first.');
            return;
        }

        // Comprehensive knowledge data covering all SKPDs
        $sampleKnowledge = [
            // ===== DINAS TENAGA KERJA DAN TRANSMIGRASI (SKPD001) =====
            [
                'title' => 'Panduan Lengkap JSA (Job Safety Analysis)',
                'content' => 'Job Safety Analysis (JSA) adalah metode identifikasi bahaya yang sistematis untuk setiap langkah pekerjaan. JSA membantu mengidentifikasi potensi bahaya dan menentukan tindakan pengendalian yang tepat sebelum pekerjaan dimulai. Dokumen ini mencakup prosedur lengkap dari perencanaan hingga evaluasi.',
                'category' => 'Keselamatan Kerja',
                'skpd_index' => 0,
                'status' => 'published',
                'published_at' => now()->subDays(5)
            ],
            [
                'title' => 'SOP Penggunaan APD (Alat Pelindung Diri)',
                'content' => 'Alat Pelindung Diri (APD) adalah peralatan yang wajib digunakan oleh pekerja untuk melindungi diri dari bahaya kerja. SOP ini menjelaskan jenis APD yang harus digunakan untuk setiap jenis pekerjaan, cara penggunaan yang benar, dan pemeliharaan APD.',
                'category' => 'Keselamatan Kerja',
                'skpd_index' => 0,
                'status' => 'published',
                'published_at' => now()->subDays(3)
            ],
            [
                'title' => 'Prosedur Penanganan Kecelakaan Kerja',
                'content' => 'Dokumen ini berisi langkah-langkah penanganan kecelakaan kerja mulai dari pertolongan pertama, pelaporan, hingga investigasi untuk mencegah kejadian serupa di masa depan. Termasuk protokol darurat dan koordinasi dengan pihak terkait.',
                'category' => 'Keselamatan Kerja',
                'skpd_index' => 0,
                'status' => 'published',
                'published_at' => now()->subDays(1)
            ],
            [
                'title' => 'Standar Kompetensi Kerja Nasional (SKKNI)',
                'content' => 'SKKNI adalah standar kompetensi yang disusun dan ditetapkan oleh industri, asosiasi profesi, dan lembaga terkait untuk mengukur kemampuan kerja seseorang. Dokumen ini berisi mapping kompetensi untuk berbagai sektor industri.',
                'category' => 'Pendidikan',
                'skpd_index' => 0,
                'status' => 'published',
                'published_at' => now()->subDays(7)
            ],
            [
                'title' => 'Panduan Pelatihan Vokasi',
                'content' => 'Panduan lengkap untuk program pelatihan vokasi yang bertujuan meningkatkan kompetensi tenaga kerja sesuai dengan kebutuhan industri. Mencakup kurikulum, metode pelatihan, dan evaluasi hasil.',
                'category' => 'Pendidikan',
                'skpd_index' => 0,
                'status' => 'published',
                'published_at' => now()->subDays(9)
            ],

            // ===== DINAS SOSIAL (SKPD002) =====
            [
                'title' => 'Prosedur Bantuan Sosial Tunai (BST)',
                'content' => 'BST adalah program bantuan sosial yang diberikan kepada keluarga miskin dan rentan. Prosedur ini menjelaskan tahapan verifikasi, validasi, dan penyaluran bantuan. Termasuk kriteria penerima dan mekanisme monitoring.',
                'category' => 'Pelayanan Publik',
                'skpd_index' => 1,
                'status' => 'published',
                'published_at' => now()->subDays(7)
            ],
            [
                'title' => 'Panduan Penanganan Penyandang Disabilitas',
                'content' => 'Panduan ini berisi standar pelayanan dan fasilitas yang harus disediakan untuk penyandang disabilitas sesuai dengan hak-hak mereka yang dijamin undang-undang. Mencakup aksesibilitas dan inklusivitas.',
                'category' => 'Pelayanan Publik',
                'skpd_index' => 1,
                'status' => 'published',
                'published_at' => now()->subDays(4)
            ],
            [
                'title' => 'SOP Bantuan Bencana Alam',
                'content' => 'Standar Operasional Prosedur untuk penanganan bantuan bencana alam. Mencakup tahapan tanggap darurat, rehabilitasi, dan rekonstruksi. Termasuk koordinasi dengan lembaga terkait dan masyarakat.',
                'category' => 'Pelayanan Publik',
                'skpd_index' => 1,
                'status' => 'published',
                'published_at' => now()->subDays(6)
            ],
            [
                'title' => 'Panduan Pemberdayaan Masyarakat',
                'content' => 'Dokumen ini berisi strategi dan metode pemberdayaan masyarakat untuk meningkatkan kesejahteraan sosial. Mencakup program pelatihan, pendampingan, dan evaluasi dampak.',
                'category' => 'Pelayanan Publik',
                'skpd_index' => 1,
                'status' => 'published',
                'published_at' => now()->subDays(8)
            ],

            // ===== DINAS KESEHATAN (SKPD003) =====
            [
                'title' => 'Protokol Pencegahan Penyakit Menular',
                'content' => 'Protokol ini berisi langkah-langkah pencegahan dan pengendalian penyakit menular di lingkungan kerja dan masyarakat. Termasuk penggunaan masker, cuci tangan, social distancing, dan vaksinasi.',
                'category' => 'Kesehatan',
                'skpd_index' => 2,
                'status' => 'published',
                'published_at' => now()->subDays(6)
            ],
            [
                'title' => 'Standar Sanitasi Lingkungan Kerja',
                'content' => 'Dokumen ini menjelaskan standar kebersihan dan sanitasi yang harus dipenuhi di lingkungan kerja untuk menjaga kesehatan dan kenyamanan pekerja. Mencakup fasilitas sanitasi dan pemeliharaan.',
                'category' => 'Kesehatan',
                'skpd_index' => 2,
                'status' => 'published',
                'published_at' => now()->subDays(2)
            ],
            [
                'title' => 'Panduan Kesehatan Kerja',
                'content' => 'Panduan lengkap untuk menjaga kesehatan pekerja di lingkungan kerja. Mencakup pemeriksaan kesehatan berkala, ergonomi, dan pencegahan penyakit akibat kerja.',
                'category' => 'Kesehatan',
                'skpd_index' => 2,
                'status' => 'published',
                'published_at' => now()->subDays(10)
            ],
            [
                'title' => 'SOP Pelayanan Kesehatan Darurat',
                'content' => 'Standar Operasional Prosedur untuk pelayanan kesehatan darurat di lingkungan kerja. Mencakup pertolongan pertama, evakuasi, dan koordinasi dengan rumah sakit.',
                'category' => 'Kesehatan',
                'skpd_index' => 2,
                'status' => 'published',
                'published_at' => now()->subDays(12)
            ],

            // ===== DINAS PENDIDIKAN (SKPD004) =====
            [
                'title' => 'Kurikulum Pelatihan Vokasi Terpadu',
                'content' => 'Kurikulum ini dirancang untuk program pelatihan vokasi yang bertujuan meningkatkan kompetensi tenaga kerja sesuai dengan kebutuhan industri. Mencakup teori dan praktik yang seimbang.',
                'category' => 'Pendidikan',
                'skpd_index' => 3,
                'status' => 'published',
                'published_at' => now()->subDays(8)
            ],
            [
                'title' => 'Standar Kompetensi Kerja Nasional Indonesia (SKKNI)',
                'content' => 'SKKNI adalah standar kompetensi yang disusun dan ditetapkan oleh industri, asosiasi profesi, dan lembaga terkait untuk mengukur kemampuan kerja seseorang. Dokumen ini berisi mapping kompetensi lengkap.',
                'category' => 'Pendidikan',
                'skpd_index' => 3,
                'status' => 'published',
                'published_at' => now()->subDays(5)
            ],
            [
                'title' => 'Panduan Pengembangan SDM',
                'content' => 'Panduan lengkap untuk pengembangan Sumber Daya Manusia melalui pendidikan dan pelatihan. Mencakup perencanaan, implementasi, dan evaluasi program pengembangan.',
                'category' => 'Pendidikan',
                'skpd_index' => 3,
                'status' => 'published',
                'published_at' => now()->subDays(11)
            ],
            [
                'title' => 'SOP Akreditasi Lembaga Pelatihan',
                'content' => 'Standar Operasional Prosedur untuk akreditasi lembaga pelatihan kerja. Mencakup kriteria, proses evaluasi, dan monitoring lembaga yang sudah terakreditasi.',
                'category' => 'Pendidikan',
                'skpd_index' => 3,
                'status' => 'published',
                'published_at' => now()->subDays(13)
            ],

            // ===== DINAS PERHUBUNGAN (SKPD005) =====
            [
                'title' => 'Prosedur Keselamatan Transportasi Umum',
                'content' => 'Prosedur ini berisi standar keselamatan yang harus dipenuhi oleh operator transportasi umum untuk menjamin keselamatan penumpang dan pengguna jalan. Mencakup inspeksi kendaraan dan pelatihan pengemudi.',
                'category' => 'Transportasi',
                'skpd_index' => 4,
                'status' => 'published',
                'published_at' => now()->subDays(9)
            ],
            [
                'title' => 'Panduan Pengelolaan Terminal',
                'content' => 'Panduan ini menjelaskan standar pengelolaan terminal transportasi yang mencakup aspek keselamatan, kenyamanan, dan pelayanan kepada pengguna jasa transportasi. Termasuk fasilitas dan sistem informasi.',
                'category' => 'Transportasi',
                'skpd_index' => 4,
                'status' => 'published',
                'published_at' => now()->subDays(6)
            ],
            [
                'title' => 'SOP Penanganan Kecelakaan Lalu Lintas',
                'content' => 'Standar Operasional Prosedur untuk penanganan kecelakaan lalu lintas. Mencakup pertolongan pertama, pengaturan lalu lintas, dan koordinasi dengan pihak terkait.',
                'category' => 'Transportasi',
                'skpd_index' => 4,
                'status' => 'published',
                'published_at' => now()->subDays(14)
            ],
            [
                'title' => 'Panduan Keselamatan Jalan',
                'content' => 'Panduan lengkap untuk keselamatan jalan raya. Mencakup perencanaan infrastruktur, rambu lalu lintas, dan edukasi masyarakat tentang keselamatan berkendara.',
                'category' => 'Transportasi',
                'skpd_index' => 4,
                'status' => 'published',
                'published_at' => now()->subDays(15)
            ]
        ];

        // Create knowledge entries
        foreach ($sampleKnowledge as $knowledgeData) {
            $category = $categories->where('name', $knowledgeData['category'])->first();
            $skpd = $skpds[$knowledgeData['skpd_index']];

            if ($category && $skpd) {
                Knowledge::create([
                    'title' => $knowledgeData['title'],
                    'content' => $knowledgeData['content'],
                    'author_id' => $user->id,
                    'category_id' => $category->id,
                    'skpd_id' => $skpd->id,
                    'status' => $knowledgeData['status'],
                    'published_at' => $knowledgeData['published_at']
                ]);
            }
        }

        $this->command->info('Comprehensive knowledge data has been seeded successfully!');
        $this->command->info('Total knowledge created: ' . count($sampleKnowledge));
        $this->command->info('Knowledge distributed across ' . $skpds->count() . ' SKPDs');
    }
}
