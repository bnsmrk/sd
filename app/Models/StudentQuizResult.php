<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentQuizResult extends Model
{
    protected $fillable = [
        'user_id',
        'activity_id',
        'score',
        'total_points',
    ];
}
