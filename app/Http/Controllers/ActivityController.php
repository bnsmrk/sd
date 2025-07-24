<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Module;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\TeacherSubAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewActivityNotification;
use Illuminate\Support\Facades\Notification;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $teacherId = Auth::id();

        $subAssignments = TeacherSubAssignment::whereHas('teacherAssignment', function ($q) use ($teacherId) {
            $q->where('user_id', $teacherId);
        })->get();

        $allowedModuleIds = Module::whereIn('section_id', $subAssignments->pluck('section_id'))
            ->whereIn('subject_id', $subAssignments->pluck('subject_id'))
            ->pluck('id');

        $search = $request->input('search');

        $activities = Activity::with('module.yearLevel', 'module.section', 'module.subject')
            ->whereIn('module_id', $allowedModuleIds)
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $formatted = $activities->through(function ($a) {
            return [
                'id' => $a->id,
                'title' => $a->title,
                'type' => $a->type,
                'scheduled_at' => optional($a->scheduled_at)->format('Y-m-d H:i'),
                'due_date' => optional($a->due_date)->format('Y-m-d H:i'),
                'year_level' => $a->module->yearLevel->name ?? '',
                'section' => $a->module->section->name ?? '',
                'subject' => $a->module->subject->name ?? '',
            ];
        });

        return Inertia::render('Activities/Index', [
            'activities' => $formatted,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        $userId = Auth::id();

        $subAssignments = TeacherSubAssignment::with(['teacherAssignment.yearLevel', 'section', 'subject'])
            ->whereHas('teacherAssignment', fn($q) => $q->where('user_id', $userId))
            ->get();

        $modules = Module::with(['yearLevel', 'section', 'subject'])
            ->whereIn('section_id', $subAssignments->pluck('section_id'))
            ->whereIn('subject_id', $subAssignments->pluck('subject_id'))
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
            'due_date' => 'nullable|date|after_or_equal:scheduled_at',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $module = Module::findOrFail($data['module_id']);

        $isAssigned = TeacherSubAssignment::whereHas('teacherAssignment', fn($q) => $q->where('user_id', Auth::id()))
            ->where('section_id', $module->section_id)
            ->where('subject_id', $module->subject_id)
            ->exists();

        if (! $isAssigned) {
            abort(403, 'You are not assigned to this module.');
        }

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('activity_files', 'public');
        }

        $activity = Activity::create($data);

        $studentUserIds = \App\Models\Student::where('year_level_id', $module->year_level_id)
            ->where('section_id', $module->section_id)
            ->where('subject_id', $module->subject_id)
            ->pluck('user_id');

        $students = User::whereIn('id', $studentUserIds)->get();

        if ($students->isNotEmpty()) {
            Notification::send($students, new NewActivityNotification($activity));
        }

        return redirect()->route('activities.index')->with('success', 'Activity created and students notified.');
    }

    public function edit(Activity $activity)
    {
        $userId = Auth::id();

        $subAssignments = TeacherSubAssignment::with(['teacherAssignment'])
            ->whereHas('teacherAssignment', fn($q) => $q->where('user_id', $userId))
            ->get();

        $modules = Module::with(['yearLevel', 'section', 'subject'])
            ->whereIn('section_id', $subAssignments->pluck('section_id'))
            ->whereIn('subject_id', $subAssignments->pluck('subject_id'))
            ->get();

        $activityModule = $activity->module;

        $isAssigned = $subAssignments->contains(function ($a) use ($activityModule) {
            return $a->section_id === $activityModule->section_id &&
                $a->subject_id === $activityModule->subject_id;
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
            'due_date' => 'nullable|date|after_or_equal:scheduled_at',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $module = Module::findOrFail($validated['module_id']);

        $isAssigned = TeacherSubAssignment::whereHas('teacherAssignment', fn($q) => $q->where('user_id', Auth::id()))
            ->where('section_id', $module->section_id)
            ->where('subject_id', $module->subject_id)
            ->exists();

        if (! $isAssigned) {
            abort(403, 'You are not authorized to update this activity.');
        }

        if ($request->hasFile('file')) {
            if ($activity->file_path && Storage::disk('public')->exists($activity->file_path)) {
                Storage::disk('public')->delete($activity->file_path);
            }

            $validated['file_path'] = $request->file('file')->store('activity_files', 'public');
        }

        $activity->update($validated);

        return redirect()->route('activities.index')->with('warning', 'You have successfully updated the Activity.');
    }

    public function destroy(Activity $activity)
    {
        $module = $activity->module;

        $isAssigned = TeacherSubAssignment::whereHas('teacherAssignment', fn($q) => $q->where('user_id', Auth::id()))
            ->where('section_id', $module->section_id)
            ->where('subject_id', $module->subject_id)
            ->exists();

        if (! $isAssigned) {
            abort(403, 'You are not authorized to delete this activity.');
        }

        if ($activity->file_path && Storage::disk('public')->exists($activity->file_path)) {
            Storage::disk('public')->delete($activity->file_path);
        }

        $activity->delete();

        return redirect()->route('activities.index')->with('danger', 'You have successfully deleted an Activity.');
    }
}
