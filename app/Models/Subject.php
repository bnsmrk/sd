<?php

namespace App\Models;

use App\Models\User;
use App\Models\Module;
use App\Models\Section;
use App\Models\Activity;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{

    protected $fillable = ['name', 'year_level_id', 'section_id', 'user_id'];

    public function yearLevel() {
        return $this->belongsTo(YearLevel::class);
    }

    public function section() {
        return $this->belongsTo(Section::class);
    }
    public function activities()
{
    return $this->hasMany(Activity::class);
}
public function teacher()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function modules()
{
    return $this->hasMany(Module::class);
}
}
