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
        $enrollments = Student::with(['user:id,name', 'yearLevel:id,name', 'section:id,name', 'subject:id,name'])->get();

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
            'user_id'     => 'required|exists:users,id',
            'year_level'  => 'required|string|max:255',
            'section'     => 'required|string|max:255',
            'subject'     => 'required|string|max:255',
        ]);

        $yearLevel = YearLevel::where('name', $validated['year_level'])->firstOrFail();
        $section   = Section::where('name', $validated['section'])->where('year_level_id', $yearLevel->id)->firstOrFail();
        $subject   = Subject::where('name', $validated['subject'])->where('year_level_id', $yearLevel->id)->where('section_id', $section->id)->firstOrFail();

        Student::create([
            'user_id'        => $validated['user_id'],
            'year_level_id'  => $yearLevel->id,
            'section_id'     => $section->id,
            'subject_id'     => $subject->id,
        ]);

        return redirect()->route('enroll.index')->with('success', 'Student enrolled successfully!');
    }

   public function edit(Student $enroll)
{
    return Inertia::render('EnrollStudent/Edit', [
        'enrollment' => $enroll->load('user', 'yearLevel', 'section', 'subject'),
        'students' => User::where('role', 'student')->get(['id', 'name']),
        'yearLevels' => YearLevel::all(['id', 'name']),
        'sections' => Section::all(['id', 'name', 'year_level_id']),
        'subjects' => Subject::all(['id', 'name', 'year_level_id', 'section_id']),
    ]);
}


 public function update(Request $request, Student $enroll)
{
    // \Log::info('Update called for student', [
    //     'student_id' => $enroll->id,
    //     'payload' => $request->all()
    // ]);

    $validated = $request->validate([
        'year_level_id' => 'required|exists:year_levels,id',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    // Update student if fields exist
    $enroll->year_level_id = $validated['year_level_id'];
    $enroll->section_id = $validated['section_id'];
    $enroll->subject_id = $validated['subject_id'];
    $enroll->save();

    return redirect()->route('enroll.index')->with('success', 'Student enrollment updated.');
}


    public function destroy($id)
    {
        Student::findOrFail($id)->delete();
        return redirect()->route('enroll.index')->with('success', 'Enrollment deleted!');
    }
}
