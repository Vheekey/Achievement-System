<?php

namespace App\Listeners;

use App\Traits\AchievementUtil;
use App\Traits\Caches;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessAchievement
{
    use Caches, AchievementUtil;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->performAchievementCaches($event->user, $event->achievement);

        return self::getAchievementResponse($event->achievement);
    }
}
