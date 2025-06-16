<?php
namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with(['yearLevel', 'section', 'subject', 'user'])->latest()->get();
        return Inertia::render('Materials/Index', [
            'materials' => $materials,
        ]);
    }

    public function create()
    {
        return Inertia::render('Materials/Create', [
            'yearLevels' => YearLevel::all(),
            'sections' => Section::all(),
            'subjects' => Subject::all(),
        ]);
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        'year_level' => 'required|exists:year_levels,name',
        'section' => 'required|exists:sections,name',
        'subject' => 'required|exists:subjects,name',
    ]);

    // Get IDs based on names
    $yearLevelId = YearLevel::where('name', $request->year_level)->value('id');
    $sectionId = Section::where('name', $request->section)->value('id');
    $subjectId = Subject::where('name', $request->subject)->value('id');

    $path = $request->file('file')->store('materials', 'public');

    Material::create([
        'title' => $validated['title'],
        'file_path' => $path,
        'year_level_id' => $yearLevelId,
        'section_id' => $sectionId,
        'subject_id' => $subjectId,
        'user_id' => auth()->id(),
    ]);

     return redirect()->route('materials.index')->with('success', 'Material uploaded successfully.');
}

    public function edit(Material $material)
    {
        return Inertia::render('Materials/Edit', [
            'material' => $material,
            'yearLevels' => YearLevel::all(),
            'sections' => Section::all(),
            'subjects' => Subject::all(),
        ]);
    }

    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $material->update($validated);

        return redirect()->route('materials.index')->with('success', 'Material updated.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Material deleted.');
    }
}
