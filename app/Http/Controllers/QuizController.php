<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QuizController extends Controller
{
    public function show($id)
    {
        $quiz = Activity::with('questions')->findOrFail($id);

        $alreadyTaken = StudentAnswer::where('user_id', Auth::id())
            ->where('activity_id', $id)
            ->exists();

        if ($alreadyTaken) {
            return redirect()->route('student.subjects')->with('error', 'You have already taken this quiz.');
        }

        return Inertia::render('Student/TakeQuiz', [
            'quiz' => $quiz,
        ]);
    }

    public function submit(Request $request)
{
    $user = Auth::user();
    $quizId = $request->quiz_id;

    // Check if already taken
    $alreadyTaken = \App\Models\StudentAnswer::where('user_id', $user->id)
        ->where('activity_id', $quizId)
        ->exists();

    if ($alreadyTaken) {
        return redirect()->route('student.subjects')->with('error', 'You already took this quiz.');
    }

    $score = 0;
    $total = 0;

    foreach ($request->answers as $questionId => $answer) {
        $question = \App\Models\Question::find($questionId);

        if (!$question) continue;

        $isCorrect = null;

        if ($question->type === 'checkbox') {
            // JSON encode for checkboxes
            $isCorrect = json_encode(array_map('strtolower', $answer)) === json_encode(array_map('strtolower', json_decode($question->answer_key, true)));
        } elseif ($question->type !== 'essay') {
            $isCorrect = strtolower(trim($question->answer_key)) === strtolower(trim($answer));
        }

        if ($isCorrect === true) {
            $score++;
        }

        $total++;

        \App\Models\StudentAnswer::create([
            'user_id' => $user->id,
            'activity_id' => $quizId, // ✅ FIXED: include activity_id
            'question_id' => $questionId,
            'answer' => is_array($answer) ? json_encode($answer) : $answer,
            'is_correct' => $isCorrect,
        ]);
    }

    return redirect()->route('student.subjects')->with('success', "Quiz submitted successfully! Score: $score / $total");
}

}
