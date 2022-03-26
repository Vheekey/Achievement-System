<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];

    public function user()
    {
        return $this->belongsToMany(User::class)->using(LessonUser::class);
    }

    const MILESTONES = [1, 5, 10, 25, 50];

    const ACHIEVEMENTS = [
        1 => 'First Lesson Watched',
        5 => '5 Lessons Watched',
        10 => '10 Lesson Watched',
        25 => '25 Lesson Watched',
        30 => '30 Lesson Watched'
    ];
}
