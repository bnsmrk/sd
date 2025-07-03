<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\TeacherAssignment;

class TeacherAssignmentController extends Controller
{
    public function index()
    {
        $assignments = TeacherAssignment::with([
            'teacher:id,name',
            'yearLevel:id,name',
            'subject:id,name'
        ])->get();

        return Inertia::render('TeacherAssignments/Index', [
            'assignments' => $assignments,
        ]);
    }
public function show(TeacherAssignment $teacherAssignment)
{
    return Inertia::render('TeacherAssignments/View', [
        'assignment' => [
            'id' => $teacherAssignment->id,
            'teacher' => $teacherAssignment->teacher()->select('id', 'name')->first(),
            'year_level' => $teacherAssignment->yearLevel()->select('id', 'name')->first(),
            'section' => $teacherAssignment->section()->select('id', 'name')->first(),
            'subject' => $teacherAssignment->subject()->select('id', 'name')->first(),
        ]
    ]);
}

    public function create()
    {
        return Inertia::render('TeacherAssignments/Create', [
        'teachers' => User::where('role', 'teacher')->select('id', 'name')->get(),
        'yearLevels' => YearLevel::select('id', 'name')->get(),
        'sections' => Section::select('id', 'name', 'year_level_id')->get(),
        'subjects' => Subject::select('id', 'name', 'year_level_id', 'section_id')->get(), // include section_id
    ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'year_level_id' => 'required|exists:year_levels,id',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    // Check for existing assignment
    $exists = TeacherAssignment::where('year_level_id', $request->year_level_id)
        ->where('section_id', $request->section_id)
        ->where('subject_id', $request->subject_id)
        ->exists();

    if ($exists) {
        return back()->withErrors([
            'subject_id' => 'A teacher is already assigned to this subject in the selected year level and section.',
        ])->withInput();
    }

    TeacherAssignment::create([
        'user_id' => $request->user_id,
        'year_level_id' => $request->year_level_id,
        'section_id' => $request->section_id,
        'subject_id' => $request->subject_id,
    ]);

    return redirect()->route('teacher-assignments.index')->with('success', 'Teacher assigned successfully.');
}


   public function edit(TeacherAssignment $teacherAssignment)
{
     return Inertia::render('TeacherAssignments/Edit', [
        'assignment' => $teacherAssignment->only(['id', 'user_id', 'year_level_id', 'section_id', 'subject_id']),
        'teachers' => User::where('role', 'teacher')->select('id', 'name')->get(),
        'yearLevels' => YearLevel::select('id', 'name')->get(),
        'sections' => Section::select('id', 'name', 'year_level_id')->get(),
        'subjects' => Subject::select('id', 'name', 'year_level_id', 'section_id')->get(),
    ]);
}


    public function update(Request $request, TeacherAssignment $teacherAssignment)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'year_level_id' => 'required|exists:year_levels,id',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    // Check if another assignment already exists
    $exists = TeacherAssignment::where('year_level_id', $request->year_level_id)
        ->where('section_id', $request->section_id)
        ->where('subject_id', $request->subject_id)
        ->where('id', '!=', $teacherAssignment->id)
        ->exists();

    if ($exists) {
        return back()->withErrors([
            'subject_id' => 'A teacher is already assigned to this subject in the selected year level and section.',
        ])->withInput();
    }

    $teacherAssignment->update([
        'user_id' => $request->user_id,
        'year_level_id' => $request->year_level_id,
        'section_id' => $request->section_id,
        'subject_id' => $request->subject_id,
    ]);

    return redirect()->route('teacher-assignments.index')->with('success', 'Assignment updated.');
}


    public function destroy(TeacherAssignment $teacherAssignment)
    {
        $teacherAssignment->delete();

        return back()->with('success', 'Assignment deleted.');
    }
}
