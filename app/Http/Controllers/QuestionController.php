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
    $activity->load([
        'module.yearLevel',
        'module.section',
        'module.subject',
    ]);

    $questions = $activity->questions()->get();

    return Inertia::render('Questions/Create', [
        'activity' => [
            'id' => $activity->id,
            'title' => $activity->title,
            'type' => $activity->type,
            'scheduled_at' => $activity->scheduled_at->format('Y-m-d H:i'),
            'module' => [
                'name' => $activity->module->name,
                'year_level' => [
                    'name' => $activity->module->yearLevel->name,
                ],
                'section' => [
                    'name' => $activity->module->section->name,
                ],
                'subject' => [
                    'name' => $activity->module->subject->name,
                ],
            ],
        ],
        'existingQuestions' => $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'type' => $q->type,
                'options' => $q->options,
                'answer_key' => $q->answer_key,
            ];
        }),
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
        'questions.*.type' => 'required|in:multiple_choice,checkboxes,true_false,essay,fill_in_blank',
        'questions.*.options' => 'nullable|array',
        'questions.*.answer_key' => 'nullable',
    ]);

    $validator->validate();

    $existingIds = $activity->questions()->pluck('id')->toArray();
    $submittedIds = [];

    foreach ($data['questions'] as $q) {
        $answerKey = is_array($q['answer_key']) ? json_encode($q['answer_key']) : $q['answer_key'];
        $options = isset($q['options']) ? json_encode($q['options']) : null;

        if (isset($q['id'])) {
            $submittedIds[] = $q['id'];
            $question = Question::find($q['id']);
            $question->update([
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $options,
                'answer_key' => $answerKey,
            ]);
        } else {
            $new = Question::create([
                'activity_id' => $activity->id,
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $options,
                'answer_key' => $answerKey,
            ]);
            $submittedIds[] = $new->id;
        }
    }

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
