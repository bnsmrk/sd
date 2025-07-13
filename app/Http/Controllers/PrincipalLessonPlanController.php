<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Material;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use App\Models\LessonPlanComment;

class PrincipalLessonPlanController extends Controller
{
    public function index(Request $request)
    {
        $yearLevelId = $request->input('year_level_id');
        $sectionId   = $request->input('section_id');

        $lessonPlans = [];

        if ($yearLevelId && $sectionId) {
            $lessonPlans = Material::with(['uploader:id,name', 'yearLevel:id,name', 'section:id,name'])
                ->where('type', 'lesson_plan')
                ->where('year_level_id', $yearLevelId)
                ->where('section_id', $sectionId)
                ->latest()
                ->get();
        }
        $lessonPlans = Material::with([
            'uploader:id,name',
            'yearLevel:id,name',
            'section:id,name',
            'comments.user:id,name'
        ])
            ->where('type', 'lesson_plan')
            ->where('year_level_id', $yearLevelId)
            ->where('section_id', $sectionId)
            ->latest()
            ->get();
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

    public function storeComment(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'comment' => 'required|string|max:1000',
        ]);

        LessonPlanComment::create([
            'material_id' => $validated['material_id'],
            'user_id' => auth()->id(),
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Comment added.');
    }
}
