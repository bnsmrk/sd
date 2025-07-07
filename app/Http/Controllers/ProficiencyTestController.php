<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use App\Models\ProficiencyTest;
use App\Models\ProficiencyQuestion;
use Illuminate\Support\Facades\Auth;
use App\Models\StudentProficiencyAnswer;
use App\Models\StudentProficiencyResult;

class ProficiencyTestController extends Controller
{
    public function index()
    {
        $tests = ProficiencyTest::with('yearLevel')->latest()->get();

        return Inertia::render('Head/ProficiencyTest', [
            'tests' => $tests,
        ]);
    }

    public function create()
    {
        return Inertia::render('Head/ProficiencyTestCreate', [
            'yearLevels' => YearLevel::all(),

        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:reading,numerical',
            'year_level_id' => 'required|exists:year_levels,id',
            'scheduled_at' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:scheduled_at',
            'description' => 'nullable|string',
        ]);

        ProficiencyTest::create($validated);

        return redirect()->route('proficiency-test.index')->with('success', 'Test created successfully.');

    }

    public function edit(string $id)
    {
        $test = ProficiencyTest::findOrFail($id);

        return Inertia::render('Head/ProficiencyTestEdit', [
           'test' => [
        'id' => $test->id,
        'title' => $test->title,
        'type' => $test->type,
        'year_level_id' => $test->year_level_id,
        'scheduled_at' => $test->scheduled_at->format('Y-m-d\TH:i'),
        'due_date' => $test->due_date?->format('Y-m-d\TH:i'),

        'description' => $test->description,
    ],
    'yearLevels' => YearLevel::all(['id', 'name']),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $test = ProficiencyTest::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:reading,numerical',
            'year_level_id' => 'required|exists:year_levels,id',
            'scheduled_at' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:scheduled_at',
            'description' => 'nullable|string',
        ]);

        $test->update($validated);

        return redirect()->route('proficiency-test.index')->with('success', 'Test updated.');
    }

    public function destroy(string $id)
    {
        ProficiencyTest::findOrFail($id)->delete();

        return redirect()->route('proficiency-test.index')->with('success', 'Test deleted.');
    }

   public function submit(Request $request, $testId)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []);

        $alreadySubmitted = StudentProficiencyResult::where('user_id', $user->id)
            ->where('proficiency_test_id', $testId)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('student.dashboard')->with('error', 'You already submitted this test.');
        }

        $questions = ProficiencyQuestion::where('proficiency_test_id', $testId)->get();

        $score = 0;
        $total = $questions->count();

        foreach ($questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $isCorrect = null;
            $earned = 0;

            $correctAnswer = $question->answer_key;

            switch ($question->type) {
                case 'multiple_choice':
                case 'true_false':
                case 'fill_in_blank':
                    $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));
                    break;

                case 'checkboxes':
                    $userArray = is_array($userAnswer) ? $userAnswer : json_decode($userAnswer, true);
                    $correctArray = is_array($correctAnswer) ? $correctAnswer : json_decode($correctAnswer, true);

                    $userArray = array_map('strtolower', array_map('trim', $userArray ?? []));
                    $correctArray = array_map('strtolower', array_map('trim', $correctArray ?? []));

                    sort($userArray);
                    sort($correctArray);

                    $isCorrect = $userArray === $correctArray;
                    break;

                case 'essay':
                    $isCorrect = null;
                    break;
            }

            if ($isCorrect === true) {
                $earned = 1;
                $score += 1;
            }

            StudentProficiencyAnswer::create([
                'user_id' => $user->id,
                'proficiency_test_id' => $testId,
                'question_id' => $question->id,
                'answer' => is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer,
                'is_correct' => $isCorrect,
                'score' => $earned,
            ]);
        }

        StudentProficiencyResult::create([
            'user_id' => $user->id,
            'proficiency_test_id' => $testId,
            'score' => $score,
            'total_points' => $total,
        ]);

        return Inertia::render('Student/ProficiencyResult', [
            'score' => $score,
            'total' => $total,
        ]);
    }


    public function show($id)
    {
        $userId = Auth::id();

        $alreadySubmitted = StudentProficiencyResult::where('user_id', $userId)
            ->where('proficiency_test_id', $id)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('student.dashboard')->with('error', 'You have already taken this test.');
        }

        $test = ProficiencyTest::with('questions')->findOrFail($id);

        return Inertia::render('Student/ProficiencyTestTake', [
            'test' => $test,
        ]);
    }

}
