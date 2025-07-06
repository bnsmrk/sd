<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use App\Models\TeacherAssignment;
use Illuminate\Support\Facades\Auth;

class TeacherDashboard extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $assignments = TeacherAssignment::where('user_id', $userId)->get();

        $totalSections = $assignments->pluck('section_id')->unique()->filter()->count();
        $sectionIds = $assignments->pluck('section_id')->unique()->filter()->toArray();
        $totalStudents = Student::whereIn('section_id', $sectionIds)->count();

        return Inertia::render('TeacherAssignments/TeacherDashboard', [
            'stats' => [
                'total_students' => $totalStudents,
                'total_sections' => $totalSections,
                'total_assignments' => $assignments->count(),
            ],
        ]);
    }
}
