<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\YearLevel;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;

class HeadDashboard extends Controller
{
    public function index()
    {
        $filters = Request::only('search');

        $query = User::where('role', 'teacher');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        $teachers = $query->with('teacherAssignments.subject.yearLevel', 'teacherAssignments.section')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($teacher) {
                $assignments = $teacher->teacherAssignments;

                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'subject_count' => $assignments->pluck('subject_id')->unique()->count(),
                    'section_count' => $assignments->pluck('section_id')->unique()->count(),
                    'year_level_count' => $assignments->pluck('subject.year_level_id')->unique()->count(),
                ];
            });

        $yearLevels = YearLevel::withCount(['students'])->get();
        $sectionCount = Section::count();
        $teacherCount = User::where('role', 'teacher')->count();

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
