<?php
namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with('module.yearLevel', 'module.section', 'module.subject')->get();

        return Inertia::render('Activities/Index', [
            'activities' => $activities,
        ]);
    }

    public function create()
    {
       $modules = Module::with('yearLevel', 'section', 'subject')->get();

    return Inertia::render('Activities/Create', [
        'modules' => $modules,
    ]);
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'module_id' => 'required|exists:modules,id',
        'scheduled_at' => 'required|date',
    ]);

    Activity::create($validated);

    return redirect()->route('activities.index')->with('success', 'Activity created successfully.');
}


    public function edit(Activity $activity)
    {
       $modules = Module::with('yearLevel', 'section', 'subject')->get();

    $activity->load('module.yearLevel', 'module.section', 'module.subject');

    return Inertia::render('Activities/Edit', [
        'activity' => $activity,
        'modules' => $modules,
    ]);
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'module_id' => 'required|exists:modules,id',
        'scheduled_at' => 'required|date',
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
