<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Knowledge;
use App\Models\User;
use App\Models\Category;

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

        // Create categories first
        $categoryMap = [];
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

        foreach ($categories as $categoryName) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            $categoryMap[$categoryName] = $category->id;
        }

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
            ],
            [
                'title' => 'Olahraga untuk Pemula',
                'content' => 'Memulai olahraga bisa menjadi tantangan bagi pemula. Artikel ini membahas berbagai jenis olahraga yang cocok untuk pemula dan tips memulai dengan aman.',
                'description' => 'Panduan olahraga untuk pemula yang aman dan efektif',
                'category' => 'Olahraga',
                'tags' => ['Olahraga', 'Fitness', 'Pemula', 'Kesehatan'],
                'status' => 'draft'
            ],
            [
                'title' => 'Penemuan Sains Terbaru',
                'content' => 'Sains terus berkembang dengan penemuan-penemuan baru yang menarik. Artikel ini membahas beberapa penemuan sains terbaru yang berpotensi mengubah dunia.',
                'description' => 'Ringkasan penemuan sains terbaru yang menarik',
                'category' => 'Sains',
                'tags' => ['Sains', 'Penemuan', 'Penelitian', 'Teknologi'],
                'status' => 'published'
            ],
            [
                'title' => 'Tips Wirausaha Sukses',
                'content' => 'Wirausaha membutuhkan strategi dan mindset yang tepat. Artikel ini membahas berbagai tips dan strategi untuk memulai dan mengembangkan bisnis yang sukses.',
                'description' => 'Panduan lengkap tips wirausaha untuk pemula',
                'category' => 'Bisnis',
                'tags' => ['Wirausaha', 'Bisnis', 'Startup', 'Strategi'],
                'status' => 'archived'
            ],
            [
                'title' => 'Pemrograman Web Modern',
                'content' => 'Web development telah berkembang pesat dengan teknologi modern. Artikel ini membahas berbagai teknologi dan framework terbaru dalam pengembangan web.',
                'description' => 'Panduan teknologi web development modern',
                'category' => 'Teknologi',
                'tags' => ['Web Development', 'JavaScript', 'React', 'Vue'],
                'status' => 'published'
            ]
        ];

        // Get SKPD users for distribution
        $skpdUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'User SKPD');
        })->get();

        if ($skpdUsers->isEmpty()) {
            // Fallback to admin user if no SKPD users exist
            $skpdUsers = collect([$user]);
        }

        foreach ($sampleKnowledge as $index => $knowledgeData) {
            $categoryName = $knowledgeData['category'];
            unset($knowledgeData['category']); // Remove category name

            // Distribute knowledge across different SKPDs
            $skpdUser = $skpdUsers[$index % $skpdUsers->count()];

            Knowledge::create(array_merge($knowledgeData, [
                'author_id' => $user->id,
                'category_id' => $categoryMap[$categoryName],
                'skpd_id' => $skpdUser->id
            ]));
        }

        $this->command->info('Sample knowledge data has been seeded successfully!');
    }
}
