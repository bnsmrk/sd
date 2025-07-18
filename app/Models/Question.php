<?php

namespace App\Models;

use App\Models\Activity;
use App\Models\StudentAnswer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
 use HasFactory;

    protected $fillable = [
        'activity_id',
        'question', 
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
    public function answers()
{
    return $this->hasMany(StudentAnswer::class);
}
public function studentAnswers()
{
    return $this->hasMany(StudentAnswer::class);
}

}
