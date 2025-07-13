<?php

namespace App\Http\Controllers;

use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Inertia\Inertia;

class PrincipalDashboard extends Controller
{
    public function index()
    {
        return Inertia::render('Principal/PrincipalDashboard', [
            'counts' => [
                'yearLevels' => YearLevel::count(),
                'sections' => Section::count(),
                'subjects' => Subject::count(),
                'teachers' => User::where('role', 'teacher')->count(),
                'students' => User::where('role', 'student')->count(),
                'heads' => User::where('role', 'head')->count(),
                'ict' => User::where('role', 'ict')->count(),
            ],
        ]);
    }
}
