<?php

namespace App\Providers;

use App\Events\AchievementUnlocked;
use App\Events\LessonWatched;
use App\Events\CommentWritten;
use App\Listeners\CommentAchievementUnlocked;
use App\Listeners\ProcessAchievement;
use App\Listeners\ProcessBadge;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        CommentWritten::class => [
            CommentAchievementUnlocked::class
        ],
        LessonWatched::class => [
            //
        ],
        BadgeUnlocked::class => [
            ProcessBadge::class
        ],
        AchievementUnlocked::class => [
            ProcessAchievement::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
