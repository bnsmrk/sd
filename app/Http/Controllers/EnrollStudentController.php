<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollStudentController extends Controller
{
    public function index()
    {
         $enrollments = Student::with([
        'user:id,name',
        'yearLevel:id,name',
        'subject:id,name'
    ])->get();

    return Inertia::render('EnrollStudent/Index', [
        'enrollments' => $enrollments,
    ]);
    }

    public function create()
    {
        return Inertia::render('EnrollStudent/Create', [
            'students'    => User::where('role', 'student')->get(['id', 'name']),
            'yearLevels'  => YearLevel::all(['id', 'name']),
            'sections'    => Section::all(['id', 'name', 'year_level_id']),
            'subjects'    => Subject::all(['id', 'name', 'year_level_id', 'section_id']),
        ]);
    }

     public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level' => 'required|string|max:255',
        ]);

        $yearLevel = YearLevel::where('name', $validated['year_level'])->firstOrFail();

        // Get all subjects linked to the year level
        $subjects = Subject::where('year_level_id', $yearLevel->id)->get();

        foreach ($subjects as $subject) {
            Student::create([
                'user_id' => $validated['user_id'],
                'year_level_id' => $yearLevel->id,
                'section_id' => $subject->section_id,
                'subject_id' => $subject->id,
            ]);
        }

        return redirect()->route('enroll.index')->with('success', 'Student enrolled in all subjects of the selected year level.');
    }

    public function edit(Student $enroll)
    {
        return Inertia::render('EnrollStudent/Edit', [
            'enrollment' => [
                'id' => $enroll->id,
                'user' => $enroll->user()->select('id', 'name')->first(),
                'year_level_id' => $enroll->year_level_id,
            ],
            'yearLevels' => YearLevel::all(['id', 'name']),
            'subjects' => Subject::all(['id', 'name', 'year_level_id', 'section_id']),
        ]);
    }

    public function update(Request $request, Student $enroll)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        // Delete all existing enrollments for this student
        Student::where('user_id', $validated['user_id'])->delete();

        // Get all subjects for new year level
        $subjects = Subject::where('year_level_id', $validated['year_level_id'])->get();

        foreach ($subjects as $subject) {
            Student::create([
                'user_id' => $validated['user_id'],
                'year_level_id' => $validated['year_level_id'],
                'section_id' => $subject->section_id,
                'subject_id' => $subject->id,
            ]);
        }

        return redirect()->route('enroll.index')->with('success', 'Student enrollment updated for the selected year level.');
    }

    public function destroy($id)
    {
        Student::findOrFail($id)->delete();

        return redirect()->route('enroll.index')->with('success', 'Enrollment deleted.');
    }
}
