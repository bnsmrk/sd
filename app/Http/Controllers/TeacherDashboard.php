<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\TeacherSubAssignment;
use Illuminate\Support\Facades\Auth;

class TeacherDashboard extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $subAssignments = TeacherSubAssignment::with('section')
            ->whereHas('teacherAssignment', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get();

        $totalAssignments = $subAssignments->count();
        $totalSections = $subAssignments->pluck('section_id')->unique()->count();

        $sectionIds = $subAssignments->pluck('section_id')->unique()->toArray();
        $totalStudents = Student::whereIn('section_id', $sectionIds)->count();

        return Inertia::render('TeacherAssignments/TeacherDashboard', [
            'stats' => [
                'total_students' => $totalStudents,
                'total_sections' => $totalSections,
                'total_assignments' => $totalAssignments,
            ],
        ]);
    }
}
