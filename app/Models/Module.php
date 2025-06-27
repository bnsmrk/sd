<?php

namespace App\Models;

use App\Models\Section;
use App\Models\Subject;
use App\Models\Activity;
use App\Models\Material;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name', 'year_level_id', 'section_id', 'subject_id'];

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }
    public function materials()
    {
        return $this->hasMany(Material::class);
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
