<?php

namespace App\Traits;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

trait Caches
{
    /**
     * Cache user achievements
     *
     * @param User $user
     * @param array $achievement_details
     * @param boolean $new_user
     * @return void
     */
    public function cacheAchievements(User $user, array $achievement_details = [], bool $new_user = false)
    {
        $keys = $this->userAchievementCacheKey($user);

        $achievements = json_decode(Cache::get('achievements'), true) ?? Achievement::pluck('category')->toArray();

        if($new_user){
            Cache::put($keys['unlocked_achievements'], json_encode([]));

            Cache::put($keys['next_available_achievements'], $achievements);

            Cache::put($keys['current_badge'], Achievement::BADGES[0]);

            Cache::put($keys['next_badge'], Achievement::BADGES[1]);

            Cache::put($keys['remaining_to_unlock_next_badge'], 4);

        }else{
            foreach($achievement_details as $detail => $value){
                Cache::put($keys[$detail], $value);
            }
        }
    }

    /**
     * Get Cached Details
     *
     * @param User $user
     * @return array $cached_details
     */
    public function getCachedDetails(User $user)
    {
        $keys = $this->userAchievementCacheKey($user);

        $cached_details = [];
        foreach($keys as $key => $value){
            $detail = Cache::get($value);
            $cached_details[$key] = ($this->isJson((string) $detail)) ? json_decode($detail, true) : $detail;
        }

        return $cached_details;
    }

    /**
     * Get user's cache keys
     *
     * @param User $user
     * @param string $cache_key
     * @return mixed
     */
    public function userAchievementCacheKey(User $user, string $cache_key = null)
    {
        $keys = [
            'unlocked_achievements' => 'unlocked_achievements-'.$user->id,
            'next_available_achievements' => 'next_available_achievements-'.$user->id,
            'current_badge' => 'current_badge-'.$user->id,
            'next_badge' => 'next_badge-'.$user->id,
            'remaining_to_unlock_next_badge' => 'remaining_to_unlock_next_badge-'.$user->id,
        ];

        return $keys[$cache_key] ?? $keys;
    }

    /**
     * Calculate and cache a user's next available achievement
     *
     * @param User $user
     * @param string $achievement
     * @return void
     */
    public function cacheNextAvailableAchievements(User $user, string $achievement)
    {
        $this->cacheUnlockedAchievements($user, $achievement);

        $user_achievement_key =  $this->userAchievementCacheKey($user, 'unlocked_achievements');
        $user_achievements = json_decode(Cache::get($user_achievement_key), true);

        $achievements = Achievement::select('group')->whereIn('category', $user_achievements)->distinct('group')->get();

        $next_achievement =[];

        foreach($achievements as $key => $value){
            $achievement_milestones = json_decode(Cache::get(strtolower($value->group).'_achievements'));
            $position = array_search($achievement, $achievement_milestones);
            $next_achievement[] = $achievement_milestones[$position+1] ?? null;
        }

        $next_achievement = array_filter($next_achievement);

        $next_available_achievements_key = $this->userAchievementCacheKey($user, 'next_available_achievements');

        Cache::put($next_available_achievements_key, json_encode($next_achievement));
    }

    /**
     * Calculate and Cache User's unlocked achievements
     *
     * @param User $user
     * @param string $achievement
     * @return void
     */
    public function cacheUnlockedAchievements(User $user, string $achievement)
    {
        $user_achievement_key =  $this->userAchievementCacheKey($user, 'unlocked_achievements');
        $user_achievements = json_decode(Cache::get($user_achievement_key), true);

        array_push($user_achievements, $achievement);

        $user_achievements = array_unique($user_achievements);

        Cache::put($user_achievement_key, json_encode($user_achievements));
    }


    /**
     * Calculate and Cache User's remaining achievements to next badge
     *
     * @param User $user
     * @param string $achievement
     * @return void
     */
    public function cacheRemainingAchievementsToNextBadge(User $user, string $achievement)
    {
        $unlocked_achievements_key = $this->userAchievementCacheKey($user, 'unlocked_achievements');
        $unlocked_achievements = json_decode(Cache::get($unlocked_achievements_key), true);
        $unlocked_achievements_count = count($unlocked_achievements);

        $next_remaining = $this->getClosestAchievement($unlocked_achievements_count);

        $remaining = is_null($next_remaining) ? 0 : $next_remaining;

        $remaining_key = $this->getClosestAchievement('remaining_to_unlock_next_badge');

        Cache::put($remaining_key, $remaining);
    }

    /**
     * Cache User's current badge
     *
     * @param User $user
     * @param string $badge
     * @return void
     */
    public function cacheCurrentBadge(User $user, string $badge)
    {
        $key = $this->userAchievementCacheKey($user, 'current_badge');

        Cache::put($key, $badge);
    }

    /**
     * Cache user's next badge
     *
     * @param User $user
     * @return void
     */
    public function cacheNextBadge(User $user)
    {
        $current_badge_key = $this->userAchievementCacheKey($user, 'current_badge');
        $current_badge = Cache::get($current_badge_key);

        $index = array_search($current_badge, Achievement::BADGES);
        $next_badge = is_null($current_badge) ? Achievement::BADGES[1] : Achievement::BADGES[$index+1];

        $next_badge_key = $this->userAchievementCacheKey($user, 'next_badge');
        Cache::put($next_badge_key, $next_badge);
    }

    /**
     * Get the group name of a set of achievements
     *
     * @param string $achievement
     * @return string
     */
    public function getGroupName(string $achievement)
    {
        $achievements = json_decode(Cache::get('achievements'), true);

        return array_search($achievement, array_column($achievements, 'group'));
    }

    /**
     * Get user's next achievements count to a badge
     *
     * @param integer $value
     * @return int
     */
    public function getClosestAchievement(int $value)
    {
        $milestones = Achievement::MILESTONES;

        $closest = null;
        foreach ($milestones as $item) {
            if ($closest === null || abs($value - $closest) > abs($item - $value)) {
                $closest = $item;
            }
        }

        return $closest;
    }

    private function isJson($value) {
        json_decode($value);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}
//moving to next badge
//not moving to next badge
