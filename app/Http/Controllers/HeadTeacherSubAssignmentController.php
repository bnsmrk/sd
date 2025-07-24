<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HeadTeacherSubAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
     public function create($teacherAssignmentId)
     {
         $teacherAssignment = TeacherAssignment::with('yearLevel')->findOrFail($teacherAssignmentId);

         // Make sure the head is allowed
         $headYearLevelIds = HeadAssignment::where('user_id', Auth::id())->pluck('year_level_id');
         if (! $headYearLevelIds->contains($teacherAssignment->year_level_id)) {
             abort(403);
         }

         return Inertia::render('Head/TeacherSubAssignments/Create', [
             'teacherAssignment' => $teacherAssignment,
             'subjects' => Subject::where('year_level_id', $teacherAssignment->year_level_id)->get(),
             'sections' => Section::where('year_level_id', $teacherAssignment->year_level_id)->get(),
         ]);
     }

    /**
     * Store a newly created resource in storage.
     */
     public function store(Request $request, $teacherAssignmentId)
     {
         $request->validate([
             'subject_id' => 'required|exists:subjects,id',
             'section_id' => 'required|exists:sections,id',
         ]);

         $teacherAssignment = TeacherAssignment::findOrFail($teacherAssignmentId);

         // optional: check if already assigned
         $alreadyAssigned = $teacherAssignment->subAssignments()
             ->where('subject_id', $request->subject_id)
             ->where('section_id', $request->section_id)
             ->exists();

         if ($alreadyAssigned) {
             return back()->withErrors(['subject_id' => 'Already assigned to this subject and section.']);
         }

         $teacherAssignment->subAssignments()->create([
             'subject_id' => $request->subject_id,
             'section_id' => $request->section_id,
         ]);

         return redirect()->back()->with('success', 'Teacher assigned to section and subject.');
     }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
