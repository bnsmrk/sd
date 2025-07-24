<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\YearLevel;
use App\Models\HeadAssignment;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeadDashboard extends Controller
{
    public function index(Request $request)
{
    $filters = $request->only('search');

    $userId = Auth::id();

    $assignedYearLevelIds = HeadAssignment::where('user_id', $userId)
        ->pluck('year_level_id')
        ->filter()
        ->unique();

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
        ->with('teacherAssignments.yearLevel')
        ->paginate(10)
        ->withQueryString()
        ->through(function ($teacher) {
            $assignments = $teacher->teacherAssignments;

            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'email' => $teacher->email,
                'subject_count' => 0,
                'section_count' => 0,
                'year_level_count' => $assignments->pluck('year_level_id')->unique()->count(),
            ];
        });

    $yearLevels = YearLevel::withCount('students')
        ->whereIn('id', $assignedYearLevelIds)
        ->get();

    $sectionCount = Section::whereIn('year_level_id', $assignedYearLevelIds)->count();
    $teacherCount = $teachers->total();

    return Inertia::render('Head/HeadDashboard', [
        'yearLevels' => $yearLevels,
        'sectionCount' => $sectionCount,
        'teacherCount' => $teacherCount,
        'teachers' => $teachers,
        'filters' => $filters,
        'chart' => [
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'barData' => [40, 55, 32, 50, 70, 30, 90],
            'lineData' => [30, 35, 25, 45, 60, 20, 80],
        ],
    ]);
}

    public function show($id)
    {
        $teacher = User::with(['teacherAssignments.subject.yearLevel', 'teacherAssignments.section'])->findOrFail($id);

        $assignments = $teacher->teacherAssignments->map(function ($assignment) {
            return [
                'subject' => $assignment->subject->name ?? 'N/A',
                'year_level' => $assignment->subject->yearLevel->name ?? 'N/A',
                'section' => $assignment->section->name ?? 'N/A',
            ];
        });

        return Inertia::render('Head/TeacherDetail', [
            'teacher' => $teacher,
            'assignments' => $assignments,
        ]);
    }
}
