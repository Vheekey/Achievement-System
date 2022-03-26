<?php

namespace App\Services\Achievement\Types;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Models\Achievement;
use App\Models\Comment as CommentModel;
use App\Services\Achievement\Contracts\AchievementTypeInterface;
use App\Traits\AchievementUtil;
use App\Traits\Caches;
use Illuminate\Support\Facades\Cache;

class Comment implements AchievementTypeInterface{

    use Caches, AchievementUtil;

    public function __construct(CommentModel $comment)
    {
        $this->comment = $comment;
    }

    public function handle() : array
    {
        if(! $this->isAchievement()){
            return [];
        }

        $responses = [];

        $responses[] = AchievementUnlocked::dispatch($this->getAchievement(), $this->comment->user)[0];

        if($this->isBadgeWorthy()){
            $responses[] = BadgeUnlocked::dispatch($this->getBadge(), $this->comment->user)[0];
        }

        return $responses;
    }

    public function getAchievement() : string
    {
        return CommentModel::ACHIEVEMENTS[$this->comment->id];
    }

    public function getBadge() : string
    {
        $user_unlocked_achievements_key = $this->getUserAchievementCacheKey($this->comment->user, 'unlocked_achievements');
        $user_unlocked_achievements = json_decode(Cache::get($user_unlocked_achievements_key), true);

        $count = count($user_unlocked_achievements);
        $badge = AchievementUtil::getMileStoneBadge($count);

        return $badge;
    }

    private function isBadgeWorthy()
    {
        $user_unlocked_achievements_key = $this->getUserAchievementCacheKey($this->comment->user, 'unlocked_achievements');
        $user_unlocked_achievements = json_decode(Cache::get($user_unlocked_achievements_key), true);

        return in_array(count($user_unlocked_achievements), Achievement::MILESTONES);
    }

    private function isAchievement() : bool
    {
        return in_array($this->comment->id, CommentModel::MILESTONES);
    }
}
