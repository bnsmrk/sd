<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Module;
use App\Models\StudentQuizResult;
use App\Models\TeacherAssignment;

class ProficiencyReportController extends Controller
{
   // inside ProficiencyReportController.php

public function index(Request $request)
{
    $teacherId = Auth::id();

    $assignments = TeacherAssignment::with(['yearLevel', 'section', 'subject'])
        ->where('user_id', $teacherId)
        ->get();

    $yearLevels = $assignments->pluck('yearLevel')->filter()->unique('id')->values()->map(fn($yl) => [
        'id' => $yl->id,
        'name' => $yl->name,
    ]);

    $sections = $assignments->pluck('section')->filter()->unique('id')->values()->map(fn($sec) => [
        'id' => $sec->id,
        'name' => $sec->name,
        'year_level_id' => $sec->year_level_id,
    ]);

    $subjects = $assignments->map(function ($assignment) {
        if (!$assignment->subject || !$assignment->section) return null;
        return [
            'id' => $assignment->subject->id,
            'name' => $assignment->subject->name,
            'section_id' => $assignment->section->id,
        ];
    })->filter()->unique('id')->values();

    $yearLevelId = $request->input('year_level_id');
    $sectionId   = $request->input('section_id');
    $subjectId   = $request->input('subject_id');
    $moduleId    = $request->input('module_id');
    $type        = $request->input('type', 'quiz');

    // Load matching modules
    $modules = [];
    if ($subjectId) {
        $modules = Module::where('subject_id', $subjectId)
            ->whereHas('activities', fn($q) => $q->where('type', $type))
            ->get(['id', 'name', 'subject_id']);
    }

    $resultsByActivity = [];

    if ($subjectId && $moduleId && $type) {
        $activities = \App\Models\Activity::with(['studentQuizResults.user'])
            ->where('module_id', $moduleId)
            ->where('type', $type)
            ->get();

        $resultsByActivity = $activities->map(function ($activity) {
            $entries = $activity->studentQuizResults->map(function ($result) {
                return [
                    'student' => $result->user->name ?? 'Unknown',
                    'score' => $result->score,
                    'total' => $result->total_points,
                    'average' => $result->total_points > 0
                        ? round(($result->score / $result->total_points) * 100, 2)
                        : 0,
                ];
            });

            return [
                'activity_title' => $activity->title,
                'entries' => $entries,
            ];
        });
    }

    return Inertia::render('ProficiencyReport/Index', [
        'yearLevels' => $yearLevels,
        'sections'   => $sections,
        'subjects'   => $subjects,
        'modules'    => $modules,
        'resultsByActivity' => $resultsByActivity,
        'filters'    => compact('yearLevelId', 'sectionId', 'subjectId', 'moduleId', 'type'),
    ]);
}


}
