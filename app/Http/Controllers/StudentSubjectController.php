<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
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
        ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name]);

    return Inertia::render('Student/Subjects', [
        'subjects' => $subjects,
    ]);
}

public function show($id)
{
    $subject = Subject::with('modules.activities')->findOrFail($id);

    return Inertia::render('Student/SubjectDetail', [
        'subject' => [
            'id' => $subject->id,
            'name' => $subject->name,
            'modules' => $subject->modules->map(fn ($mod) => [
                'id' => $mod->id,
                'title' => $mod->title,
                'activities' => $mod->activities->map(fn ($act) => [
                    'id' => $act->id,
                    'title' => $act->title,
                    'type' => $act->type,
                    'scheduled_at' => $act->scheduled_at,
                ]),
            ]),
        ],
    ]);
}
}
