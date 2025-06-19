<?php

namespace App\Models;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;

class StudentQuizResult extends Model
{
    protected $fillable = [
        'user_id',
        'activity_id',
        'score',
        'total_points',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
