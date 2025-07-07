<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\ProficiencyTest;
use App\Models\ProficiencyQuestion;
use Illuminate\Support\Facades\Validator;

class ProficiencyQuestionController extends Controller
{
    public function create(ProficiencyTest $proficiencyTest)
{
    $proficiencyTest->load('yearLevel', 'proficiencyQuestions');

    return Inertia::render('Head/CreateQuestions', [
        'proficiencyTest' => [
            'id' => $proficiencyTest->id,
            'title' => $proficiencyTest->title,
            'type' => $proficiencyTest->type,
            'scheduled_at' => $proficiencyTest->scheduled_at->format('Y-m-d H:i'),
            'year_level' => [
                'name' => $proficiencyTest->yearLevel->name,
            ],
        ],
        'existingQuestions' => $proficiencyTest->proficiencyQuestions->map(function ($q) {
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


   public function store(Request $request, ProficiencyTest $proficiencyTest)
{
    $data = $request->all();

    $validator = Validator::make($data, [
        'questions' => 'required|array|min:1',
        'questions.*.id' => 'nullable|integer|exists:proficiency_questions,id',
        'questions.*.question' => 'required|string',
        'questions.*.type' => 'required|in:multiple_choice,checkboxes,true_false,essay,fill_in_blank',
        'questions.*.options' => 'nullable|array',
        'questions.*.answer_key' => 'nullable',
    ]);

    $validator->validate();

    $existingIds = $proficiencyTest->proficiencyQuestions()->pluck('id')->toArray();
    $submittedIds = [];

    foreach ($data['questions'] as $q) {
        $answerKey = is_array($q['answer_key']) ? json_encode($q['answer_key']) : $q['answer_key'];
        $options = isset($q['options']) ? json_encode($q['options']) : null;

        if (isset($q['id'])) {
            $submittedIds[] = $q['id'];
            $question = ProficiencyQuestion::find($q['id']);
            $question->update([
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $options,
                'answer_key' => $answerKey,
            ]);
        } else {
            $new = ProficiencyQuestion::create([
                'proficiency_test_id' => $proficiencyTest->id,
                'question' => $q['question'],
                'type' => $q['type'],
                'options' => $options,
                'answer_key' => $answerKey,
            ]);
            $submittedIds[] = $new->id;
        }
    }

    $toDelete = array_diff($existingIds, $submittedIds);
    ProficiencyQuestion::destroy($toDelete);

    return redirect()->route('proficiency-test.index')->with('success', 'Questions updated successfully.');
}


}
