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
        'yearLevels' => YearLevel::all(),
        'subjects' => Subject::with('yearLevel')->get(),
    ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'year_level_id' => 'required|exists:year_levels,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    Module::create($validated);

    return redirect()->route('modules.index')->with('success', 'Module created successfully.');
}


    public function edit(Module $module)
{
    return Inertia::render('Modules/Edit', [
        'module' => $module,
        'yearLevels' => YearLevel::all(),
        'subjects' => Subject::with('yearLevel')->get(),
    ]);
}

public function update(Request $request, Module $module)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'year_level_id' => 'required|exists:year_levels,id',
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
