<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Module;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Material;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    private function getTeacherAssignments()
    {
        return Auth::user()
            ->teacherAssignments()
            ->with(['yearLevel', 'section', 'subject'])
            ->get();
    }

    public function index()
    {
        $assignments = $this->getTeacherAssignments();

        $materials = Material::with(['yearLevel', 'section', 'subject', 'user','comments.user:id,name'])
            ->whereIn('year_level_id', $assignments->pluck('year_level_id'))
            ->whereIn('subject_id', $assignments->pluck('subject_id'))
            ->latest()
            ->get();

        return Inertia::render('Materials/Index', [
            'materials' => $materials,
        ]);
    }

    public function create()
{
    $assignments = $this->getTeacherAssignments();

    $yearLevels = $assignments->pluck('yearLevel')->unique('id')->values();
    $sections = $assignments->pluck('section')->unique('id')->values();
    $subjects = $assignments->pluck('subject')->unique('id')->values();
    $modules = Module::whereIn('year_level_id', $assignments->pluck('year_level_id'))
        ->whereIn('subject_id', $assignments->pluck('subject_id'))
        ->with(['yearLevel', 'subject'])
        ->get();

    // Create subject-section pairs
    $subjectSectionMap = $assignments->map(function ($assignment) {
        return [
            'section_id' => $assignment->section_id,
            'subject_id' => $assignment->subject_id,
        ];
    });

    return Inertia::render('Materials/Create', [
        'modules' => $modules,
        'subjects' => $subjects,
        'yearLevels' => $yearLevels,
        'sections' => $sections,
        'subjectSectionMap' => $subjectSectionMap,
    ]);
}




    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:material,lesson_plan',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'module_id' => 'nullable|exists:modules,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        $user = Auth::user();
        $year_level_id = null;
        $section_id = null;
        $subject_id = null;

        if ($request->type === 'material') {
            $module = Module::findOrFail($request->module_id);

            $authorized = $user->teacherAssignments()
                ->where('year_level_id', $module->year_level_id)
                ->where('subject_id', $module->subject_id)
                ->exists();

            if (!$authorized) {
                abort(403, 'Unauthorized to upload material for this module.');
            }

            $year_level_id = $module->year_level_id;
            $section_id = $module->section_id;
            $subject_id = $module->subject_id;
        } else {
            $subject = Subject::findOrFail($request->subject_id);

            $authorized = $user->teacherAssignments()
                ->where('year_level_id', $subject->year_level_id)
                ->where('subject_id', $subject->id)
                ->exists();

            if (!$authorized) {
                abort(403, 'Unauthorized to upload lesson plan for this subject.');
            }

            $year_level_id = $subject->year_level_id;
            $section_id = $request->section_id;
            $subject_id = $subject->id;
        }

        $path = $request->file('file')->store('materials', 'public');

        Material::create([
            'title' => $request->title,
            'description' => $request->description,

            'type' => $request->type,
            'file_path' => $path,
            'year_level_id' => $year_level_id,
            'section_id' => $section_id,
            'subject_id' => $subject_id,
            'module_id' => $request->module_id,
            'user_id' => $user->id,
        ]);

        return redirect()->route('materials.index')->with('success', 'Material uploaded successfully.');
    }

   public function edit(Material $material)
{
    \Log::info('EDIT page hit: material id=' . $material->id);

    $user = Auth::user(); // ✅ fix: define $user

    $assignments = $this->getTeacherAssignments();

    $yearLevels = $assignments->pluck('yearLevel')->unique('id')->values();
    $sections = $assignments->pluck('section')->unique('id')->values();
    $subjects = $assignments->pluck('subject')->unique('id')->values();

    $modules = Module::whereIn('year_level_id', $assignments->pluck('year_level_id'))
        ->whereIn('subject_id', $assignments->pluck('subject_id'))
        ->with(['yearLevel', 'subject'])
        ->get();

    // ✅ Corrected subject-section map using $assignments, not $user
    $subjectSectionMap = $assignments->map(fn ($assignment) => [
        'section_id' => $assignment->section_id,
        'subject_id' => $assignment->subject_id,
    ]);

    return Inertia::render('Materials/Edit', [
        'material' => $material,
        'modules' => $modules,
        'subjects' => $subjects,
        'yearLevels' => $yearLevels,
        'sections' => $sections,
        'subjectSectionMap' => $subjectSectionMap,
    ]);
}



 public function update(Request $request, Material $material)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'type' => 'required|in:material,lesson_plan',
        'year_level_id' => 'required|exists:year_levels,id',
        'subject_id' => 'required|exists:subjects,id',
        'section_id' => 'nullable|exists:sections,id',
        'module_id' => 'nullable|exists:modules,id',
    ]);

    $user = Auth::user();

    $authorized = $user->teacherAssignments()
        ->where('year_level_id', $request->year_level_id)
        ->where('subject_id', $request->subject_id)
        ->exists();

    if (!$authorized) {
        abort(403, 'Unauthorized to update this material.');
    }

    \Log::info('Updating material', $request->all());

    $material->update([
        'title' => $request->title,
        'description' => $request->description,
        'type' => $request->type,
        'year_level_id' => $request->year_level_id,
        'subject_id' => $request->subject_id,
        'section_id' => $request->section_id,
        'module_id' => $request->module_id,
    ]);

    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('materials', 'public');
        $material->update(['file_path' => $path]);
    }

    return redirect()->route('materials.index')->with('success', 'Material updated.');
}


    public function destroy(Material $material)
    {
        $user = Auth::user();

        $authorized = $user->teacherAssignments()
            ->where('year_level_id', $material->year_level_id)
            ->where('subject_id', $material->subject_id)
            ->exists();

        if (!$authorized) {
            abort(403, 'Unauthorized to delete this material.');
        }

        $material->delete();

        return redirect()->route('materials.index')->with('success', 'Material deleted.');
    }
}
