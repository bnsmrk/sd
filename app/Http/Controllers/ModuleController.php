<?php
namespace App\Http\Controllers;

use App\Models\Module;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::with(['yearLevel', 'section', 'subject'])->get();

        return Inertia::render('Modules/Index', [
            'modules' => $modules,
        ]);
    }

    public function create()
    {
        return Inertia::render('Modules/Create', [
            'yearLevels' => YearLevel::all(['id', 'name']),
            'sections' => Section::all(['id', 'name', 'year_level_id']),
            'subjects' => Subject::all(['id', 'name', 'year_level_id', 'section_id']),
        ]);
    }

    public function store(Request $request)
    {
       $request->validate([
    'name' => 'required|string|max:255',
    'year_level_id' => 'required|exists:year_levels,id',
    'section_id' => 'required|exists:sections,id',
    'subject_id' => 'required|exists:subjects,id',
]);

Module::create([
    'name' => $request->name,
    'year_level_id' => $request->year_level_id,
    'section_id' => $request->section_id,
    'subject_id' => $request->subject_id,
]);

    return redirect()->route('modules.index')->with('success', 'Module created.');
    }

    public function edit(Module $module)
{
    return Inertia::render('Modules/Edit', [
        'module' => $module,
        'yearLevels' => \App\Models\YearLevel::all(),
        'sections' => \App\Models\Section::all(),
        'subjects' => \App\Models\Subject::all(),
    ]);
}

public function update(Request $request, Module $module)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'year_level_id' => 'required|exists:year_levels,id',
        'section_id' => 'required|exists:sections,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    $module->update($validated);

    return redirect()->route('modules.index')->with('success', 'Module updated successfully.');
}

    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('modules.index')->with('success', 'Module deleted successfully!');
    }
}
