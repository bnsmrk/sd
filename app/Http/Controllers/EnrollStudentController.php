<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Mail\TestMail;
use Illuminate\Support\Facades\Mail;
class EnrollStudentController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');

    $subQuery = \App\Models\Student::selectRaw('MIN(id) as id')
        ->groupBy('user_id');

    $enrollments = \App\Models\Student::with(['user:id,name', 'yearLevel:id,name', 'section:id,name'])
        ->whereIn('id', $subQuery)
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        })
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('EnrollStudent/Index', [
        'enrollments' => $enrollments,
        'filters' => ['search' => $search],
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

    public function show(Student $enroll)
    {
        $enroll->load(['user:id,name', 'yearLevel:id,name', 'section:id,name', 'subject:id,name']);

        return Inertia::render('EnrollStudent/Index', [
            'enrollment' => $enroll,
        ]);
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'required|exists:users,id',
        'year_level_id' => 'required|exists:year_levels,id',
        'section_id' => 'required|exists:sections,id',
    ]);

    $alreadyEnrolled = Student::where('user_id', $validated['user_id'])
        ->where('year_level_id', $validated['year_level_id'])
        ->exists();

    if ($alreadyEnrolled) {
        return back()->withErrors([
            'user_id' => 'Student is already enrolled in the selected year level.',
        ])->withInput();
    }

    $subjects = Subject::where('year_level_id', $validated['year_level_id'])->get();

    if ($subjects->isEmpty()) {
        return back()->withErrors(['year_level_id' => 'No subjects found for selected year level.']);
    }

    foreach ($subjects as $subject) {
        Student::create([
            'user_id' => $validated['user_id'],
            'year_level_id' => $validated['year_level_id'],
            'section_id' => $validated['section_id'],
            'subject_id' => $subject->id,
        ]);
    }

    $student = User::find($validated['user_id']);
    $yearLevel = YearLevel::find($validated['year_level_id']);
    $section = Section::find($validated['section_id']);

    Mail::to($student->email)->send(new TestMail($student, $yearLevel, $section, $subjects));

    return redirect()->route('enroll.index')->with('success', 'Student enrolled successfully.');
}


    public function edit(Student $enroll)
    {
        return Inertia::render('EnrollStudent/Edit', [
            'enrollment' => [
                'id' => $enroll->id,
                'user' => $enroll->user()->select('id', 'name')->first(),
                'year_level_id' => $enroll->year_level_id,
                'section_id' => $enroll->section_id,
            ],
            'yearLevels' => YearLevel::all(['id', 'name']),
            'sections' => Section::all(['id', 'name', 'year_level_id']),
            'subjects' => Subject::all(['id', 'name', 'year_level_id', 'section_id']),
        ]);
    }

    public function update(Request $request, Student $enroll)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        Student::where('user_id', $validated['user_id'])->delete();

        $subjects = Subject::where('year_level_id', $validated['year_level_id'])->get();

        foreach ($subjects as $subject) {
            Student::create([
                'user_id' => $validated['user_id'],
                'year_level_id' => $validated['year_level_id'],
                'section_id' => $validated['section_id'],
                'subject_id' => $subject->id,
            ]);
        }

        return redirect()->route('enroll.index')->with('success', 'Enrollment updated.');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        Student::where('user_id', $student->user_id)->delete();

        return redirect()->route('enroll.index')->with('success', 'Enrollment deleted.');
    }
}
