<?php

namespace App\Models;

use App\Models\User;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Submission extends Model
{
protected $fillable = ['activity_id', 'user_id', 'content', 'file_path','score',
    'graded',];

    public function activity(): BelongsTo {
        return $this->belongsTo(Activity::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
