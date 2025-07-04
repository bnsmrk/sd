<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProficiencyTest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class ProficiencyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'proficiency_test_id',
        'question',
        'type',
        'options',
        'answer_key',
    ];

    protected $casts = [
        'options' => 'array',
        'answer_key' => 'array',
    ];

    public function proficiencyTest()
    {
        return $this->belongsTo(ProficiencyTest::class);
    }
}
