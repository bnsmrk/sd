<?php

namespace App\Models;


use App\Models\YearLevel;
use App\Models\ProficiencyQuestion;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentProficiencyAnswer;
use App\Models\StudentProficiencyResult;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProficiencyTest extends Model
{
    protected $fillable = [
        'title',
        'type',
        'year_level_id',
        'scheduled_at',
         'due_date',
        'description',
    ];
    protected $casts = [
        'scheduled_at' => 'datetime',
        'due_date' => 'datetime',
    ];

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }

    public function proficiencyQuestions()
    {
        return $this->hasMany(ProficiencyQuestion::class);
    }

    public function results()
    {
        return $this->hasMany(StudentProficiencyResult::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentProficiencyAnswer::class);
    }
    public function questions()
    {
        return $this->hasMany(ProficiencyQuestion::class);
    }

}
