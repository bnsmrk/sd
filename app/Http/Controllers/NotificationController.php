<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        return Auth::user()->notifications;
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
