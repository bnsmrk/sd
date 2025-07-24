<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherSubAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_assignment_id',
        'subject_id',
        'section_id',
    ];

    public function teacherAssignment()
    {
        return $this->belongsTo(TeacherAssignment::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
