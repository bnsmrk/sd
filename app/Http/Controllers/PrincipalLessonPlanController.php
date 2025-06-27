<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Material;
use App\Models\YearLevel;
use Illuminate\Http\Request;

class PrincipalLessonPlanController extends Controller
{
    public function index(Request $request)
    {
        $yearLevelId = $request->input('year_level_id');
        $sectionId   = $request->input('section_id');

        // Only fetch lesson plans if both filters are selected
        $lessonPlans = [];

        if ($yearLevelId && $sectionId) {
            $lessonPlans = Material::with(['uploader:id,name', 'yearLevel:id,name', 'section:id,name'])
                ->where('type', 'lesson_plan')
                ->where('year_level_id', $yearLevelId)
                ->where('section_id', $sectionId)
                ->latest()
                ->get();
        }

        return Inertia::render('Principal/LessonPlans/Index', [
            'lessonPlans' => $lessonPlans,
            'filters' => [
                'year_level_id' => $yearLevelId,
                'section_id' => $sectionId,
            ],
            'yearLevels' => YearLevel::all(['id', 'name']),
            'sections'   => Section::all(['id', 'name', 'year_level_id']),
        ]);
    }
}
