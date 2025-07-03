<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\YearLevel;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['yearLevel', 'section'])->get();

        return Inertia::render('Subject/Index', [
            'subjects' => $subjects,
             'yearLevels' => YearLevel::all(), 
              'sections' => Section::all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Subject/Create', [
            'yearLevels' => YearLevel::all(),
             'sections' => Section::all(),
        ]);
    }

    public function store(Request $request)
{
    $request->validate([
        'year_level_id' => 'required|exists:year_levels,id',
        'shared_subjects' => 'nullable|array',
        'shared_subjects.*' => 'nullable|string|max:255',
        'major_subjects' => 'nullable|array',
        'major_subjects.*' => 'nullable|string|max:255',
    ]);

    foreach (array_filter($request->shared_subjects ?? []) as $name) {
        Subject::create([
            'name' => $name,
            'year_level_id' => $request->year_level_id,
            'section_id' => null,
        ]);
    }

    foreach (array_filter($request->major_subjects ?? []) as $name) {
        Subject::create([
            'name' => $name,
            'year_level_id' => $request->year_level_id,
            'section_id' => null,
        ]);
    }

    return redirect()->route('subjects.index')->with('success', 'Subjects created.');
}


    public function edit(Subject $subject)
    {
        return Inertia::render('Subject/Edit', [
            'subject' => $subject,
            'yearLevels' => YearLevel::all(),
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $yearLevel = YearLevel::findOrFail($request->year_level_id);
        $isSHS = in_array($yearLevel->name, ['Grade 11', 'Grade 12']);

        // Prevent duplicates for Grade 7–10
        if (!$isSHS) {
            $exists = Subject::where('year_level_id', $request->year_level_id)
                ->whereNull('section_id')
                ->where('name', $request->name)
                ->where('id', '!=', $subject->id)
                ->exists();

            if ($exists) {
                return back()->withErrors(['name' => 'Subject already exists for this year level.']);
            }
        }

        $subject->update([
            'name' => $request->name,
            'year_level_id' => $request->year_level_id,
        ]);

        return redirect()->route('subjects.index')->with('warning', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('danger', 'Subject deleted.');
    }
}
