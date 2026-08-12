<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetch unread milestone notifications for admin/webmaster users.
     */
    public function getUnreadNotifications(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $query = Notification::where('is_read', false)
            ->orderBy('created_at', 'desc');

        $unreadCount = $query->count();
        $notifications = $query->take(15)->get();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead(Notification $notification): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        Notification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }
}
