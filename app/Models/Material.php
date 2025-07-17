<?php

namespace App\Models;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use App\Models\LessonPlanComment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'file_path',   'type', 'year_level_id', 'section_id', 'subject_id', 'user_id','module_id', 'description',
        'video_path',
        'video_link',
    ];

    public function yearLevel() {
        return $this->belongsTo(YearLevel::class);
    }

    public function section() {
        return $this->belongsTo(Section::class);
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function comments()
{
    return $this->hasMany(LessonPlanComment::class);
}
}
