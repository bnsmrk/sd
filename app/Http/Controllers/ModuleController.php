<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\TeacherSubAssignment;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $subAssignments = TeacherSubAssignment::with(['teacherAssignment'])
            ->whereHas('teacherAssignment', fn ($q) => $q->where('user_id', $user->id))
            ->get();

        $query = Module::with(['yearLevel', 'subject', 'section'])
            ->whereIn('year_level_id', $subAssignments->pluck('teacherAssignment.year_level_id'))
            ->whereIn('section_id', $subAssignments->pluck('section_id'))
            ->whereIn('subject_id', $subAssignments->pluck('subject_id'));

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $modules = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Modules/Index', [
            'modules' => $modules,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        $subAssignments = TeacherSubAssignment::with([
            'teacherAssignment.yearLevel',
            'section',
            'subject',
        ])->whereHas('teacherAssignment', fn ($q) => $q->where('user_id', $user->id))
          ->get();

        $grouped = $subAssignments->groupBy(fn ($sub) => $sub->teacherAssignment->year_level_id)->map(function ($items, $yearLevelId) {
            $yearLevel = $items->first()->teacherAssignment->yearLevel;

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

        $user = Auth::user();

        $isAssigned = TeacherSubAssignment::where('section_id', $validated['section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->whereHas('teacherAssignment', function ($q) use ($user, $validated) {
                $q->where('user_id', $user->id)
                    ->where('year_level_id', $validated['year_level_id']);
            })
            ->exists();

        if (! $isAssigned) {
            abort(403, 'You are not assigned to this year level, section, and subject.');
        }

        Module::create($validated);

        return redirect()->route('modules.index')->with('success', 'Module created successfully.');
    }

    public function edit(Module $module)
    {
        $user = Auth::user();

        $subAssignments = TeacherSubAssignment::with([
            'teacherAssignment.yearLevel',
            'section',
            'subject',
        ])->whereHas('teacherAssignment', fn ($q) => $q->where('user_id', $user->id))
          ->get();

        $grouped = $subAssignments->groupBy(fn ($sub) => $sub->teacherAssignment->year_level_id)->map(function ($items, $yearLevelId) {
            $yearLevel = $items->first()->teacherAssignment->yearLevel;

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

        $isAssigned = TeacherSubAssignment::where('section_id', $validated['section_id'])
            ->where('subject_id', $validated['subject_id'])
            ->whereHas('teacherAssignment', function ($q) use ($user, $validated) {
                $q->where('user_id', $user->id)
                    ->where('year_level_id', $validated['year_level_id']);
            })
            ->exists();

        if (! $isAssigned) {
            abort(403, 'You are not assigned to this year level, section, and subject.');
        }

        $module->update($validated);

        return redirect()->route('modules.index')->with('warning', 'Module updated successfully.');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('modules.index')->with('danger', 'Module deleted.');
    }
}
