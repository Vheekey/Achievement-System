<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return $this->jsonResponse(HTTP_SUCCESS, 'User achievements', $this->getCachedDetails($user));
    }
}
