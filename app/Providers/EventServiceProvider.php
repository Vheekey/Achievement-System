<?php

namespace App\Providers;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\LessonWatched;
use App\Events\CommentWritten;
use App\Listeners\ProcessAchievement;
use App\Listeners\ProcessBadge;
use App\Listeners\ProcessComment;
use App\Listeners\ProcessLesson;
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
            ProcessComment::class
        ],
        LessonWatched::class => [
            ProcessLesson::class
        ],
        AchievementUnlocked::class => [
            ProcessAchievement::class
        ],
        BadgeUnlocked::class => [
           ProcessBadge::class,
        ]
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
