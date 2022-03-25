<?php

namespace App\Services\Contracts;

interface AchievementTypeInterface
{
    public function handle() : void;

    public function unlockAchievement() : void;
}
