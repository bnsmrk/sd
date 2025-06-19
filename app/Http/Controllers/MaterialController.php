<?php
namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Module;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Material;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        'modules' => Module::with(['yearLevel', 'section', 'subject'])->get(),
    ]);
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        'module_id' => 'required|exists:modules,id',
        'type' => 'required|in:material,lesson_plan',
    ]);

    $module = Module::findOrFail($validated['module_id']);

    $path = $request->file('file')->store('materials', 'public');

    Material::create([
        'title' => $validated['title'],
        'file_path' => $path,
        'year_level_id' => $module->year_level_id,
        'section_id' => $module->section_id,
        'subject_id' => $module->subject_id,
        'user_id' => auth()->id(),
        'type' => $validated['type'],
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
