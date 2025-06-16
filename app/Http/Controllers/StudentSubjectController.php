<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentSubjectController extends Controller
{

public function index()
{
    $userId = Auth::id();

    $subjects = Student::with('subject.activities')
        ->where('user_id', $userId)
        ->get()
        ->map(function ($student) {
            return [
                'id' => $student->subject->id,
                'name' => $student->subject->name,
                'activities' => $student->subject->activities->map(function ($activity) {
                    return [
                        'id' => $activity->id,
                        'title' => $activity->title,
                        'type' => $activity->type,
                        'date' => $activity->date,
                    ];
                }),
            ];
        });

    return Inertia::render('Student/Subjects', [
        'subjects' => $subjects,
    ]);
}
}
