<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Module;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index()
    {
        $teacherId = Auth::id();

    // Get assigned combinations for this teacher
    $assignments = TeacherAssignment::where('user_id', $teacherId)->get();

    // Extract valid module IDs based on assignments
    $allowedModuleIds = Module::whereIn('year_level_id', $assignments->pluck('year_level_id'))
        ->whereIn('section_id', $assignments->pluck('section_id'))
        ->whereIn('subject_id', $assignments->pluck('subject_id'))
        ->pluck('id');

    // Only load activities linked to allowed modules
    $activities = Activity::with('module.yearLevel', 'module.section', 'module.subject')
        ->whereIn('module_id', $allowedModuleIds)
        ->get();

    $formatted = $activities->map(function ($a) {
        return [
            'id' => $a->id,
            'title' => $a->title,
            'type' => $a->type,
            'scheduled_at' => \Carbon\Carbon::parse($a->scheduled_at)->format('Y-m-d H:i'),
            'year_level' => $a->module->yearLevel->name ?? '',
            'section' => $a->module->section->name ?? '',
            'subject' => $a->module->subject->name ?? '',
        ];
    });

    return Inertia::render('Activities/Index', [
        'activities' => $formatted,
    ]);
    }

    public function create()
    {
       $user = Auth::user();

    // Get assignments
    $assignments = $user->teacherAssignments()->get();

    // Filter only allowed modules
    $modules = Module::with(['yearLevel', 'section', 'subject'])
        ->whereIn('year_level_id', $assignments->pluck('year_level_id'))
        ->whereIn('section_id', $assignments->pluck('section_id'))
        ->whereIn('subject_id', $assignments->pluck('subject_id'))
        ->get();

    return Inertia::render('Activities/Create', compact('modules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:quiz,exam,essay',
            'module_id' => 'required|exists:modules,id',
            'scheduled_at' => 'required|date',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB
        ]);

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('activity_files');
        }

        Activity::create($data);

        return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
    }

    public function edit(Activity $activity)
    {
        $user = Auth::user();

    // Get this teacher's valid assignments
    $assignments = $user->teacherAssignments()->get();

    // Restrict to only assigned modules
    $modules = Module::with(['yearLevel', 'section', 'subject'])
        ->whereIn('year_level_id', $assignments->pluck('year_level_id'))
        ->whereIn('section_id', $assignments->pluck('section_id'))
        ->whereIn('subject_id', $assignments->pluck('subject_id'))
        ->get();

    // Check if the teacher is allowed to edit this activity
    $activityModule = $activity->module;
    $isAssigned = $assignments->contains(function ($a) use ($activityModule) {
        return $a->year_level_id === $activityModule->year_level_id
            && $a->section_id === $activityModule->section_id
            && $a->subject_id === $activityModule->subject_id;
    });

    if (! $isAssigned) {
        abort(403, 'You are not authorized to edit this activity.');
    }

    return Inertia::render('Activities/Edit', [
        'activity' => $activity,
        'modules' => $modules,
    ]);
    }

   public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'type' => 'required|in:quiz,exam,essay',
        'module_id' => 'required|exists:modules,id',
        'scheduled_at' => 'required|date',
        'description' => 'nullable|string',
        'file' => 'nullable|file|max:10240',
    ]);

    $module = Module::findOrFail($validated['module_id']);
    $user = Auth::user();

    $isAssigned = $user->teacherAssignments()
        ->where('year_level_id', $module->year_level_id)
        ->where('section_id', $module->section_id)
        ->where('subject_id', $module->subject_id)
        ->exists();

    if (! $isAssigned) {
        abort(403, 'You are not authorized to update this activity.');
    }

    // Handle file upload
    if ($request->hasFile('file')) {
        if ($activity->file_path && \Storage::disk('public')->exists($activity->file_path)) {
            \Storage::disk('public')->delete($activity->file_path);
        }

        $validated['file_path'] = $request->file('file')->store('activity_files', 'public');
    }

    $activity->update($validated);

    return redirect()->route('activities.index')->with('success', 'Activity updated.');
    }



    public function destroy(Activity $activity)
    {
        if ($activity->file_path && Storage::exists($activity->file_path)) {
            Storage::delete($activity->file_path);
        }

        $activity->delete();

        return redirect()->route('activities.index')->with('success', 'Activity deleted.');
    }
}
