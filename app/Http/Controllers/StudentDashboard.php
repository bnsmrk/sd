<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class StudentDashboard extends Controller
{
   public function index()
   {
       return Inertia::render('Student.StudentDashboard');
   }
}
