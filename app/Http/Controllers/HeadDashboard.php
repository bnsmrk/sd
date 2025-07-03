<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class HeadDashboard extends Controller
{
  public function index()
   {
       return Inertia::render('Head/HeadDashboard');
   }
}
