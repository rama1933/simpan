<?php

namespace Database\Seeders;

use App\Models\Knowledge;
use App\Models\Category;
use App\Models\User;
use App\Models\MasterSKPD;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PublicKnowledgeSeeder extends Seeder
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

        // Generate 100 dummy knowledge data with years 2020-2025
        $knowledgeData = [];
        
        $titles = [
            'Panduan Implementasi Sistem Manajemen Mutu ISO 9001:2015',
            'Strategi Pengembangan SDM di Era Digital',
            'Prosedur Standar Pelayanan Publik Terpadu',
            'Panduan Keselamatan dan Kesehatan Kerja (K3)',
            'Sistem Informasi Manajemen Kepegawaian',
            'Pedoman Pengelolaan Keuangan Daerah',
            'Strategi Pemberdayaan Masyarakat Desa',
            'Panduan Penggunaan Teknologi Informasi',
            'Prosedur Pengadaan Barang dan Jasa',
            'Sistem Monitoring dan Evaluasi Program',
            'Panduan Pengelolaan Arsip Digital',
            'Strategi Komunikasi Publik yang Efektif',
            'Prosedur Penanganan Pengaduan Masyarakat',
            'Panduan Audit Internal Pemerintahan',
            'Sistem Perencanaan Pembangunan Daerah',
            'Pedoman Pengelolaan Aset Daerah',
            'Strategi Peningkatan Kualitas Pelayanan',
            'Panduan Implementasi e-Government',
            'Prosedur Pengelolaan Data dan Informasi',
            'Sistem Pengendalian Intern Pemerintah'
        ];

        $contentTemplates = [
            'Dokumen ini berisi panduan lengkap untuk implementasi sistem yang efektif dan efisien. Mencakup tahapan perencanaan, pelaksanaan, monitoring, dan evaluasi. Dilengkapi dengan contoh kasus dan best practices dari berbagai instansi.',
            'Panduan komprehensif yang membahas strategi dan metodologi terkini dalam pengelolaan organisasi modern. Dokumen ini telah disesuaikan dengan standar nasional dan internasional yang berlaku.',
            'Prosedur operasional standar yang telah teruji dan terbukti meningkatkan efektivitas kerja. Berisi langkah-langkah detail, flowchart, dan indikator keberhasilan yang dapat diukur.',
            'Manual praktis yang dirancang untuk membantu implementasi sistem manajemen yang berkelanjutan. Mencakup framework, tools, dan template yang dapat langsung digunakan.',
            'Pedoman teknis yang berisi standar dan prosedur yang harus diikuti untuk memastikan kualitas output yang optimal. Dilengkapi dengan checklist dan form evaluasi.'
        ];

        for ($i = 1; $i <= 100; $i++) {
            // Random year between 2020-2025
            $year = rand(2020, 2025);
            $month = rand(1, 12);
            $day = rand(1, 28);
            
            // Create random date
            $publishedDate = Carbon::create($year, $month, $day);
            
            // Random title with number
            $baseTitle = $titles[array_rand($titles)];
            $title = $baseTitle . ' - Edisi ' . $i;
            
            // Random content
            $content = $contentTemplates[array_rand($contentTemplates)] . "\n\n";
            $content .= "Dokumen ini merupakan revisi ke-{$i} yang telah disesuaikan dengan perkembangan terbaru di tahun {$year}. ";
            $content .= "Berisi panduan praktis, contoh implementasi, dan evaluasi hasil yang telah terbukti efektif di berbagai instansi pemerintah. ";
            $content .= "Dokumen ini dapat digunakan sebagai referensi untuk meningkatkan kualitas pelayanan dan kinerja organisasi.";
            
            // Random category and SKPD
            $category = $categories->random();
            $skpd = $skpds->random();
            
            $knowledgeData[] = [
                'title' => $title,
                'content' => $content,
                'description' => substr($content, 0, 200) . '...',
                'author_id' => $user->id,
                'category_id' => $category->id,
                'skpd_id' => $skpd->id,
                'status' => 'published',
                'verification_status' => 'approved',
                'verified_by' => $user->id,
                'verified_at' => $publishedDate->copy()->addDays(1),
                'published_at' => $publishedDate,
                'created_at' => $publishedDate,
                'updated_at' => $publishedDate->copy()->addDays(2)
            ];
        }

        // Insert data in chunks for better performance
        $chunks = array_chunk($knowledgeData, 20);
        foreach ($chunks as $chunk) {
            Knowledge::insert($chunk);
        }

        $this->command->info('100 public knowledge data has been seeded successfully!');
        $this->command->info('Data range: 2020-2025');
        $this->command->info('All data is published and verified for public access.');
    }
}