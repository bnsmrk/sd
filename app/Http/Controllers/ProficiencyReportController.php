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
   public function index(Request $request)
{
    $teacherId = Auth::id();

    // Load teacher's assignments with relationships
    $assignments = TeacherAssignment::with(['yearLevel', 'section', 'subject'])
        ->where('user_id', $teacherId)
        ->get();

    // Unique Year Levels
    $yearLevels = $assignments->pluck('yearLevel')->filter()->unique('id')->values()->map(function ($yearLevel) {
        return [
            'id' => $yearLevel->id,
            'name' => $yearLevel->name,
        ];
    });

    // Unique Sections
    $sections = $assignments->pluck('section')->filter()->unique('id')->values()->map(function ($section) {
        return [
            'id' => $section->id,
            'name' => $section->name,
            'year_level_id' => $section->year_level_id,
        ];
    });

    // Unique Subjects
    $subjects = $assignments->map(function ($assignment) {
        if (!$assignment->subject || !$assignment->section) return null;

        return [
            'id' => $assignment->subject->id,
            'name' => $assignment->subject->name,
            'section_id' => $assignment->section->id,
        ];
    })->filter()->unique('id')->values();

    // Request filters
    $yearLevelId = $request->input('year_level_id');
    $sectionId   = $request->input('section_id');
    $subjectId   = $request->input('subject_id');
    $moduleId    = $request->input('module_id');
    $type        = $request->input('type', 'quiz');

    // Load matching modules
    $modules = [];
    if ($yearLevelId && $sectionId && $subjectId) {
        $modules = Module::where('year_level_id', $yearLevelId)
            ->where('section_id', $sectionId)
            ->where('subject_id', $subjectId)
            ->get(['id', 'name'])
            ->toArray();
    }

    // Load results
    $results = collect();
    if ($moduleId && $type) {
        $results = StudentQuizResult::with(['user', 'activity.module'])
            ->whereHas('activity', function ($q) use ($moduleId, $type) {
                $q->where('module_id', $moduleId)
                  ->where('type', $type);
            })
            ->get()
            ->groupBy('user_id')
            ->map(function ($group) {
                $student = $group->first()->user;
                $score = $group->sum('score');
                $total = $group->sum('total_points');
                return [
                    'student' => $student?->name ?? 'Unknown',
                    'score' => $score,
                    'total' => $total,
                    'average' => $total > 0 ? round(($score / $total) * 100, 2) : 0,
                ];
            })
            ->values();
    }

    return Inertia::render('ProficiencyReport/Index', [
        'yearLevels' => $yearLevels,
        'sections'   => $sections,
        'subjects'   => $subjects,
        'modules'    => $modules,
        'results'    => $results,
        'filters'    => [
            'year_level_id' => $yearLevelId,
            'section_id'    => $sectionId,
            'subject_id'    => $subjectId,
            'module_id'     => $moduleId,
            'type'          => $type,
        ],
    ]);
}

}
