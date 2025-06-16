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
            'subjects' => $subjects
        ]);
    }

    public function create()
    {
        $yearLevels = YearLevel::all();
        $sections = Section::all();

        return Inertia::render('Subject/Create', [
             'yearLevels' => YearLevel::all(),
        'sections' => Section::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        Subject::create($request->only('name', 'year_level_id', 'section_id'));

        return redirect()->route('subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject)
{
    $yearLevels = YearLevel::all();
    $sections = Section::all();

    return Inertia::render('Subject/Edit', [
        'subject' => $subject,
        'yearLevels' => $yearLevels,
        'sections' => $sections,
    ]);
}


    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
        ]);

        $subject->update($request->only('name', 'year_level_id', 'section_id'));

        return redirect()->route('subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted.');
    }
}
