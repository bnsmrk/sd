<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
   public function show($id)
    {
        $quiz = Activity::with('questions')
        ->where('id', $id)
        ->firstOrFail();

    return Inertia::render('Student/TakeQuiz', [
        'quiz' => $quiz,
    ]);
    }

    public function submit(Request $request)
    {
        $user = Auth::user();

        foreach ($request->answers as $questionId => $answer) {
            $question = Question::find($questionId);

            if (!$question) continue;

            $isCorrect = null;

            if ($question->type !== 'essay') {
                $isCorrect = strtolower(trim($question->answer_key)) === strtolower(trim($answer));
            }

            StudentAnswer::create([
                'user_id' => $user->id,
                'question_id' => $questionId,
                'answer' => $answer,
                'is_correct' => $isCorrect,
            ]);
        }

        return redirect()->route('student.subjects')->with('success', 'Quiz submitted successfully!');
    }
}
