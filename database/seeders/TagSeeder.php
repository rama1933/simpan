<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Laravel',
            'PHP',
            'Vue',
            'TypeScript',
            'Tailwind',
            'Inertia',
            'Sanctum',
            'Pest',
            'Redis',
            'MySQL',
            'JavaScript',
            'CSS',
            'HTML',
            'Bootstrap',
            'API',
            'Database',
            'Security',
            'Performance',
            'Testing',
            'Documentation'
        ];

        foreach ($names as $name) {
            Tag::firstOrCreate([
                'slug' => Str::slug($name)
            ], [
                'name' => $name,
                'is_active' => true,
            ]);
        }
    }
}