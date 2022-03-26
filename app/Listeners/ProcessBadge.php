<?php

namespace App\Listeners;

use App\Traits\AchievementUtil;
use App\Traits\Caches;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;

class ProcessBadge
{
    use Caches, AchievementUtil;

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $this->performBadgeCaches($event->user, $event->badge_name);

        return self::getBadgeResponse($event->badge_name);
    }
}
