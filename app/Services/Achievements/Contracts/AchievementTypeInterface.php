<?php

namespace App\Services\Contracts;

interface AchievementTypeInterface
{
    public function handle() : void;

    public function getAchievement() : string;

    public function getBadge() : string;
}
