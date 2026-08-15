<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetchUnread()
    {
        $user = Auth::user();
        $notifications = $user->userNotifications()
            ->whereNull('read_at')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'count' => $user->userNotifications()->whereNull('read_at')->count(),
            'notifications' => $notifications
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->userNotifications()->findOrFail($id);
        $notification->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->userNotifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function index()
    {
        $notifications = Auth::user()->userNotifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }
}
