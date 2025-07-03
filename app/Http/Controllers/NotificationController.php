<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        return Auth::user()->unreadNotifications;
    }

    public function markAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'Notifications marked as read.']);
    }
}
