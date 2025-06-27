<?php

namespace App\Models;

use App\Models\User;
use App\Models\Material;
use Illuminate\Database\Eloquent\Model;

class LessonPlanComment extends Model
{
    protected $fillable = ['material_id', 'user_id', 'comment'];

    public function lessonPlan()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
