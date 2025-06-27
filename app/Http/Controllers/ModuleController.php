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
        $user = Auth::user();

    // Get all teacher assignments
    $assignments = $user->teacherAssignments()->get();

    // Filter modules the teacher is assigned to
    $modules = Module::with(['yearLevel', 'subject', 'section'])
        ->whereIn('year_level_id', $assignments->pluck('year_level_id'))
        ->whereIn('section_id', $assignments->pluck('section_id'))
        ->whereIn('subject_id', $assignments->pluck('subject_id'))
        ->get();

    return Inertia::render('Modules/Index', [
        'modules' => $modules,
    ]);
    }

    public function create()
    {
        $user = Auth::user();

        // ✅ Define assignments first
        $assignments = $user->teacherAssignments()
            ->with(['yearLevel', 'section', 'subject'])
            ->get();

        // ✅ Then group by year level
        $grouped = $assignments->groupBy(fn($a) => $a->yearLevel->id)->map(function ($items, $yearLevelId) {
            $yearLevel = $items->first()->yearLevel;

            return [
                'id' => $yearLevel->id,
                'name' => $yearLevel->name,
                'sections' => $items->pluck('section')->unique('id')->values()->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                ]),
                'subjects' => $items->pluck('subject')->unique('id')->values()->map(function ($subject) use ($items) {
                    $sectionIds = $items->where('subject_id', $subject->id)->pluck('section_id')->unique()->values();
                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'section_ids' => $sectionIds,
                    ];
                }),
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
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        // Check if the user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized action.');
        }

            // Make sure the teacher is assigned to this combination
            $isAssigned = Auth::user()->teacherAssignments()
                ->where('year_level_id', $validated['year_level_id'])
                ->where('section_id', $validated['section_id'])
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if (!$isAssigned) {
                abort(403, 'You are not assigned to this year level, section, and subject.');
            }

            Module::create($validated);


        return redirect()->route('modules.index')->with('success', 'Module created successfully.');
    }

 public function edit(Module $module)
{
    $user = Auth::user();

    $assignments = $user->teacherAssignments()
        ->with(['yearLevel', 'section', 'subject'])
        ->get();

    $grouped = $assignments->groupBy(fn($a) => $a->yearLevel->id)->map(function ($items, $yearLevelId) {
        $yearLevel = $items->first()->yearLevel;

        return [
            'id' => $yearLevel->id,
            'name' => $yearLevel->name,
            'sections' => $items->pluck('section')->unique('id')->values()->map(fn($s) => [
                'id' => $s->id,
                'name' => $s->name,
            ]),
            'subjects' => $items->pluck('subject')->unique('id')->values()->map(function ($subject) use ($items) {
                $sectionIds = $items->filter(fn($i) => $i->subject_id === $subject->id)
                    ->pluck('section.id')->unique()->values();

                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'section_ids' => $sectionIds,
                ];
            }),
        ];
    })->values();

    return Inertia::render('Modules/Edit', [
        'module' => $module,
        'assignments' => $grouped,
    ]);
}


public function update(Request $request, Module $module)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'year_level_id' => 'required|exists:year_levels,id',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    $user = Auth::user();

    $isAssigned = $user->teacherAssignments()
        ->where('year_level_id', $validated['year_level_id'])
        ->where('section_id', $validated['section_id'])
        ->where('subject_id', $validated['subject_id'])
        ->exists();

    if (! $isAssigned) {
        abort(403, 'You are not assigned to this year level, section, and subject.');
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
