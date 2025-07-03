<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class IctDashboard extends Controller
{
   public function index()
   {
       return Inertia::render('Ict/IctDashboard');
   }
}
