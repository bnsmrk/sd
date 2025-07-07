<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
   public function index()
{
    return Inertia::render('Dashboard', [
        'userSummary' => [
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ],
        'monthlyStats' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr'],
            'students' => [30, 45, 60, 80],
            'teachers' => [5, 8, 10, 12],
        ],
    ]);
}
}
