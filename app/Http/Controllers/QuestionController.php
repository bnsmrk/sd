<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use App\Models\Question;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;
class QuestionController extends Controller

{

    public function index()
    {
        return Inertia::render('Questions/Create', [
            'activity' => $activity,
        ]);
    }


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


    public function downloadCsv(Activity $activity)
    {
        $questions = $activity->questions()->get();

        $filename = 'activity_' . $activity->id . '_questions.csv';
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
        ];

        $columns = ['Question #', 'Question', 'Type', 'Choices', 'Answer Key'];

        $callback = function () use ($questions, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($questions as $index => $question) {
                fputcsv($file, [
                    $index + 1,
                    $question->question,
                    $question->type,
                    $question->options ? implode('; ', json_decode($question->options, true)) : 'N/A',
                    is_array(json_decode($question->answer_key, true))
                        ? implode(', ', json_decode($question->answer_key, true))
                        : $question->answer_key,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadPdf(Activity $activity)
    {
        $questions = $activity->questions()->get();

        $pdf = Pdf::loadView('pdf.questions', [
            'activity' => $activity,
            'questions' => $questions,
        ]);

        return $pdf->download('activity_' . $activity->id . '_questions.pdf');
    }


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
