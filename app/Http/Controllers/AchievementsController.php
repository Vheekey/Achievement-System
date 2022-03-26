<?php

namespace App\Http\Controllers;

use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\LessonUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class AchievementsController extends Controller
{
    /**
     * Get User Achievement
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return $this->jsonResponse(HTTP_SUCCESS, 'User achievements', $this->getCachedDetails($user));
    }

    /**
     * Create user comment and award achievent
     *
     * @param CommentRequest $request
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function comment(CommentRequest $request, User $user)
    {
        $comment = Comment::create([
            'body' => $request->comment,
            'user_id' => $user->id,
        ]);

        $event_response = CommentWritten::dispatch($comment);

        $response = implode('', $event_response[0]);

        return $this->jsonResponse(HTTP_SUCCESS, 'Comment Saved! '.$response);
    }

    /**
     * Handle lesson watched achievement
     *
     * @param User $user
     * @param Lesson $lesson
     *
     * @return \Illuminate\Http\Response
     */
    public function watchLesson(User $user, Lesson $lesson)
    {
        $watched = $user->watched()->where('id', $lesson->id)->exists();

        if($watched){
            return;
        }

        $details = $user->lessons()->find($lesson->id);

        if(is_null($details)){
            $user->lessons()->attach($lesson);
        }

        $lesson->user()->sync([$user->id => ['watched' => true]]);

        $response = LessonWatched::dispatch($lesson, $user);

        $response = implode('', $response[0]);

        return $this->jsonResponse(HTTP_SUCCESS, $response);
    }
}
