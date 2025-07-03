<?php

namespace App\Models;

use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
   protected $fillable = ['year_level_id', 'name'];

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }
}
