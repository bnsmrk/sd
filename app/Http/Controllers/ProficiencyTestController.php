<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use App\Models\ProficiencyTest;

class ProficiencyTestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tests = ProficiencyTest::with('yearLevel')->latest()->get();

        return Inertia::render('Head/ProficiencyTest', [
            'tests' => $tests,
        ]);
    }

    public function create()
    {
        return Inertia::render('Head/ProficiencyTestCreate', [
            'yearLevels' => YearLevel::all(),
            // 'sections' => Section::all(),
            // 'subjects' => Subject::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:reading,numerical',
            'year_level_id' => 'required|exists:year_levels,id',

            'scheduled_at' => 'required|date',
            'description' => 'nullable|string',
        ]);

        ProficiencyTest::create($validated);

        return redirect()->route('proficiency-test.index')->with('success', 'Test created successfully.');

    }

    public function edit(string $id)
    {
        $test = ProficiencyTest::findOrFail($id);

        return Inertia::render('Head/ProficiencyTestEdit', [
            'test' => $test,
            'yearLevels' => YearLevel::all(),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $test = ProficiencyTest::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:reading,numerical',
            'year_level_id' => 'required|exists:year_levels,id',

            'scheduled_at' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $test->update($validated);

        return redirect()->route('proficiency-test.index')->with('success', 'Test updated.');
    }

    public function destroy(string $id)
{
    ProficiencyTest::findOrFail($id)->delete();

    return redirect()->route('proficiency-test.index')->with('success', 'Test deleted.');
}
}
