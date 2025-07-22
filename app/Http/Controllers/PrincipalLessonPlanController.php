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
        $sectionId = $request->input('section_id');
        $search = $request->input('search');

        $query = Material::with(['uploader:id,name', 'yearLevel:id,name', 'section:id,name', 'comments.user:id,name'])
            ->where('type', 'lesson_plan');

        if ($yearLevelId) {
            $query->where('year_level_id', $yearLevelId);
        }

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('uploader', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $lessonPlans = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Principal/LessonPlans/Index', [
            'lessonPlans' => $lessonPlans,
            'filters' => [
                'year_level_id' => $yearLevelId,
                'section_id' => $sectionId,
                'search' => $search,
            ],
            'yearLevels' => YearLevel::all(['id', 'name']),
            'sections' => Section::all(['id', 'name', 'year_level_id']),
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
