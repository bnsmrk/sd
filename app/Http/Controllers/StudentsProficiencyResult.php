<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\YearLevel;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StudentProficiencyResult;
use App\Models\HeadAssignment;
use Illuminate\Support\Facades\Auth;

class StudentsProficiencyResult extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $role = $user->role;

        if ($role === 'principal') {
            $yearLevels = YearLevel::select('id', 'name')->get();
            $sections   = Section::select('id', 'name', 'year_level_id')->get();
        } else {
            $assignments = HeadAssignment::where('user_id', $userId)->get();
            $yearLevelIds = $assignments->pluck('year_level_id')->unique();
            $sectionIds   = $assignments->pluck('section_id')->filter()->unique();

            $yearLevels = YearLevel::whereIn('id', $yearLevelIds)->select('id', 'name')->get();
            $sections   = Section::whereIn('id', $sectionIds)->select('id', 'name', 'year_level_id')->get();
        }

        $yearLevelId = $request->input('year_level_id');
        $sectionId   = $request->input('section_id');
        $type        = $request->input('type');

        $individuals = [];
        $sectionsAvg = [];
        $yearLevelAverage = null;

        if ($yearLevelId && $type) {
            $studentsQuery = Student::with(['user', 'section'])
                ->whereHas('user', fn($q) => $q->where('role', 'student'))
                ->where('year_level_id', $yearLevelId);

            if ($sectionId) {
                $studentsQuery->where('section_id', $sectionId);
            }

            $students = $studentsQuery->get()->unique('user_id');

            $individuals = $students->map(function ($student) use ($type) {
                $results = StudentProficiencyResult::where('user_id', $student->user_id)
                    ->whereHas('proficiencyTest', fn($q) => $q->where('type', $type))
                    ->get();

                $average = $results->count() > 0
                    ? round($results->avg(fn($r) => $r->total_points > 0 ? ($r->score / $r->total_points) * 100 : 0), 2)
                    : 0;

                return [
                    'student' => [
                        'id'     => $student->user->id,
                        'name'   => $student->user->name,
                        'section' => $student->section ? [
                            'id' => $student->section->id,
                            'name' => $student->section->name,
                        ] : null,
                    ],
                    'average' => $average,
                ];
            })->values();

            $sectionsAvg = $individuals->groupBy(fn($item) => $item['student']['section']['id'] ?? null)
                ->map(function ($group) {
                    $section = $group->first()['student']['section'] ?? ['id' => null, 'name' => 'N/A'];
                    $avg = round($group->avg('average'), 2);

                    return [
                        'section' => $section,
                        'average' => $avg,
                    ];
                })->values();

            $yearLevelAverage = round($individuals->avg('average'), 2);
        }

        return Inertia::render('Head/StudentsProficiencyResult', [
            'yearLevels' => $yearLevels,
            'sections' => $sections,
            'individuals' => $individuals,
            'sectionsAvg' => $sectionsAvg,
            'yearLevelAverage' => $yearLevelAverage,
            'filters' => compact('yearLevelId', 'sectionId', 'type'),
        ]);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;
        $role = $user->role;

        $yearLevelId = $request->input('year_level_id');
        $sectionId   = $request->input('section_id');
        $type        = $request->input('type');

        if ($role === 'head') {
            $assignments = HeadAssignment::where('user_id', $userId)->get();
            $yearLevelIds = $assignments->pluck('year_level_id')->unique();
            $sectionIds   = $assignments->pluck('section_id')->filter()->unique();

            if (!in_array($yearLevelId, $yearLevelIds->toArray())) {
                abort(403, 'You are not authorized to export this year level.');
            }

            if ($sectionId && !in_array($sectionId, $sectionIds->toArray())) {
                abort(403, 'You are not authorized to export this section.');
            }
        }

        $individuals = collect();
        $sectionsAvg = collect();
        $yearLevelAverage = 0;

        if ($yearLevelId && $type) {
            $studentsQuery = Student::with(['user', 'section'])
                ->whereHas('user', fn($q) => $q->where('role', 'student'))
                ->where('year_level_id', $yearLevelId);

            if ($sectionId) {
                $studentsQuery->where('section_id', $sectionId);
            }

            $students = $studentsQuery->get()->unique('user_id');

            $individuals = $students->map(function ($student) use ($type) {
                $results = StudentProficiencyResult::where('user_id', $student->user_id)
                    ->whereHas('proficiencyTest', fn($q) => $q->where('type', $type))
                    ->get();

                $average = $results->count() > 0
                    ? round($results->avg(fn($r) => $r->total_points > 0 ? ($r->score / $r->total_points) * 100 : 0), 2)
                    : 0;

                return [
                    'student' => [
                        'id'     => $student->user->id,
                        'name'   => $student->user->name,
                        'section' => $student->section ? [
                            'id' => $student->section->id,
                            'name' => $student->section->name,
                        ] : null,
                    ],
                    'average' => $average,
                ];
            })->values();

            $sectionsAvg = $individuals->groupBy(fn($item) => $item['student']['section']['id'] ?? null)
                ->map(function ($group) {
                    $section = $group->first()['student']['section'] ?? ['id' => null, 'name' => 'N/A'];
                    $avg = round($group->avg('average'), 2);

                    return [
                        'section' => $section,
                        'average' => $avg,
                    ];
                })->values();

            $yearLevelAverage = round($individuals->avg('average'), 2);
        }

        $pdf = Pdf::loadView('pdf.student_proficiency_result', [
            'individuals' => $individuals,
            'sectionsAvg' => $sectionsAvg,
            'yearLevelAverage' => $yearLevelAverage,
            'yearLevelName' => optional(YearLevel::find($yearLevelId))->name,
            'sectionName' => $sectionId ? optional(Section::find($sectionId))->name : null,
            'type' => ucfirst($type),
        ]);

        return $pdf->download('student_proficiency_result.pdf');
    }
}
