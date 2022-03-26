<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\User;
use App\Traits\Caches;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAchievementTest extends TestCase
{
    use WithFaker, RefreshDatabase, Caches;

    public function setUp(): void
    {
        parent::setUp();

        Achievement::upsert(AchievementSeeder::achievements(), ['category', 'group'], ['category']);

        self::putInitialCaches();
    }

    /**
     * Test that a user gets a beginner badge
     *
     * @return void
     */
    public function test_user_gets_badge_on_signup()
    {
        $new_user_badge = Achievement::BADGES[0];

        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $response = $this->getJson(route('user.index', ['user' => $user->id]));
        $details = $response->decodeResponseJson();
        $badge = $details['data']['current_badge'];

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertEquals($badge, $new_user_badge);
    }

    /**
     * Test that a user can see the next attainable badge
     *
     * @return void
     */
    public function test_user_can_see_next_badge()
    {
        $new_user_badge = Achievement::BADGES[0];

        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $response = $this->getJson(route('user.index', ['user' => $user->id]));
        $details = $response->decodeResponseJson();
        $badge = $details['data']['current_badge'];

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertEquals($badge, $new_user_badge);
    }

    /**
     * Test that a user can achieve the next attainable badge
     *
     * @return void
     */
    public function test_user_can_achieve_next_badge()
    {
        $user_next_badge = Achievement::BADGES[1];

        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $payload = [
            'comment' => $this->faker->text()
        ];

        $this->postJson(route('user.comment', ['user' => $user->id]), $payload);

        $response = $this->getJson(route('user.index', ['user' => $user->id]));
        $details = $response->decodeResponseJson();
        $badge = $details['data']['next_badge'];

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $this->assertEquals($badge, $user_next_badge);
    }

    /**
     * Test that a user can get achievement records
     *
     * @return void
     */
    public function test_user_can_get_achievemts()
    {
        $user = User::factory()->create();
        $this->cacheAchievements($user, [], true);

        $response = $this->getJson(route('user.index', ['user' => $user->id]));

        $response->assertOk();
        $response->assertJson([
            'success' => true
        ]);
        $response->assertJsonStructure([
            'data' => [
                'unlocked_achievements',
                'next_available_achievements',
                'current_badge',
                'next_badge',
                'remaining_to_unlock_next_badge'
            ]
        ]);
    }
}
