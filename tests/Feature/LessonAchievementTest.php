<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\User;
use App\Traits\Caches;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LessonAchievementTest extends TestCase
{
    use WithFaker, RefreshDatabase, Caches;

    public function setUp(): void
    {
        parent::setUp();

        Achievement::upsert(AchievementSeeder::achievements(), ['category', 'group'], ['category']);

        self::putInitialCaches();
    }

    /**
     * Test that a user can watch lesson
     *
     * @return void
     */
    public function test_user_can_watch_lesson()
    {
        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $lesson = Lesson::factory()->create();

        $response = $this->postJson(route('user.watched-lesson', ['user' => $user->id, 'lesson' => $lesson->id]));

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertEquals(count($user->lessons), 1);
    }

    /**
     * Test that if a user repeatedly watches a lesson, it does not unlock multiple lesson achievements
     *
     * @return void
     */
    public function test_repeated_lesson_watched_does_not_unlock_multiple_achievements()
    {
        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $lesson = Lesson::factory()->create();

        $count = 5;

        for($i=0; $i < $count; $i++){
            $this->postJson(route('user.watched-lesson', ['user' => $user->id, 'lesson' => $lesson->id]));
        }

        $response = $this->getJson(route('user.index', ['user' => $user->id]));

        $details = $response->decodeResponseJson();
        $unlocked_achievements = $details['data']['unlocked_achievements'];
        $unlocked_achievements_count = count(json_decode($unlocked_achievements, true));

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertEquals($unlocked_achievements_count, 1);
    }

    /**
     * Test that if a user repeatedly watches a lesson, it does not create multiple lesson records
     *
     * @return void
     */
    public function test_repeated_lesson_watched_does_not_create_multiple_records()
    {
        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $lesson = Lesson::factory()->create();

        $count = 5;

        for($i=0; $i < $count; $i++){
            $response = $this->postJson(route('user.watched-lesson', ['user' => $user->id, 'lesson' => $lesson->id]));
        }

        $response->assertOk();
        $this->assertEquals(count($user->lessons), 1);
    }

    /**
     * Test that watching a lesson unlocks an achievement
     *
     * @return void
     */
    public function test_lesson_watched_unlocks_an_achievement()
    {
        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $lesson = Lesson::factory()->create();

        $this->postJson(route('user.watched-lesson', ['user' => $user->id, 'lesson' => $lesson->id]));

        $response = $this->getJson(route('user.index', ['user' => $user->id]));

        $achievement = Lesson::ACHIEVEMENTS[1];

        $details = $response->decodeResponseJson();
        $unlocked_achievements = $details['data']['unlocked_achievements'];
        $achievement_exists = in_array($achievement, json_decode($unlocked_achievements, true));

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertTrue($achievement_exists);
    }
}
