<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $badges = [
            [
                'name' => 'Trusted',
                'slug' => 'trusted',
                'color' => '#198754',
                'min_score' => 70,
                'max_score' => 100,
            ],
            [
                'name' => 'Mixed',
                'slug' => 'suspicious',
                'color' => '#ffc107',
                'min_score' => 40,
                'max_score' => 69,
            ],
            [
                'name' => 'Fake',
                'slug' => 'fake',
                'color' => '#dc3545',
                'min_score' => 0,
                'max_score' => 39,
            ],
            [
                'name' => 'Unverified',
                'slug' => 'unverified',
                'color' => '#6c757d',
                'min_score' => null,
                'max_score' => null,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::query()->updateOrCreate(['slug' => $badge['slug']], $badge);
        }
    }
}
