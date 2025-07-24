<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Module;
use App\Models\Subject;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TeacherSubAssignment;

class MaterialController extends Controller
{
    private function getTeacherSubAssignments()
    {
        return TeacherSubAssignment::with(['section', 'subject'])
            ->whereHas('teacherAssignment', fn($q) => $q->where('user_id', Auth::id()))
            ->get();
    }

    public function index(Request $request)
    {
        $assignments = $this->getTeacherSubAssignments();

        $materials = Material::with(['yearLevel', 'section', 'subject', 'user', 'comments.user:id,name'])
            ->where('user_id', Auth::id())
            ->whereIn('section_id', $assignments->pluck('section_id'))
            ->whereIn('subject_id', $assignments->pluck('subject_id'))
            ->when($request->filled('search'), fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Materials/Index', [
            'materials' => $materials,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        $assignments = $this->getTeacherSubAssignments();

        $yearLevels = $assignments->pluck('section.yearLevel')->unique('id')->values();
        $sections = $assignments->pluck('section')->unique('id')->values();
        $subjects = $assignments->pluck('subject')->unique('id')->values();

        $modules = Module::with(['yearLevel', 'subject'])
            ->whereIn('section_id', $assignments->pluck('section_id'))
            ->whereIn('subject_id', $assignments->pluck('subject_id'))
            ->get();

        $subjectSectionMap = $assignments->map(fn($a) => [
            'section_id' => $a->section_id,
            'subject_id' => $a->subject_id,
        ]);

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
            'module_id' => $request->type === 'material' ? 'required|exists:modules,id' : 'nullable',
            'subject_id' => $request->type === 'lesson_plan' ? 'required|exists:subjects,id' : 'nullable',
            'section_id' => 'nullable|exists:sections,id',
            'video' => 'nullable|file|mimetypes:video/mp4,video/x-msvideo,video/quicktime,video/x-matroska|max:51200',
            'video_link' => 'nullable|url|max:255',
        ]);

        $videoPath = $request->hasFile('video') ? $request->file('video')->store('materials/videos', 'public') : null;

        $user = Auth::user();
        $year_level_id = null;
        $section_id = null;
        $subject_id = null;
        $module_id = null;

        if ($request->type === 'material') {
            $module = Module::findOrFail($request->module_id);
            $authorized = TeacherSubAssignment::whereHas('teacherAssignment', fn($q) => $q->where('user_id', $user->id))
                ->where('section_id', $module->section_id)
                ->where('subject_id', $module->subject_id)
                ->exists();

            if (!$authorized) {
                abort(403, 'Unauthorized to upload material for this module.');
            }

            $year_level_id = $module->year_level_id;
            $section_id = $module->section_id;
            $subject_id = $module->subject_id;
            $module_id = $module->id;
        } else {
            $authorized = TeacherSubAssignment::whereHas('teacherAssignment', fn($q) => $q->where('user_id', $user->id))
                ->where('section_id', $request->section_id)
                ->where('subject_id', $request->subject_id)
                ->exists();

            if (!$authorized) {
                abort(403, 'Unauthorized to upload lesson plan.');
            }

            $subject = Subject::findOrFail($request->subject_id);
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
            'module_id' => $module_id,
            'user_id' => $user->id,
            'video_path' => $videoPath,
            'video_link' => $request->video_link,
        ]);

        return redirect()->route('materials.index')->with('success', 'Material uploaded successfully.');
    }

    public function edit(Material $material)
    {
        $assignments = $this->getTeacherSubAssignments();

        $yearLevels = $assignments->pluck('section.yearLevel')->unique('id')->values();
        $sections = $assignments->pluck('section')->unique('id')->values();
        $subjects = $assignments->pluck('subject')->unique('id')->values();

        $modules = Module::with(['yearLevel', 'subject'])
            ->whereIn('section_id', $assignments->pluck('section_id'))
            ->whereIn('subject_id', $assignments->pluck('subject_id'))
            ->get();

        $subjectSectionMap = $assignments->map(fn($a) => [
            'section_id' => $a->section_id,
            'subject_id' => $a->subject_id,
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
            'video' => 'nullable|file|mimetypes:video/mp4,video/x-msvideo,video/quicktime,video/x-matroska|max:51200',
            'video_link' => 'nullable|url|max:255',
        ]);

        $user = Auth::user();

        $authorized = TeacherSubAssignment::whereHas('teacherAssignment', fn($q) => $q->where('user_id', $user->id))
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if (!$authorized) {
            abort(403, 'Unauthorized to update this material.');
        }

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

        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('materials/videos', 'public');
            $material->update(['video_path' => $videoPath]);
        }

        $material->update(['video_link' => $request->video_link]);

        return redirect()->route('materials.index')->with('warning', 'Material updated.');
    }

    public function destroy(Material $material)
    {
        $user = Auth::user();

        $authorized = TeacherSubAssignment::whereHas('teacherAssignment', fn($q) => $q->where('user_id', $user->id))
            ->where('section_id', $material->section_id)
            ->where('subject_id', $material->subject_id)
            ->exists();

        if (!$authorized) {
            abort(403, 'Unauthorized to delete this material.');
        }

        $material->delete();

        return redirect()->route('materials.index')->with('danger', 'Material deleted.');
    }
}
