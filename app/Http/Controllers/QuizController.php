<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
    $quizId = $request->input('quiz_id');
    $answers = $request->input('answers', []);

    $alreadySubmitted = StudentAnswer::where('user_id', $user->id)
        ->where('activity_id', $quizId)
        ->exists();

    if ($alreadySubmitted) {
        return redirect()->route('student.subjects')->with('error', 'You have already submitted this quiz.');
    }

    foreach ($answers as $questionId => $answer) {
        $question = Question::find($questionId);
        if (!$question) continue;

        $isCorrect = null;

        if ($question->type !== 'essay') {
            $correctAnswer = $question->answer_key;

            if ($question->type === 'checkboxes') {
                // Parse both correct and user answers
                $userAnswer = is_array($answer) ? $answer : json_decode($answer, true);
                $correct = is_array($correctAnswer) ? $correctAnswer : json_decode($correctAnswer, true);

                if (!is_array($userAnswer) || !is_array($correct)) {
                    \Log::error('❌ Checkbox answer not in array format', [
                        'question_id' => $questionId,
                        'user_answer' => $userAnswer,
                        'correct_answer' => $correct,
                    ]);
                    $isCorrect = false;
                } else {
                    // Normalize (lowercase & trim)
                    $userAnswer = array_map(fn($v) => strtolower(trim($v)), $userAnswer);
                    $correct = array_map(fn($v) => strtolower(trim($v)), $correct);

                    sort($userAnswer);
                    sort($correct);

                    $isCorrect = $userAnswer === $correct;

                    \Log::info('✅ Checkbox comparison', [
                        'question_id' => $questionId,
                        'user_answer' => $userAnswer,
                        'correct_answer' => $correct,
                        'is_correct' => $isCorrect,
                    ]);
                }
            } else {
                $isCorrect = strtolower(trim($correctAnswer)) === strtolower(trim(is_array($answer) ? '' : $answer));
            }
        }

        StudentAnswer::create([
            'user_id' => $user->id,
            'activity_id' => $quizId,
            'question_id' => $questionId,
            'answer' => is_array($answer) ? json_encode($answer) : $answer,
            'is_correct' => $isCorrect,
        ]);
    }

    return redirect()->route('student.subjects')->with('success', 'Quiz submitted successfully!');
}


}
