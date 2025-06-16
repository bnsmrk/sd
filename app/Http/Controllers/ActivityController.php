<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['yearLevel', 'section', 'subject'])->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'title' => $a->title,
                'type' => $a->type,
                'scheduled_at' => $a->scheduled_at,
                'year_level' => $a->yearLevel->name ?? '',
                'section' => $a->section->name ?? '',
                'subject' => $a->subject->name ?? '',
            ];
        });

        return Inertia::render('Activities/Index', [
            'activities' => $activities,
        ]);
    }

    public function create()
    {
        return Inertia::render('Activities/Create', [
            'yearLevels' => YearLevel::all(['id', 'name']),
            'sections' => Section::all(['id', 'name', 'year_level_id']),
            'subjects' => Subject::all(['id', 'name', 'year_level_id', 'section_id']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,exam',
           'scheduled_at' => 'required|date',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

       Activity::create($request->only([
    'title', 'type', 'scheduled_at', 'year_level_id', 'section_id', 'subject_id'
]));
        return redirect()->route('activities.index')->with('success', 'Activity created.');
    }

    public function edit(Activity $activity)
    {
        return Inertia::render('Activities/Edit', [
            'activity' => $activity,
            'yearLevels' => YearLevel::all(['id', 'name']),
            'sections' => Section::all(['id', 'name', 'year_level_id']),
            'subjects' => Subject::all(['id', 'name', 'year_level_id', 'section_id']),
        ]);
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:quiz,exam',
           'scheduled_at' => 'required|date',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $activity->update($validated);

        return redirect()->route('activities.index')->with('success', 'Activity updated.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('activities.index')->with('success', 'Activity deleted.');
    }
}
