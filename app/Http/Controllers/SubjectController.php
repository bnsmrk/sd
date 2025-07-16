<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['yearLevel', 'section']);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $subjects = $query->paginate(5)->withQueryString();

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

        $request->merge([
            'shared_subjects' => array_filter($request->shared_subjects, fn($s) => trim($s) !== ''),
            'major_subjects' => array_filter($request->major_subjects ?? [], fn($s) => trim($s) !== ''),
        ]);

        $validated = $request->validate([
            'year_level_id' => ['required', 'exists:year_levels,id'],
            'shared_subjects' => ['required', 'array'],
            'shared_subjects.*' => [
                'required',
                'string',
                'distinct',
                'max:255',
                Rule::unique('subjects', 'name')->where(fn($q) => $q->where('year_level_id', $request->year_level_id)),
            ],
            'major_subjects' => ['nullable', 'array'],
            'major_subjects.*' => [
                'required_with:major_subjects',
                'string',
                'distinct',
                'max:255',
                Rule::unique('subjects', 'name')->where(fn($q) => $q->where('year_level_id', $request->year_level_id)),
            ],
        ], [
            'shared_subjects.required' => 'Please enter at least one shared subject.',
            'shared_subjects.*.required' => 'Each shared subject is required.',
            'shared_subjects.*.unique' => 'The subject/s has already been taken.',
            'shared_subjects.*.distinct' => 'Duplicate shared subject found.',

            'major_subjects.*.required_with' => 'Each major subject is required.',
            'major_subjects.*.unique' => 'The subject/s has already been taken.',
            'major_subjects.*.distinct' => 'Duplicate major subject found.',
        ]);

        try {
            foreach ($validated['shared_subjects'] as $subjectName) {
                Subject::create([
                    'name' => $subjectName,
                    'year_level_id' => $validated['year_level_id'],
                ]);
            }

            foreach ($validated['major_subjects'] ?? [] as $subjectName) {
                Subject::create([
                    'name' => $subjectName,
                    'year_level_id' => $validated['year_level_id'],
                ]);
            }

            return to_route('subjects.index')->with('success', 'Subjects created successfully.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw ValidationException::withMessages([
                    'shared_subjects.0' => 'The subject already exists in another year level.',
                ]);
            }
        }
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

        try {
            $exists = Subject::where('name', $request->name)
                ->where('year_level_id', $request->year_level_id)
                ->where('id', '!=', $subject->id)
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'name' => ['Subject name already exists for this year level.'],
                ]);
            }

            $subject->update([
                'name' => $request->name,
                'year_level_id' => $request->year_level_id,
            ]);

            return redirect()->route('subjects.index')->with('warning', 'Subject updated.');
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw ValidationException::withMessages([
                    'name' => ['This subject already exists in another year level.'],
                ]);
            }
        }
    }



    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('subjects.index')->with('danger', 'Subject deleted.');
    }
}
