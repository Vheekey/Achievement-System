<?php

namespace App\Services\Achievements\Types;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Listeners\CommentAchievementUnlocked;
use App\Models\Achievement;
use App\Models\Comment;
use App\Services\Contracts\AchievementTypeInterface;
use App\Traits\Caches;
use Illuminate\Support\Facades\Cache;

class Comments implements AchievementTypeInterface{

    use Caches;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function handle() : void
    {
        if(! $this->isAchievement()){
            return;
        }

        $this->unlockAchievement();
        
        if($this->isBadgeWorthy()){
            BadgeUnlocked::dispatch($this->comment->user);
        }
    }

    public function unlockAchievement() : void
    {
        $achievement = Comment::ACHIEVEMENTS[$this->comment->id];

        $this->performAchievementCaches($this->comment->user, $achievement);
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
