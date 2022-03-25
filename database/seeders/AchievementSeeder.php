<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $achievements = array(
            [
                'category' => 'First Lesson Watched',
                'group' => Achievement::LESSON,
            ],
            [
                'category' => '5 Lessons Watched',
                'group' => Achievement::LESSON,
            ],
            [
                'category' => '10 Lessons Watched',
                'group' => Achievement::LESSON,
            ],
            [
                'category' => '25 Lessons Watched',
                'group' => Achievement::LESSON,
            ],
            [
                'category' => '50 Lessons Watched',
                'group' => Achievement::LESSON,
            ],

            [
                'category' => 'First Comment Written',
                'group' => Achievement::COMMENT,
            ],
            [
                'category' => '3 Comments Written',
                'group' => Achievement::COMMENT,
            ],
            [
                'category' => '5 Comments Written',
                'group' => Achievement::COMMENT,
            ],
            [
                'category' => '10 Comment Written',
                'group' => Achievement::COMMENT,
            ],
            [
                'category' => '20 Comment Written',
                'group' => Achievement::COMMENT,
            ]
        );

        Achievement::upsert($achievements, ['category', 'group'], ['category']);
    }
}
