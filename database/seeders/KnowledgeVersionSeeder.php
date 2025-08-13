<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KnowledgeVersion;
use App\Models\Knowledge;
use App\Models\User;
use App\Models\Category;
use App\Models\MasterSKPD;
use App\Models\Tag;
use Carbon\Carbon;

class KnowledgeVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $knowledges = Knowledge::all();
        $users = User::all();
        $categories = Category::all();
        $skpds = MasterSKPD::all();
        $tags = Tag::all();

        foreach ($knowledges as $knowledge) {
            // Create 2-3 versions for each knowledge
            $versionCount = rand(2, 3);
            
            for ($i = 1; $i <= $versionCount; $i++) {
                $user = $users->random();
                $category = $categories->random();
                $skpd = $skpds->random();
                
                $version = KnowledgeVersion::create([
                    'knowledge_id' => $knowledge->id,
                    'version_number' => $i,
                    'title' => $knowledge->title . ' - Versi ' . $i,
                    'content' => $this->generateVersionContent($knowledge->content, $i),
                    'summary' => $this->generateSummary($knowledge->title, $i),
                    'category_id' => $category->id,
                    'skpd_id' => $skpd->id,
                    'status' => $this->getRandomStatus($i, $versionCount),
                    'verification_status' => $this->getRandomVerificationStatus(),
                    'change_type' => $i === 1 ? 'created' : 'updated',
                    'change_reason' => $this->getChangeReason($i),
                    'effective_date' => Carbon::now()->subDays(rand(1, 30)),
                    'expiry_date' => Carbon::now()->addDays(rand(30, 365)),
                    'created_by' => $user->id,
                    'verified_by' => $users->random()->id,
                    'verified_at' => Carbon::now()->subDays(rand(1, 10)),
                    'created_at' => Carbon::now()->subDays(rand(1, 60)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);

                // Attach random tags
                $randomTags = $tags->random(rand(1, 3));
                $version->tags()->attach($randomTags->pluck('id'));
            }
        }
    }

    private function generateVersionContent($originalContent, $versionNumber)
    {
        $additions = [
            "\n\n## Pembaruan Versi {$versionNumber}\n",
            "Konten telah diperbarui dengan informasi terbaru pada versi {$versionNumber}.",
            "\n\n### Perbaikan dan Peningkatan\n- Perbaikan struktur konten\n- Penambahan informasi detail\n- Optimisasi readability",
            "\n\n### Catatan Versi {$versionNumber}\nVersi ini mencakup perbaikan berdasarkan feedback pengguna."
        ];
        
        return $originalContent . $additions[array_rand($additions)];
    }

    private function generateSummary($title, $versionNumber)
    {
        $summaries = [
            "Ringkasan untuk {$title} versi {$versionNumber} dengan pembaruan konten terbaru.",
            "Versi {$versionNumber} dari {$title} mencakup perbaikan dan penambahan informasi.",
            "Pembaruan {$title} versi {$versionNumber} dengan fokus pada peningkatan kualitas konten.",
            "Ringkasan komprehensif {$title} versi {$versionNumber} untuk referensi cepat."
        ];
        
        return $summaries[array_rand($summaries)];
    }

    private function getRandomStatus($versionNumber, $totalVersions)
    {
        // Latest version is usually published, older versions are archived
        if ($versionNumber === $totalVersions) {
            return collect(['published', 'draft'])->random();
        } else {
            return collect(['archived', 'published'])->random();
        }
    }

    private function getRandomVerificationStatus()
    {
        return collect(['verified', 'pending', 'rejected'])->random();
    }

    private function getChangeReason($versionNumber)
    {
        $reasons = [
            "Pembaruan konten berdasarkan regulasi terbaru",
            "Perbaikan kesalahan pada versi sebelumnya",
            "Penambahan informasi detail dan contoh",
            "Optimisasi struktur dan format konten",
            "Update berdasarkan feedback pengguna",
            "Sinkronisasi dengan kebijakan terbaru",
            "Perbaikan tata bahasa dan ejaan",
            "Penambahan referensi dan sumber terpercaya"
        ];
        
        if ($versionNumber === 1) {
            return "Pembuatan versi awal dokumen";
        }
        
        return $reasons[array_rand($reasons)];
    }
}