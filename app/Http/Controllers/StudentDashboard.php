<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\ProficiencyTest;
use App\Models\StudentProficiencyResult;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $student = Student::where('user_id', $userId)->first();

        $proficiencyTests = [];
        $takenTestIds = [];

        if ($student && $student->year_level_id) {
            $proficiencyTests = ProficiencyTest::where('year_level_id', $student->year_level_id)
                ->orderBy('scheduled_at', 'asc')
                ->get()
                ->map(function ($test) {
                    return [
                        'id' => $test->id,
                        'title' => $test->title,
                        'type' => ucfirst($test->type),
                        'scheduled_at' => $test->scheduled_at->format('Y-m-d H:i'),
                        'description' => $test->description,
                    ];
                });

            $takenTestIds = StudentProficiencyResult::where('user_id', $userId)
                ->pluck('proficiency_test_id')
                ->toArray();
        }

        return Inertia::render('Student/StudentDashboard', [
            'proficiencyTests' => $proficiencyTests,
            'takenTestIds' => $takenTestIds,
        ]);
    }
}
