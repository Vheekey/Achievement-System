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
}
