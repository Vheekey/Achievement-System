<?php

namespace App\Listeners;

use App\Services\Achievement\Types\Lesson;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessLesson
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        return (new Lesson($event->lesson, $event->user))->handle();
    }
}
