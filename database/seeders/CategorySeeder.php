<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Keselamatan Kerja',
            'Pelayanan Publik',
            'Kesehatan',
            'Pendidikan',
            'Transportasi',
            'Teknologi',
            'Bisnis',
            'Seni & Budaya',
            'Olahraga',
            'Sains',
            'Lainnya'
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }

        $this->command->info('Categories seeded successfully!');
        $this->command->info('Total categories created: ' . count($categories));
    }
}
