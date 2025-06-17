<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $quizId = $request->input('quiz_id');
        $answers = $request->input('answers', []);

        // Prevent double submission
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

                if ($question->type === 'checkbox') {
                    $userAnswer = is_array($answer) ? $answer : json_decode($answer, true);
                    $correct = json_decode($correctAnswer, true);

                    $userAnswer = array_map(fn($v) => strtolower(trim($v)), $userAnswer);
                    $correct = array_map(fn($v) => strtolower(trim($v)), $correct);

                    sort($userAnswer);
                    sort($correct);

                    $isCorrect = $userAnswer === $correct;
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
