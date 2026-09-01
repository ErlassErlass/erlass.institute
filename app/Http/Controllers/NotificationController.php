<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetch unread notifications for admin/webmaster users (Milestones & Tickets).
     */
    public function getUnreadNotifications(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            return response()->json([
                'unread_count' => 0,
                'ticket_count' => 0,
                'milestone_count' => 0,
                'notifications' => []
            ]);
        }

        // Tiket aktif yang membutuhkan respon/tindakan admin (status OPEN atau ada balasan baru)
        $activeTickets = \App\Models\Ticket::with([
                'user:id,nama_lengkap',
                'session.ekstrakurikuler.sekolah:kodlan,namasekolah',
                'session.rombel.ekstrakurikuler.sekolah:kodlan,namasekolah'
            ])
            ->where(function ($q) {
                $q->where('status', \App\Models\Ticket::STATUS_OPEN)
                  ->orWhere('has_unread_reply_for_admin', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $ticketCount = $activeTickets->count();

        $milestoneCount = Notification::where('is_read', false)
            ->where('type', 'milestone_report')
            ->count();

        $unreadCount = $ticketCount + $milestoneCount;

        // Petakan tiket aktif agar selalu tampil pada tab Tiket & Semua
        $ticketNotifications = $activeTickets->take(25)->map(function ($ticket) {
            $schoolName = $ticket->session?->ekstrakurikuler?->sekolah?->namasekolah 
                ?? ($ticket->session?->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? null);

            $prioritasLabel = match ($ticket->prioritas) {
                'urgent' => 'URGENT',
                'high' => 'TINGGI',
                'low' => 'RENDAH',
                default => 'NORMAL'
            };

            $kategoriLabel = match ($ticket->kategori) {
                'jadwal_honor' => 'Jadwal / Honor',
                'teknis_error' => 'Teknis / Error',
                default => 'Keluhan Lain'
            };

            return [
                'id' => 'ticket-' . $ticket->id,
                'type' => $ticket->has_unread_reply_for_admin ? 'ticket_reply' : 'ticket_created',
                'title' => '🎫 Tiket ' . ($ticket->has_unread_reply_for_admin ? 'Dibalas' : 'Baru') . ': ' . $ticket->ticket_number . ' (' . $kategoriLabel . ')',
                'is_read' => false,
                'created_at' => $ticket->updated_at ? $ticket->updated_at->toISOString() : $ticket->created_at->toISOString(),
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'judul' => $ticket->judul,
                    'deskripsi_snippet' => \Illuminate\Support\Str::limit($ticket->deskripsi, 95),
                    'prioritas' => $ticket->prioritas,
                    'prioritas_label' => $prioritasLabel,
                    'kategori_label' => $kategoriLabel,
                    'instruktur_nama' => $ticket->user?->nama_lengkap ?? 'Instruktur',
                    'sekolah_nama' => $schoolName,
                    'ticket_url' => route('tickets.show', $ticket->id),
                ]
            ];
        });

        // Ambil notifikasi milestone yang belum dibaca
        $milestoneNotifications = Notification::where('is_read', false)
            ->where('type', 'milestone_report')
            ->orderBy('created_at', 'desc')
            ->take(25)
            ->get();

        $notifications = $ticketNotifications->concat($milestoneNotifications)
            ->sortByDesc('created_at')
            ->values();

        return response()->json([
            'unread_count' => $unreadCount,
            'ticket_count' => $ticketCount,
            'milestone_count' => $milestoneCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark single notification as read.
     */
    public function markAsRead($notification): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($notification instanceof Notification) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        } elseif (is_numeric($notification)) {
            $notif = Notification::find($notification);
            if ($notif) {
                $notif->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            }
        }

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

        \App\Models\Ticket::where('has_unread_reply_for_admin', true)->update([
            'has_unread_reply_for_admin' => false,
        ]);

        return response()->json(['success' => true]);
    }
}
