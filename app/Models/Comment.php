<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory;

    const MILESTONES = [1, 3, 5, 10, 20];

    const ACHIEVEMENTS = [
        1 => 'First Comment Written',
        3 => '3 Comments Written',
        5 => '5 Comments Written',
        10 => '10 Comments Written',
        20 => '20 Comments Written'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'user_id'
    ];

    /**
     * Get the user that wrote the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
