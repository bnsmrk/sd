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
        'modules' => Module::with(['yearLevel', 'subject'])->get(),
        'subjects' => Subject::with(['yearLevel'])->get(),
        'yearLevels' => YearLevel::all(),
    ]);
}


  public function store(Request $request)
    {
        \Log::info('Material Store Request:', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:material,lesson_plan',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'module_id' => 'nullable|exists:modules,id',
            'subject_id' => 'nullable|exists:subjects,id',
        ]);

        $path = $request->file('file')->store('materials', 'public');

        // Initialize
        $year_level_id = null;
        $section_id = null;
        $subject_id = null;

        if ($request->type === 'material') {
            $module = Module::findOrFail($request->module_id);
            $year_level_id = $module->year_level_id;
            $section_id = $module->section_id;
            $subject_id = $module->subject_id;
        } else {
            $subject = Subject::with(['yearLevel', 'section'])->findOrFail($request->subject_id);
            $year_level_id = $subject->year_level_id;
            $section_id = $subject->section_id;
            $subject_id = $subject->id;
        }

        Material::create([
            'title' => $request->title,
            'type' => $request->type, // ✅ use what was submitted
            'file_path' => $path,
            'year_level_id' => $year_level_id,
            'section_id' => $section_id,
            'subject_id' => $subject_id,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('materials.index')->with('success', 'Material uploaded successfully.');
    }


//

    public function edit(Material $material)
    {
         return Inertia::render('Materials/Edit', [
        'material' => $material,
        'modules' => Module::with(['yearLevel', 'section', 'subject'])->get(),
        'subjects' => Subject::with(['yearLevel', 'section'])->get(),
        'yearLevels' => YearLevel::all(),
    ]);
    }

    public function update(Request $request, Material $material)
{
    \Log::info('Update request', $request->all());

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'type' => 'required|in:material,lesson_plan',
        'year_level_id' => 'required|exists:year_levels,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    $material->update($validated);

    if ($request->hasFile('file')) {
        $path = $request->file('file')->store('materials', 'public');
        $material->update(['file_path' => $path]);
    }

    return redirect()->route('materials.index')->with('success', 'Material updated.');
}




    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Material deleted.');
    }
}
