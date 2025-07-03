<?php

namespace App\Http\Controllers;

use App\Models\YearLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class YearLevelController extends Controller
{
    public function index()
    {
        $yearLevels = YearLevel::all();
        return Inertia::render('YearLevel/Index', [
            'yearLevels' => $yearLevels
        ]);
    }

    public function create()
    {
        return Inertia::render('YearLevel/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        YearLevel::create($validated);

        return redirect()->route('year-levels.index')->with('success', 'Year Level added successfully.');

    }

    public function edit(YearLevel $yearLevel)
    {
        return Inertia::render('YearLevel/Edit', [
            'yearLevel' => $yearLevel
        ]);
    }

    public function update(Request $request, YearLevel $yearLevel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $yearLevel->update($validated);

        return redirect()->route('year-levels.index')->with('success', 'Year Level updated successfully.');
    }

    public function destroy(YearLevel $yearLevel)
    {
        $yearLevel->delete();
        return redirect()->back()->with('danger', 'Year Level deleted.');
    }
}
