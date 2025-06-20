<?php

namespace App\Models;

use App\Models\Section;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;

class YearLevel extends Model
{
 protected $fillable =['name'];
 public function subjects()
{
    return $this->hasMany(Subject::class);
}
public function sections()
{
    return $this->hasMany(Section::class);
}
}
