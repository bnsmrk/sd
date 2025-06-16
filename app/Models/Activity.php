<?php

namespace App\Models;

use App\Models\Section;
use App\Models\Subject;
use App\Models\Question;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
     use HasFactory;

    protected $fillable = [
        'title', 'type', 'scheduled_at',
        'year_level_id', 'section_id', 'subject_id',
    ];

    public function questions() {
        return $this->hasMany(Question::class);
    }

    public function yearLevel()
{
    return $this->belongsTo(YearLevel::class);
}

public function section()
{
    return $this->belongsTo(Section::class);
}

public function subject()
{
    return $this->belongsTo(Subject::class);
}
}
