<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Activity;
use App\Models\Submission;
use Illuminate\Http\Request;
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

    // Save uploaded file
    if ($request->hasFile('file')) {
        $data['file_path'] = $request->file('file')->store('essay_submissions', 'public');
    }

    Submission::create($data);

    return redirect()->route('dashboard')->with('success', 'Essay submitted successfully!');
}

public function showEssaySubmissions(Request $request, Activity $activity)
{
    $search = $request->input('search');

    $submissions = Submission::with('user')
        ->where('activity_id', $activity->id)
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        })
        ->latest()
        ->paginate(10)
        ->withQueryString(); // Keeps search query when paginating

    return Inertia::render('Activities/EssaySubmissions', [
        'activity' => $activity,
        'submissions' => $submissions,
        'filters' => [
            'search' => $search,
        ],
    ]);
}


public function updateScore(Request $request, Submission $submission)
{
        Log::info('Updating score for submission ID: ' . $submission->id);

    $data = $request->validate([
        'score' => 'required|integer|min:0|max:100',
    ]);

    $submission->update([
        'score' => $data['score'],
        'graded' => 'Graded',
    ]);
    Log::info('Updated with score: ' . $data['score']);

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
