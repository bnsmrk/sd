<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use App\Models\Submission;
use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubmissionController extends Controller
{
    public function takeEssay(Activity $activity)
{
    return Inertia::render('Student/TakeEssay', [
        'activity' => $activity,
    ]);
}


    public function submitEssay(Request $request, Activity $activity)
{
    $data = $request->validate([
        'content' => 'nullable|string',
        'file' => 'nullable|file|max:10240',
    ]);

    $data['activity_id'] = $activity->id;
    $data['user_id'] = auth()->id();

    if ($request->hasFile('file')) {
        $data['file_path'] = $request->file('file')->store('essay_submissions', 'public');
    }

    Submission::create($data);

    return redirect()->route('dashboard')->with('success', 'Essay submitted successfully!');
}



public function showEssaySubmissions(Request $request, Activity $activity)
{
    $search = $request->input('search');

    // Log the access event
    Log::info('Viewing Essay Submissions', [
        'user_id' => auth()->id(),
        'user_name' => auth()->user()->name ?? 'Guest',
        'activity_id' => $activity->id,
        'activity_title' => $activity->title,
        'search' => $search,
    ]);

    $submissions = Submission::with('user')
        ->where('activity_id', $activity->id)
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('Activities/EssaySubmissions', [
        'activity' => $activity,
        'submissions' => $submissions,
        'filters' => [
            'search' => $search,
        ],
    ]);
}


public function showEssayScoringForm(Activity $activity, $userId = null)
{
    $query = StudentAnswer::with(['user', 'question'])
        ->where('activity_id', $activity->id)
        ->whereHas('question', fn($q) => $q->where('type', 'essay'));

    if ($userId) {
        $query->where('user_id', $userId);
    }

    $studentAnswers = $query->get();

    return Inertia::render('Activities/EssayScoring', [
        'activity' => $activity,
        'answers' => $studentAnswers->map(fn($a) => [
            'id' => $a->id,
            'student' => $a->user?->name ?? 'Unknown',
            'question' => $a->question?->question ?? 'No question found',
            'type' => $a->question?->type ?? 'unknown',
            'answer' => $a->answer ?? 'No answer submitted.',
            'score' => $a->score ?? 0,
        ]),
    ]);
}


public function showEssayAnswerTable(Request $request, Activity $activity)
    {
        $search = $request->input('search');

        $submissions = StudentAnswer::with(['user', 'question'])
            ->where('activity_id', $activity->id)
            ->whereHas('question', fn($q) => $q->where('type', 'essay'))
            ->when($search, function ($query, $search) {
                $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
            })
            ->orderBy('user_id')
            ->paginate(10)
            ->withQueryString();

        Log::info('🧾 Viewing Essay Answer Table', [
            'user_id' => auth()->id(),
            'activity_id' => $activity->id,
            'search' => $search,
        ]);

        return Inertia::render('Activities/EssayAnswerTable', [
            'activity' => $activity,
            'submissions' => $submissions,
            'filters' => ['search' => $search],
        ]);
    }





    public function storeEssayScores(Request $request, Activity $activity)
    {
        // Update individual scores
        foreach ($request->scores as $answerId => $score) {
            StudentAnswer::where('id', $answerId)->update([
                'score' => $score,
            ]);
        }

        // Recalculate total scores per student
        $studentScores = StudentAnswer::where('activity_id', $activity->id)
            ->with('user')
            ->get()
            ->groupBy('user_id');

        foreach ($studentScores as $userId => $answers) {
            $totalScore = $answers->sum('score');
            $totalPossible = $totalScore;

            \App\Models\StudentQuizResult::updateOrCreate(
                [
                    'user_id' => $userId,
                    'activity_id' => $activity->id,
                ],
                [
                    'score' => $totalScore,
                    'total_points' => $totalPossible,
                ]
            );
        }

        \Log::info('✅ Essay Scores Saved and Results Updated', [
            'activity_id' => $activity->id,
            'scored_students' => $studentScores->keys(),
        ]);

        // 🔁 Redirect back to the table page
        return redirect()
            ->route('activities.essay.answers', $activity->id)
            ->with('success', 'Essay scores saved and student results updated.');
    }




public function updateScore(Request $request, Submission $submission)
{

    $data = $request->validate([
        'score' => 'required|integer|min:0|max:100',
    ]);

    $submission->update([
        'score' => $data['score'],
        'graded' => 'Graded',
    ]);

    return redirect()->route('activities.essay.view', $submission->activity_id)
    ->with('success', 'Score saved.');

}


public function show(Submission $submission)
{
    $submission->load('user', 'activity');

    return Inertia::render('Activities/ViewEssaySubmission', [
        'submission' => $submission,
    ]);
}
}
