<?php

namespace App\Models;

use App\Models\User;
use App\Models\ProficiencyTest;
use Illuminate\Database\Eloquent\Model;

class StudentProficiencyResult extends Model
{
    protected $fillable = [
        'user_id',
        'proficiency_test_id',
        'score',
        'total_points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test()
    {
        return $this->belongsTo(ProficiencyTest::class, 'proficiency_test_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function proficiencyTest()
    {
        return $this->belongsTo(ProficiencyTest::class);
    }
}
