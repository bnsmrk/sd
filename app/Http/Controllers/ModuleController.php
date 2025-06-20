<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\YearLevel;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with(['yearLevel', 'subject'])->get();

        return Inertia::render('Modules/Index', [
            'modules' => $modules,
        ]);
    }

    public function create()
{
    $user = Auth::user();

    $assignments = $user->teacherAssignments()
        ->with(['yearLevel', 'section', 'subject'])
        ->get();

    // Group assignments by year level
    $grouped = $assignments->groupBy(fn($a) => $a->yearLevel->id)->map(function ($items, $yearLevelId) {
        $yearLevel = $items->first()->yearLevel;

        return [
            'id' => $yearLevel->id,
            'name' => $yearLevel->name,
            'sections' => $items->pluck('section')->unique('id')->values()->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
            ]),
            'subjects' => $items->pluck('subject')->unique('id')->values()->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
            ]),
        ];
    })->values();

    return Inertia::render('Modules/Create', [
        'assignments' => $grouped,
    ]);
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $user = Auth::user();
        $isAssigned = $user->teacherAssignments()
            ->where('year_level_id', $validated['year_level_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if (!$isAssigned) {
            abort(403, 'You are not assigned to this year level and subject.');
        }

        Module::create($validated);

        return redirect()->route('modules.index')->with('success', 'Module created successfully.');
    }

    public function edit(Module $module)
    {
        $user = Auth::user();

        $assignments = $user->teacherAssignments()->with(['yearLevel', 'subject'])->get();

        return Inertia::render('Modules/Edit', [
            'module' => $module,
            'yearLevels' => $assignments->pluck('yearLevel')->unique('id')->values(),
            'subjects' => $assignments->pluck('subject')->unique('id')->values(),
        ]);
    }

    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $user = Auth::user();
        $isAssigned = $user->teacherAssignments()
            ->where('year_level_id', $validated['year_level_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if (!$isAssigned) {
            abort(403, 'You are not assigned to this year level and subject.');
        }

        $module->update($validated);

        return redirect()->route('modules.index')->with('success', 'Module updated successfully.');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('modules.index')->with('success', 'Module deleted.');
    }
}
