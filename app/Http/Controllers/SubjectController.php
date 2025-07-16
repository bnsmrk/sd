<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\YearLevel;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['yearLevel', 'section']);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $subjects = $query->paginate(10)->withQueryString();

        return Inertia::render('Subject/Index', [
            'subjects' => $subjects,
            'yearLevels' => YearLevel::all(),
            'sections' => Section::all(),
            'filters' => $request->only('search'),
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

        $sharedSubjects = array_filter($request->shared_subjects ?? []);
        $majorSubjects = array_filter($request->major_subjects ?? []);
        $allSubjects = array_merge($sharedSubjects, $majorSubjects);

        $existingSubjects = Subject::whereIn('name', $allSubjects)
            ->pluck('name')
            ->toArray();

        if (!empty($existingSubjects)) {
            throw ValidationException::withMessages([
                'shared_subjects' => ['The following subject(s) already exist: ' . implode(', ', $existingSubjects)],
            ]);
        }

        foreach ($sharedSubjects as $name) {
            Subject::create([
                'name' => $name,
                'year_level_id' => $request->year_level_id,
                'section_id' => null,
            ]);
        }

        foreach ($majorSubjects as $name) {
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

        $exists = Subject::where('name', $request->name)
            ->where('id', '!=', $subject->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'name' => ['Subject name already exists.'],
            ]);
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
