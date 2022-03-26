<?php

namespace App\Listeners;

use App\Services\Achievement\Types\Comment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProcessComment
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        return (new Comment($event->comment))->handle();
    }
}
