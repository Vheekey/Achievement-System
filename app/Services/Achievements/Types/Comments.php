<?php

namespace App\Services\Achievements\Types;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Listeners\CommentAchievementUnlocked;
use App\Models\Achievement;
use App\Models\Comment;
use App\Services\Contracts\AchievementTypeInterface;
use App\Traits\AchievementUtil;
use App\Traits\Caches;
use Illuminate\Support\Facades\Cache;

class Comments implements AchievementTypeInterface{

    use Caches, AchievementUtil;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function handle() : void
    {
        if(! $this->isAchievement()){
            return;
        }

        AchievementUnlocked::dispatch($this->getAchievement(), $this->comment->user);

        if($this->isBadgeWorthy()){
            BadgeUnlocked::dispatch($this->getBadge(), $this->comment->user);
        }
    }

    public function getAchievement() : string
    {
        return Comment::ACHIEVEMENTS[$this->comment->id];
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
        return in_array($this->comment->id, Comment::MILESTONES);
    }
}
