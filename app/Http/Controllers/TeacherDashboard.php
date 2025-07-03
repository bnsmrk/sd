<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class TeacherDashboard extends Controller
{
    public function index()
   {
       return Inertia::render('TeacherAssignments.TeacherDashboard');
   }
}
