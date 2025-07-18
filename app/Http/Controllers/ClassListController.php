<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\Auth;

class ClassListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     {
         $userId = Auth::id();

         $assignments = TeacherAssignment::with('subject')->where('user_id', $userId)->get();

         $sectionIds = $assignments->pluck('section_id')->unique()->filter()->values();

         $subjectsBySection = $assignments->groupBy('section_id')->map(function ($assignments) {
             return $assignments->pluck('subject.name')->unique()->values();
         });

         $studentsQuery = Student::with(['user', 'section', 'yearLevel'])
             ->whereIn('section_id', $sectionIds);

         if ($request->filled('search')) {
             $studentsQuery->whereHas('user', function ($query) use ($request) {
                 $query->where('name', 'like', '%' . $request->search . '%');
             });
         }

         $students = $studentsQuery->paginate(10)->through(function ($s) use ($subjectsBySection) {
             $subjects = $subjectsBySection[$s->section_id] ?? collect();
             return [
                 'id' => $s->id,
                 'name' => $s->user?->name ?? 'Unnamed',
                 'year_level' => $s->yearLevel?->name ?? '-',
                 'section' => $s->section?->name ?? '-',
                 'subjects' => $subjects->all(),
             ];
         });

         return Inertia::render('TeacherAssignments/ClassList', [
             'students' => $students,
             'filters' => $request->only('search'),
         ]);
     }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
