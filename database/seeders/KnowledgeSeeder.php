<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Knowledge;
use App\Models\User;

class KnowledgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user for seeding
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        $categories = [
            'Teknologi',
            'Bisnis',
            'Kesehatan',
            'Pendidikan',
            'Seni & Budaya',
            'Olahraga',
            'Sains',
            'Lainnya'
        ];

        $sampleKnowledge = [
            [
                'title' => 'Pengenalan Laravel Framework',
                'content' => 'Laravel adalah framework PHP yang powerful dan elegan untuk pengembangan web. Framework ini menyediakan berbagai fitur yang memudahkan developer dalam membangun aplikasi web yang robust dan scalable.',
                'description' => 'Artikel pengenalan dasar Laravel Framework untuk pemula',
                'category' => 'Teknologi',
                'tags' => ['Laravel', 'PHP', 'Framework', 'Web Development'],
                'status' => 'published'
            ],
            [
                'title' => 'Strategi Marketing Digital',
                'content' => 'Marketing digital telah menjadi bagian penting dari strategi bisnis modern. Artikel ini membahas berbagai strategi dan teknik marketing digital yang efektif untuk meningkatkan penjualan dan brand awareness.',
                'description' => 'Panduan lengkap strategi marketing digital untuk bisnis',
                'category' => 'Bisnis',
                'tags' => ['Marketing', 'Digital', 'Bisnis', 'Strategy'],
                'status' => 'published'
            ],
            [
                'title' => 'Pentingnya Pola Hidup Sehat',
                'content' => 'Pola hidup sehat adalah kunci untuk menjaga kesehatan tubuh dan pikiran. Artikel ini membahas berbagai aspek pola hidup sehat termasuk nutrisi, olahraga, dan manajemen stres.',
                'description' => 'Panduan lengkap pola hidup sehat untuk semua usia',
                'category' => 'Kesehatan',
                'tags' => ['Kesehatan', 'Pola Hidup', 'Nutrisi', 'Olahraga'],
                'status' => 'published'
            ],
            [
                'title' => 'Metode Pembelajaran Efektif',
                'content' => 'Setiap orang memiliki gaya belajar yang berbeda. Artikel ini membahas berbagai metode pembelajaran yang efektif dan bagaimana mengoptimalkan proses belajar sesuai dengan gaya belajar masing-masing.',
                'description' => 'Panduan metode pembelajaran efektif untuk semua usia',
                'category' => 'Pendidikan',
                'tags' => ['Pendidikan', 'Belajar', 'Metode', 'Efektif'],
                'status' => 'draft'
            ],
            [
                'title' => 'Seni Fotografi Dasar',
                'content' => 'Fotografi adalah seni menangkap momen melalui lensa kamera. Artikel ini membahas dasar-dasar fotografi termasuk komposisi, pencahayaan, dan teknik pengambilan gambar yang baik.',
                'description' => 'Panduan dasar fotografi untuk pemula',
                'category' => 'Seni & Budaya',
                'tags' => ['Fotografi', 'Seni', 'Kamera', 'Komposisi'],
                'status' => 'published'
            ]
        ];

        foreach ($sampleKnowledge as $knowledgeData) {
            Knowledge::create(array_merge($knowledgeData, [
                'author_id' => $user->id
            ]));
        }

        $this->command->info('Sample knowledge data has been seeded successfully!');
    }
}
