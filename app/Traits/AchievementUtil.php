<?php

namespace App\Traits;

use App\Models\Achievement;

trait AchievementUtil
{
    public static function getMileStoneBadge(int $milestone)
    {
        return self::getMileStoneBadges()[$milestone];
    }

    public static function getMileStoneBadges()
    {
        $milestone_badge = [];

        foreach(Achievement::MILESTONES as $milestone => $value){
            $milestone_badge[$value] = Achievement::BADGES[$milestone];
        }

        return $milestone_badge;
    }

    public static function getBadgeResponse(string $badge)
    {
        return 'Hurray🎉! You have unlocked a badge. '. $badge. ' badge awarded!';
    }

    public static function getAchievementResponse(string $achievement)
    {
        return 'Hurray🎉! You have unlocked an achievement. '. $achievement. ' achievement reached!';
    }
}
