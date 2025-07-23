<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeadAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'year_level_id',
        'section_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
