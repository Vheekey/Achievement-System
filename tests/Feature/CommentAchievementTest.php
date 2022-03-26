<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\User;
use App\Traits\Caches;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommentAchievementTest extends TestCase
{
    use WithFaker, RefreshDatabase, Caches;

    public function setUp(): void
    {
        parent::setUp();

        Achievement::upsert(AchievementSeeder::achievements(), ['category', 'group'], ['category']);

        self::putInitialCaches();
    }

    /**
     * Test that a user can comment
     *
     * @return void
     */
    public function test_user_can_comment()
    {
        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $payload = [
            'comment' => $this->faker->text()
        ];

        $response = $this->postJson(route('user.comment', ['user' => $user->id]), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertEquals(count($user->comments), 1);
    }

    /**
     * Test that a user can unlock achievement comment
     *
     * @return void
     */
    public function test_user_can_unlock_comment_achievement()
    {
        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $payload = [
            'comment' => $this->faker->text()
        ];

        $count = 5;
        $achievement = '5 Comments Written';

        for($i=0; $i < $count; $i++){
            $this->postJson(route('user.comment', ['user' => $user->id]), $payload);
        }

        $response = $this->getJson(route('user.index', ['user' => $user->id]));

        $details = $response->decodeResponseJson();
        $unlocked_achievements = $details['data']['unlocked_achievements'];
        $achievement_exists = in_array($achievement, json_decode($unlocked_achievements, true));

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertEquals(count($user->comments), $count);
        $this->assertTrue($achievement_exists);
    }
}
