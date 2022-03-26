<?php

namespace App\Services\Achievement\Contracts;

interface AchievementTypeInterface
{
    public function handle() : array;

    public function getAchievement() : string;

    public function getBadge() : string;
}
