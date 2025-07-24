<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\HeadAssignment;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\Auth;
use App\Models\YearLevel;
use App\Models\Subject;
use App\Models\Section;

class HeadTeacherSubAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     {
         $filters = $request->only('search');

         $userId = Auth::id();

         $assignedYearLevelIds = HeadAssignment::where('user_id', $userId)
             ->pluck('year_level_id');

         $query = User::where('role', 'teacher')
             ->whereHas('teacherAssignments', function ($q) use ($assignedYearLevelIds) {
                 $q->whereIn('year_level_id', $assignedYearLevelIds);
             });

         if (!empty($filters['search'])) {
             $query->where(function ($q) use ($filters) {
                 $q->where('name', 'like', '%' . $filters['search'] . '%')
                   ->orWhere('email', 'like', '%' . $filters['search'] . '%');
             });
         }

         $teachers = $query
         ->with(['teacherAssignments' => function ($q) use ($assignedYearLevelIds) {
            $q->whereIn('year_level_id', $assignedYearLevelIds)
              ->with('subAssignments', 'yearLevel');
        }])

             ->paginate(10)
             ->withQueryString()
             ->through(function ($teacher) {
                $assignments = $teacher->teacherAssignments;

                $subAssignments = $assignments->flatMap(function ($assignment) {
                    return $assignment->subAssignments;
                });

                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'subject_count' => $subAssignments->pluck('subject_id')->filter()->unique()->count(),
                    'section_count' => $subAssignments->pluck('section_id')->filter()->unique()->count(),
                    'year_level_count' => $assignments->pluck('year_level_id')->unique()->count(),
                ];
            });


         return Inertia::render('Head/HeadTeacherAssignment', [
             'teachers' => $teachers,
             'filters' => $filters,
         ]);
     }


     public function create()
    {
        $head = Auth::user();

        $yearLevelIds = HeadAssignment::where('user_id', $head->id)->pluck('year_level_id');

        $teachers = User::where('role', 'teacher')
            ->whereHas('teacherAssignments', function ($q) use ($yearLevelIds) {
                $q->whereIn('year_level_id', $yearLevelIds);
            })
            ->get();

        $subjects = Subject::whereIn('year_level_id', $yearLevelIds)->get();
        $sections = Section::whereIn('year_level_id', $yearLevelIds)->get();

        return Inertia::render('Head/HeadTeacherAssignmentCreate', [
            'teachers' => $teachers,
            'subjects' => $subjects,
            'sections' => $sections,
        ]);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $teacherAssignment = TeacherAssignment::where('user_id', $validated['teacher_id'])
            ->whereHas('yearLevel', function ($q) use ($validated) {
                $q->whereHas('subjects', fn ($q2) => $q2->where('id', $validated['subject_id']));
            })
            ->firstOrFail();


        $alreadyAssigned = $teacherAssignment->subAssignments()
            ->where('subject_id', $validated['subject_id'])
            ->where('section_id', $validated['section_id'])
            ->exists();

            if ($alreadyAssigned) {
                return redirect()->back()->withErrors([
                    'subject_id' => 'This teacher is already assigned to this subject and section.',
                ]);
            }


        $teacherAssignment->subAssignments()->create([
            'subject_id' => $validated['subject_id'],
            'section_id' => $validated['section_id'],
        ]);

        return redirect()->route('head-teacher-assignments.index')->with('success', 'Teacher assigned.');
    }



    public function show($id)
    {
        $teacher = User::with([
            'teacherAssignments.subAssignments' => function ($q) {
                $q->with('subject.yearLevel', 'section');
            }
        ])
        ->where('role', 'teacher')
        ->findOrFail($id);

        $subAssignments = $teacher->teacherAssignments->flatMap(function ($assignment) {
            return $assignment->subAssignments;
        });

        $assignments = $subAssignments->map(function ($sub) {
            return [
                'subject' => $sub->subject->name ?? 'N/A',
                'year_level' => $sub->subject->yearLevel->name ?? 'N/A',
                'section' => $sub->section->name ?? 'N/A',
            ];
        });

        return Inertia::render('Head/TeacherDetail', [
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
            ],
            'assignments' => $assignments,
        ]);
    }




     public function edit($id)
     {
         $teacher = User::with([
             'teacherAssignments.subAssignments' => fn ($q) => $q->with('subject', 'section'),
         ])->where('role', 'teacher')->findOrFail($id);

         $yearLevelIds = HeadAssignment::where('user_id', Auth::id())->pluck('year_level_id');

         $subjects = Subject::whereIn('year_level_id', $yearLevelIds)->get();
         $sections = Section::whereIn('year_level_id', $yearLevelIds)->get();

         $subAssignments = $teacher->teacherAssignments->flatMap(fn ($t) => $t->subAssignments)->map(fn ($sub) => [
             'id' => $sub->id,
             'subject_id' => $sub->subject_id,
             'section_id' => $sub->section_id,
             'subject_name' => $sub->subject->name,
             'section_name' => $sub->section->name,
         ]);

         return Inertia::render('Head/HeadTeacherAssignmentEdit', [
             'teacher' => [
                 'id' => $teacher->id,
                 'name' => $teacher->name,
             ],
             'subAssignments' => $subAssignments,
             'subjects' => $subjects,
             'sections' => $sections,
         ]);
     }


     public function update(Request $request, $id)
{
    $validated = $request->validate([
        'assignments' => 'required|array',
        'assignments.*.subject_id' => 'required|exists:subjects,id',
        'assignments.*.section_id' => 'required|exists:sections,id',
    ]);

    $teacherAssignments = TeacherAssignment::where('user_id', $id)
        ->with('subAssignments')
        ->get();

    $existing = collect();
    foreach ($teacherAssignments as $ta) {
        foreach ($ta->subAssignments as $sub) {
            $existing->push([
                'id' => $sub->id,
                'subject_id' => $sub->subject_id,
                'section_id' => $sub->section_id,
                'teacher_assignment_id' => $ta->id,
            ]);
        }
    }

    $seen = [];
    foreach ($validated['assignments'] as $a) {
        $key = $a['subject_id'] . '-' . $a['section_id'];
        if (in_array($key, $seen)) {
            return back()->withErrors([
                'assignments' => 'Duplicate subject and section detected in form.',
            ]);
        }
        $seen[] = $key;
    }

    $incoming = collect($validated['assignments']);
    $incomingIds = $incoming->pluck('id')->filter()->all();

    foreach ($existing as $existingSub) {
        if (!in_array($existingSub['id'], $incomingIds)) {
            \App\Models\TeacherSubAssignment::where('id', $existingSub['id'])->delete();
        }
    }

    foreach ($incoming as $a) {
        $subjectId = $a['subject_id'];
        $sectionId = $a['section_id'];
        $rowId = $a['id'] ?? null;

        $alreadyExists = \App\Models\TeacherSubAssignment::whereHas('teacherAssignment', function ($q) use ($id) {
                $q->where('user_id', $id);
            })
            ->where('subject_id', $subjectId)
            ->where('section_id', $sectionId)
            ->when($rowId, fn ($q) => $q->where('id', '!=', $rowId))
            ->exists();

        if ($alreadyExists) {
            return back()->withErrors([
                'assignments' => 'Subject and section already assigned to this teacher.',
            ]);
        }

        if ($rowId) {
            \App\Models\TeacherSubAssignment::where('id', $rowId)->update([
                'subject_id' => $subjectId,
                'section_id' => $sectionId,
            ]);
        } else {
            $subject = \App\Models\Subject::find($subjectId);
            $ta = $teacherAssignments->firstWhere('year_level_id', $subject->year_level_id);
            if ($ta) {
                $ta->subAssignments()->create([
                    'subject_id' => $subjectId,
                    'section_id' => $sectionId,
                ]);
            }
        }
    }

    return redirect()->route('head-teacher-assignments.index')->with('success', 'Assignments updated.');
}





    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
