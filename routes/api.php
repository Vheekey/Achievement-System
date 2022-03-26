<?php

use App\Http\Controllers\AchievementsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('users')->controller(AchievementsController::class)->group(function(){
    Route::post('{user}/comment', 'comment')->name('user.comment');
    Route::post('{user}/lesson/{lesson}/watched', 'watchLesson')->name('user.watched-lesson');
});
