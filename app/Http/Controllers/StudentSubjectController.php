<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Models\StudentQuizResult;
use Illuminate\Support\Facades\Auth;

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

    $quizResults = StudentQuizResult::where('user_id', $userId)->get()->keyBy('activity_id');
    $essaySubmissions = Submission::where('user_id', $userId)->get()->keyBy('activity_id');

    $moduleCount = $subject->modules->count();
    $subjectProgress = 0;

   $modules = $subject->modules->map(function ($mod) use ($quizResults, $essaySubmissions) {
    $activities = $mod->activities;
    $activityCount = $activities->count();

    $completedCount = 0;

    $activityData = $activities->map(function ($act) use ($quizResults, $essaySubmissions, &$completedCount) {
        $result = $quizResults->get($act->id);
        $submission = $essaySubmissions->get($act->id);

        $isCompleted = $result !== null || $submission !== null;
        if ($isCompleted) $completedCount++;

        return [
            'id' => $act->id,
            'title' => $act->title,
            'type' => $act->type,
            'scheduled_at' => $act->scheduled_at?->format('Y-m-d H:i'),
            'score' => $result?->score,
            'total_points' => $result?->total_points,
            'submitted' => $submission !== null,
            'essay_score' => $submission?->score,
        ];
    });

    $moduleProgress = $activityCount > 0 ? ($completedCount / $activityCount) * 100 : 0;

    return [
        'id' => $mod->id,
        'title' => $mod->title,
        'progress' => round($moduleProgress, 2),
        'materials' => $mod->materials->map(fn ($mat) => [
            'id' => $mat->id,
            'title' => $mat->title,
            'description' => $mat->description,
            'file_path' => $mat->file_path,
            'uploaded_by' => $mat->user->name ?? 'Unknown',
        ]),
        'activities' => $activityData,
    ];
});


    return Inertia::render('Student/SubjectDetail', [
        'subject' => [
            'id' => $subject->id,
            'name' => $subject->name,
            'modules' => $modules,
        ],
        'progress' => round($subjectProgress, 2),
    ]);
}

}
