<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProficiencyAnswer extends Model
{
    protected $fillable = [
        'user_id',
        'proficiency_test_id',
        'question_id',
        'answer',
        'is_correct',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(ProficiencyTest::class, 'proficiency_test_id');
    }

    public function question()
    {
        return $this->belongsTo(ProficiencyQuestion::class, 'question_id');
    }
}
