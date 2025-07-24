<?php

namespace App\Models;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    protected $fillable = [
        'user_id', 'year_level_id', 'section_id', 'subject_id',
    ];

    public function teacher() {
        return $this->belongsTo(User::class, 'user_id');
    }

     public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function yearLevel() {
        return $this->belongsTo(YearLevel::class);
    }
    public function section()
    {
        return $this->belongsTo(Section::class);
    }
    public function subject() {
        return $this->belongsTo(Subject::class);
    }
    public function subAssignments()
    {
        return $this->hasMany(TeacherSubAssignment::class);
    }
    public function teacherAssignment()
    {
        return $this->belongsTo(TeacherAssignment::class);
    }
}
