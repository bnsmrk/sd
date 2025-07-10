<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use App\Models\StudentQuizResult;
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

    $alreadySubmitted = StudentQuizResult::where('user_id', $user->id)
        ->where('activity_id', $quizId)
        ->exists();

    if ($alreadySubmitted) {
        return redirect()->route('student.subjects')->with('error', 'You already submitted this quiz.');
    }

    $quiz = Activity::with(['questions', 'module'])->findOrFail($quizId);

    $score = 0;
    $total = 0;

    foreach ($answers as $questionId => $answer) {
        $question = Question::find($questionId);
        if (!$question) continue;
    
        $points = 1;
        $isCorrect = null;
        $earned = 0;
    
        if ($question->type !== 'essay') {
            $correctAnswer = $question->answer_key;
    
            if ($question->type === 'checkboxes') {
                $userAnswer = is_array($answer) ? $answer : json_decode($answer, true);
                $correct = is_array($correctAnswer) ? $correctAnswer : json_decode($correctAnswer, true);
    
                $userAnswer = array_map(fn($v) => strtolower(trim($v)), $userAnswer ?? []);
                $correct = array_map(fn($v) => strtolower(trim($v)), $correct ?? []);
    
                sort($userAnswer);
                sort($correct);
                Log::debug('Checkbox Validation', [
                    'question_id' => $questionId,
                    'userAnswer' => $userAnswer,
                    'correctAnswer' => $correct,
                    'isMatch' => $userAnswer === $correct,
                ]);
            
                $isCorrect = $userAnswer === $correct;
            } else {
                $isCorrect = strtolower(trim($correctAnswer)) === strtolower(trim(is_array($answer) ? '' : $answer));
            }
    
            if ($isCorrect) {
                $earned = $points;
                $score += $earned;
            }
        }
    
        // ✅ Always increment total — even essay
        $total += $points;
    
        StudentAnswer::create([
            'user_id' => $user->id,
            'activity_id' => $quizId,
            'question_id' => $questionId,
            'answer' => is_array($answer) ? json_encode($answer) : $answer,
            'is_correct' => $isCorrect,
            'score' => $earned,
        ]);
    }
    

    StudentQuizResult::create([
        'user_id' => $user->id,
        'activity_id' => $quizId,
        'score' => $score,
        'total_points' => $total,
    ]);

    return Inertia::render('Student/QuizResult', [
    'score' => $score,
    'total' => $total,
    'quizId' => $quiz->id,
    'subjectId' => $quiz->module->subject_id,
]);

}


}
