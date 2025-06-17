<?php

namespace App\Models;

use App\Models\Module;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Question;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    protected $fillable = ['title', 'module_id','scheduled_at'];


    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}

