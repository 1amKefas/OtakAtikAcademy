<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a single notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Ensure the logged-in user owns this notification
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Get all notifications for the logged-in user
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('notifications.index', [
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Get count of unread notifications (for API/AJAX)
     */
    public function unreadCount()
    {
        $count = Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $count
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        $notification->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }
}
