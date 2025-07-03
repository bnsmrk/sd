<?php

namespace App\Models;


use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProficiencyTest extends Model
{protected $fillable = [
        'title',
        'type',
        'year_level_id',
        'scheduled_at',
        'description',
    ];

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }


}
