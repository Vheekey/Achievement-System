<?php

namespace App\Http\Controllers;

use App\Events\CommentWritten;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return $this->jsonResponse(HTTP_SUCCESS, 'User achievements', $this->getCachedDetails($user));
    }

    /**
     * Create user comment
     *
     * @param CommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function comment(CommentRequest $request)
    {
        $comment = Comment::create([
            'body' => $request->comment,
            'user_id' => $request->user()->id,
        ]);

        CommentWritten::dispatch($comment);

        return $this->jsonResponse(HTTP_CREATED, 'Comment Saved');
    }
}
