<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HeadDashboard extends Controller
{
    public function index()
    {
        $yearLevels = YearLevel::withCount(['students'])->get();
        $sectionCount = Section::count();
        $teacherCount = User::where('role', 'teacher')->count(); 

        // Sample chart data: monthly student registration (replace with actual data)
        $chartLabels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
        $barData = [40, 55, 32, 50, 70, 30, 90]; // example
        $lineData = [30, 35, 25, 45, 60, 20, 80]; // example

        return Inertia::render('Head/HeadDashboard', [
            'yearLevels' => $yearLevels,
            'sectionCount' => $sectionCount,
            'teacherCount' => $teacherCount,
            'chart' => [
                'labels' => $chartLabels,
                'barData' => $barData,
                'lineData' => $lineData,
            ],
        ]);
    }
}
