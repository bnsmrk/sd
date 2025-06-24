<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('module.yearLevel', 'module.section', 'module.subject')->get();

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
        $modules = Module::with('yearLevel', 'section', 'subject')->get();
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
        $modules = Module::with('yearLevel', 'section', 'subject')->get();
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

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if it exists
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
