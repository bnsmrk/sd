<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TeacherAssignment;

class TeacherAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assignments = TeacherAssignment::with([
            'teacher:id,name',
            'yearLevel:id,name',
        ])
            ->when($search, function ($query, $search) {
                $query->whereHas('teacher', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('TeacherAssignments/Index', [
            'assignments' => $assignments,
            'filters' => ['search' => $search],
        ]);
    }

    public function show(TeacherAssignment $teacherAssignment)
    {
        return Inertia::render('TeacherAssignments/View', [
            'assignment' => [
                'id' => $teacherAssignment->id,
                'teacher' => $teacherAssignment->teacher()->select('id', 'name')->first(),
                'year_level' => $teacherAssignment->yearLevel()->select('id', 'name')->first(),
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('TeacherAssignments/Create', [
            'teachers' => User::where('role', 'teacher')->select('id', 'name')->get(),
            'yearLevels' => YearLevel::select('id', 'name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $exists = TeacherAssignment::where('user_id', $request->user_id)
            ->where('year_level_id', $request->year_level_id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'year_level_id' => 'This teacher is already assigned to the selected year level.',
            ]);
        }

        TeacherAssignment::create([
            'user_id' => $request->user_id,
            'year_level_id' => $request->year_level_id,
        ]);

        return redirect()->route('teacher-assignments.index')->with('success', 'Teacher assigned successfully.');
    }

    public function edit(TeacherAssignment $teacherAssignment)
    {
        return Inertia::render('TeacherAssignments/Edit', [
            'assignment' => $teacherAssignment->only(['id', 'user_id', 'year_level_id']),
            'teachers' => User::where('role', 'teacher')->select('id', 'name')->get(),
            'yearLevels' => YearLevel::select('id', 'name')->get(),
        ]);
    }

    public function update(Request $request, TeacherAssignment $teacherAssignment)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $exists = TeacherAssignment::where('user_id', $request->user_id)
            ->where('year_level_id', $request->year_level_id)
            ->where('id', '!=', $teacherAssignment->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'year_level_id' => 'This teacher is already assigned to the selected year level.',
            ]);
        }

        $teacherAssignment->update([
            'user_id' => $request->user_id,
            'year_level_id' => $request->year_level_id,
        ]);

        return redirect()->route('teacher-assignments.index')->with('warning', 'Assignment updated.');
    }

    public function destroy(TeacherAssignment $teacherAssignment)
    {
        $teacherAssignment->delete();

        return back()->with('danger', 'Assignment deleted.');
    }
}
