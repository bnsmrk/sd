<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentQuizResult;

class StudentSubjectController extends Controller
{
   public function index()
{
    $userId = Auth::id();

    $subjects = Student::with('subject')
        ->where('user_id', $userId)
        ->get()
        ->pluck('subject')
        ->map(function ($subject) {
            $assignment = \App\Models\TeacherAssignment::with('user')
                ->where('subject_id', $subject->id)
                ->first();

            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'teacher' => $assignment?->user?->name ?? 'Unknown',
            ];
        });

    return Inertia::render('Student/Subjects', [
        'subjects' => $subjects,
    ]);
}

public function show($id)
{
    $userId = Auth::id();

    $subject = Subject::with(['modules.activities', 'modules.materials.user'])->findOrFail($id);

    $quizResults = StudentQuizResult::where('user_id', $userId)->get()
        ->keyBy('activity_id');

    return Inertia::render('Student/SubjectDetail', [
        'subject' => [
            'id' => $subject->id,
            'name' => $subject->name,
            'modules' => $subject->modules->map(fn ($mod) => [
                'id' => $mod->id,
                'title' => $mod->title,
                'materials' => $mod->materials->map(fn ($mat) => [
                    'id' => $mat->id,
                    'title' => $mat->title,
                    'description' => $mat->description,
                    'file_path' => $mat->file_path,
                    'uploaded_by' => $mat->user->name ?? 'Unknown',
                ]),
                'activities' => $mod->activities->map(function ($act) use ($quizResults) {
                    $result = $quizResults->get($act->id);

                    return [
                        'id' => $act->id,
                        'title' => $act->title,
                        'type' => $act->type,
                        'scheduled_at' => $act->scheduled_at,
                        'score' => $result?->score,
                        'total_points' => $result?->total_points,
                    ];
                }),
            ]),
        ],
    ]);
}

}
