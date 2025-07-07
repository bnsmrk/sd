<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SectionController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');

    $sections = Section::with('yearLevel')
        ->when($search, fn ($query) =>
            $query->where('name', 'like', "%{$search}%")
        )
        ->orderBy('id')
        ->paginate(5)
        ->withQueryString(); // keeps search in the URL when paginating

    $yearLevels = YearLevel::all();

    return Inertia::render('Section/Index', [
        'sections' => $sections,
        'yearLevels' => $yearLevels,
        'filters' => [
            'search' => $search,
        ],
    ]);
}
    public function create()
    {
        $yearLevels = YearLevel::all();
        return Inertia::render('Section/Create', [
            'yearLevels' => $yearLevels,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        Section::create($validated);

        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }

    public function edit(Section $section)
    {
        $yearLevels = YearLevel::all();
        return Inertia::render('Section/Edit', [
            'section' => $section,
            'yearLevels' => $yearLevels,
        ]);
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year_level_id' => 'required|exists:year_levels,id',
        ]);

        $section->update($validated);

        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('sections.index')->with('success', 'Section deleted successfully.');
    }
}
