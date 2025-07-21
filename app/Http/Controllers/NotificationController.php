<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $enrollments = $user->enrollments()->get(['year_level_id', 'section_id', 'subject_id']);

        if ($enrollments->isEmpty()) {
            return collect();
        }

        $filtered = $user->notifications->filter(function ($notification) use ($enrollments) {
            $data = $notification->data;

            if (!isset($data['year_level_id'])) {
                return false;
            }

            return $enrollments->contains(function ($enrollment) use ($data) {
                return
                    $enrollment->year_level_id == $data['year_level_id'] &&
                    (!isset($data['section_id']) || $enrollment->section_id == $data['section_id']) &&
                    (!isset($data['subject_id']) || $enrollment->subject_id == $data['subject_id']);
            });
        });

        return $filtered->values();
    }

    public function markAsRead(Request $request)
    {
        $notification = Auth::user()->notifications()->where('id', $request->id)->first();

        if ($notification && $notification->read_at === null) {
            $notification->markAsRead();
        }

        return response()->json(['message' => 'Notification marked as read.']);
    }
}
