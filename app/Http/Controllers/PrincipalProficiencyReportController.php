<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Module;
use App\Models\Activity;

class PrincipalProficiencyReportController extends Controller
{
    public function index(Request $request)
{
    // Filters from query
    $yearLevelId = $request->input('year_level_id');
    $sectionId   = $request->input('section_id');
    $subjectId   = $request->input('subject_id');
    $moduleId    = $request->input('module_id');
    $type        = $request->input('type', 'quiz');

    // Static lists
    $yearLevels = YearLevel::select('id', 'name')->get();
    $sections   = Section::select('id', 'name', 'year_level_id')->get();

    // Subject filtering based on selected year level and section
    $subjectQuery = Subject::select('id', 'name', 'section_id', 'year_level_id');

    if ($yearLevelId) {
        $subjectQuery->where('year_level_id', $yearLevelId);
    }

    if ($sectionId) {
        $subjectQuery->where(function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId)->orWhereNull('section_id'); // include general subjects
        });
    }

    $subjects = $subjectQuery->get();

    // Modules
    $modules = [];
    if ($subjectId) {
        $modules = Module::where('subject_id', $subjectId)
            ->whereHas('activities', fn ($q) => $q->where('type', $type))
            ->get(['id', 'name', 'subject_id']);
    }

    // Results
    $resultsByActivity = [];
    if ($subjectId && $moduleId && $type) {
        $activities = Activity::with(['studentQuizResults.user'])
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
        })->toArray();
    }

    return Inertia::render('Principal/Index', [
        'yearLevels' => $yearLevels,
        'sections'   => $sections,
        'subjects'   => $subjects,
        'modules'    => $modules,
        'resultsByActivity' => $resultsByActivity,
        'filters'    => compact('yearLevelId', 'sectionId', 'subjectId', 'moduleId', 'type'),
    ]);
}


    public function exportPdf(Request $request)
    {
        $subjectId = $request->input('subject_id');
        $moduleId  = $request->input('module_id');
        $type      = $request->input('type', 'quiz');

        $resultsByActivity = [];

        if ($subjectId && $moduleId && $type) {
            $activities = Activity::with(['studentQuizResults.user'])
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
            })->toArray();
        }

        $pdf = Pdf::loadView('pdf.proficiency_report', [
            'resultsByActivity' => $resultsByActivity,
            'type' => ucfirst($type),
        ]);

        return $pdf->download('proficiency_report.pdf');
    }
}
