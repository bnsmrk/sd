<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Questions/Create', [
            'activity' => $activity,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Activity $activity)
    {
       $questions = $activity->questions()->get();

    return Inertia::render('Questions/Create', [
        'activity' => $activity,
        'existingQuestions' => $questions,
    ]);
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(Request $request, Activity $activity)
{
    $data = $request->all();

    $validator = Validator::make($data, [
        'questions' => 'required|array|min:1',
        'questions.*.id' => 'nullable|integer|exists:questions,id',
        'questions.*.question' => 'required|string',
        'questions.*.type' => 'required|in:multiple_choice,true_false,essay',
        'questions.*.options' => 'nullable|array',
        'questions.*.answer_key' => 'nullable|string',
    ]);

    $validator->validate();

    $existingIds = $activity->questions()->pluck('id')->toArray();
    $submittedIds = [];

    foreach ($data['questions'] as $q) {
        if (isset($q['id'])) {
            // Update existing question
            $submittedIds[] = $q['id'];
            $question = Question::find($q['id']);
            $question->update([
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $q['type'] === 'multiple_choice' ? json_encode($q['options']) : null,
                'answer_key' => $q['answer_key'],
            ]);
        } else {
            // Create new question
            $new = Question::create([
                'activity_id' => $activity->id,
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $q['type'] === 'multiple_choice' ? json_encode($q['options']) : null,
                'answer_key' => $q['answer_key'],
            ]);
            $submittedIds[] = $new->id;
        }
    }

    // Delete removed questions
    $toDelete = array_diff($existingIds, $submittedIds);
    Question::destroy($toDelete);

    return redirect()->route('activities.index')->with('success', 'Questions updated successfully.');
}




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
