<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Section;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use App\Models\HeadAssignment;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class HeadAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assignments = HeadAssignment::with(['user:id,name', 'yearLevel:id,name'])
            ->when($search, fn($q) => $q->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")))
            ->get()
            ->groupBy('year_level_id')
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        return Inertia::render('Head/HeadAssignments', [
            'assignments' => $assignments->values(),
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return Inertia::render('Head/HeadAssignmentCreate', [
            'heads' => User::where('role', 'head')->select('id', 'name')->get(),
            'yearLevels' => YearLevel::select('id', 'name')->get(),
            'sections' => Section::select('id', 'name', 'year_level_id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $alreadyAssigned = HeadAssignment::where('user_id', $validated['user_id'])
            ->where('year_level_id', '!=', $validated['year_level_id'])
            ->exists();

        if ($alreadyAssigned) {
            return back()->withErrors(['user_id' => 'This head is already assigned to a different year level.'])->withInput();
        }

        HeadAssignment::where('year_level_id', $validated['year_level_id'])->delete();

        $sections = Section::where('year_level_id', $validated['year_level_id'])->get();
        foreach ($sections as $section) {
            HeadAssignment::create([
                'user_id' => $validated['user_id'],
                'year_level_id' => $validated['year_level_id'],
                'section_id' => $section->id,
            ]);
        }

        return redirect()->route('head-assignments.index')->with('success', 'Head assigned successfully.');
    }

    public function assignToAll(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $alreadyAssigned = HeadAssignment::where('user_id', $validated['user_id'])
            ->where('year_level_id', '!=', $validated['year_level_id'])
            ->exists();

        if ($alreadyAssigned) {
            return back()->withErrors([
                'user_id' => 'This head is already assigned to another year level.',
            ])->withInput();
        }

        HeadAssignment::where('year_level_id', $validated['year_level_id'])->delete();

        $sections = Section::where('year_level_id', $validated['year_level_id'])->get();
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $alreadyAssigned = HeadAssignment::where('user_id', $validated['user_id'])
            ->where('year_level_id', '!=', $validated['year_level_id'])
            ->exists();

        if ($alreadyAssigned) {
            throw ValidationException::withMessages([
                'user_id' => 'This head is already assigned to another year level.',
            ]);
        }

        HeadAssignment::where('year_level_id', $validated['year_level_id'])->delete();

        $sections = Section::where('year_level_id', $validated['year_level_id'])->get();
        foreach ($sections as $section) {
            HeadAssignment::create([
                'user_id' => $validated['user_id'],
                'year_level_id' => $validated['year_level_id'],
                'section_id' => $section->id,
            ]);
        }

        return redirect()->route('head-assignments.index')->with('success', 'Head assigned successfully.');
    }

    public function destroy(HeadAssignment $headAssignment)
    {
        $headAssignment->delete();

        return back()->with('danger', 'Assignment removed.');
    }
    public function destroyByYearLevel($yearLevel)
    {
        HeadAssignment::where('year_level_id', $yearLevel)->delete();

        return back()->with('danger', 'Head assignment removed.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit($yearLevelId)
    {
        return Inertia::render('Head/HeadAssignmentEdit', [
            'assignment' => HeadAssignment::where('year_level_id', $yearLevelId)->firstOrFail(),
            'heads' => User::where('role', 'head')->select('id', 'name')->get(),
            'yearLevels' => YearLevel::select('id', 'name')->get(),
        ]);
    }


    public function update(Request $request, $yearLevelId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $isAssignedElsewhere = HeadAssignment::where('user_id', $validated['user_id'])
            ->where('year_level_id', '!=', $yearLevelId)
            ->exists();

        if ($isAssignedElsewhere) {
            throw ValidationException::withMessages([
                'user_id' => 'This head is already assigned to another year level.',
            ]);
        }

        $existingHead = HeadAssignment::where('year_level_id', $validated['year_level_id'])
            ->where('user_id', '!=', $validated['user_id'])
            ->first();

        if ($existingHead) {
            throw ValidationException::withMessages([
                'year_level_id' => 'This year level already has a head assigned.',
            ]);
        }

        HeadAssignment::where('year_level_id', $yearLevelId)->delete();

        $sections = Section::where('year_level_id', $validated['year_level_id'])->get();
        foreach ($sections as $section) {
            HeadAssignment::create([
                'user_id' => $validated['user_id'],
                'year_level_id' => $validated['year_level_id'],
                'section_id' => $section->id,
            ]);
        }

        return redirect()->route('head-assignments.index')->with('success', 'Head assignment updated.');
    }





    /**
     * Remove the specified resource from storage.
     */
}
