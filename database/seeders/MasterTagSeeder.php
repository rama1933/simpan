<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterTag;
use Illuminate\Support\Str;

class MasterTagSeeder extends Seeder
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
            'MySQL'
        ];

        foreach ($names as $name) {
            MasterTag::firstOrCreate([
                'slug' => Str::slug($name)
            ], [
                'name' => $name,
                'is_active' => true,
            ]);
        }
    }
}
