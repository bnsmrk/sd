<?php

namespace App\Models;

use App\Models\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
 use HasFactory;

    protected $fillable = [
        'activity_id',
        'question',       // ✅ make sure this is included
        'type',
        'options',
        'answer_key',
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function activity() {
        return $this->belongsTo(Activity::class);
    }
}
