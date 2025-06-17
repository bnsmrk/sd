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

        $subjects = Student::with('subject.modules.activities')
            ->where('user_id', $userId)
            ->get()
            ->map(function ($student) {
                $subject = $student->subject;

                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'activities' => $subject->modules
                        ->flatMap(function ($module) {
                            return $module->activities->map(function ($activity) {
                                return [
                                    'id' => $activity->id,
                                    'title' => $activity->title,
                                    'type' => $activity->type,
                                    'scheduled_at' => $activity->scheduled_at,
                                ];
                            });
                        }),
                ];
            });

        return Inertia::render('Student/Subjects', [
            'subjects' => $subjects,
        ]);
    }
}
