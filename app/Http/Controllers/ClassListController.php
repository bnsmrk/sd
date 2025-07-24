<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\TeacherSubAssignment;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClassListController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $subAssignments = TeacherSubAssignment::with('subject')
            ->whereHas('teacherAssignment', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get();

        $sectionIds = $subAssignments->pluck('section_id')->unique()->filter()->values();

        $subjectsBySection = $subAssignments->groupBy('section_id')->map(function ($group) {
            return $group->pluck('subject.name')->unique()->values();
        });

        $studentsQuery = Student::whereIn('section_id', $sectionIds)
            ->selectRaw('MIN(id) as id, user_id, section_id')
            ->groupBy('user_id', 'section_id');

        if ($request->filled('search')) {
            $studentsQuery->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $students = $studentsQuery->paginate(10)->through(function ($s) use ($subjectsBySection) {
            $student = Student::with(['user', 'section', 'yearLevel'])->find($s->id);
            $subjects = $subjectsBySection[$student->section_id] ?? collect();
            return [
                'id' => $student->id,
                'name' => $student->user?->name ?? 'Unnamed',
                'year_level' => $student->yearLevel?->name ?? '-',
                'section' => $student->section?->name ?? '-',
                'subjects' => $subjects->all(),
            ];
        });

        return Inertia::render('TeacherAssignments/ClassList', [
            'students' => $students,
            'filters' => $request->only('search'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $userId = Auth::id();

        $subAssignments = TeacherSubAssignment::with('subject')
            ->whereHas('teacherAssignment', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get();

        $sectionIds = $subAssignments->pluck('section_id')->unique()->filter()->values();

        $subjectsBySection = $subAssignments->groupBy('section_id')->map(function ($group) {
            return $group->pluck('subject.name')->unique()->values();
        });

        $studentsQuery = Student::whereIn('section_id', $sectionIds)
            ->selectRaw('MIN(id) as id, user_id, section_id')
            ->groupBy('user_id', 'section_id');

        if ($request->filled('search')) {
            $studentsQuery->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $students = $studentsQuery->get()->map(function ($s) use ($subjectsBySection) {
            $student = Student::with(['user', 'section', 'yearLevel'])->find($s->id);
            return [
                'Name' => $student->user?->name ?? 'Unnamed',
                'Year Level' => $student->yearLevel?->name ?? '-',
                'Section' => $student->section?->name ?? '-',
                'Subjects' => ($subjectsBySection[$student->section_id] ?? collect())->implode(', '),
            ];
        });

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="class-list.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Year Level', 'Section', 'Subjects']);
            foreach ($students as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
