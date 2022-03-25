<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    //badges
    const BADGES = [
        'Beginner',
        'Intermediate',
        'Advanced',
        'Master'
    ];

    //Groups
    const LESSON = 'Lesson';
    const COMMENT = 'Comment';

    //milestones
    const MILESTONES = [0, 4, 8, 10];
}
