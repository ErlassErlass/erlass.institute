<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetch notifications for admin/webmaster users (Milestones & Tickets).
     * Supports status=unread (default) or status=read.
     */
    public function getUnreadNotifications(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            return response()->json([
                'unread_count' => 0,
                'read_count' => 0,
                'ticket_count' => 0,
                'milestone_count' => 0,
                'notifications' => []
            ]);
        }

        $viewStatus = $request->input('status', 'unread'); // 'unread' | 'read'

        // Tiket aktif yang membutuhkan respon/tindakan admin
        $activeTicketsQuery = \App\Models\Ticket::with([
                'user:id,nama_lengkap',
                'session.ekstrakurikuler.sekolah:kodlan,namasekolah',
                'session.rombel.ekstrakurikuler.sekolah:kodlan,namasekolah'
            ])
            ->where(function ($q) {
                $q->where('status', \App\Models\Ticket::STATUS_OPEN)
                  ->orWhere('has_unread_reply_for_admin', true);
            });

        $ticketCount = (clone $activeTicketsQuery)->count();
        $systemUnreadCount = Notification::where('is_read', false)
            ->where(function ($q) use ($user) {
                $q->whereNull('target_roles')
                  ->orWhere('target_roles', 'like', "%{$user->role}%");
            })
            ->count();

        $unreadCount = $ticketCount + $systemUnreadCount;
        $readCount = Notification::where('is_read', true)->count();

        if ($viewStatus === 'read') {
            // Read system notifications
            $systemNotifications = Notification::where('is_read', true)
                ->where(function ($q) use ($user) {
                    $q->whereNull('target_roles')
                      ->orWhere('target_roles', 'like', "%{$user->role}%");
                })
                ->orderBy('read_at', 'desc')
                ->orderBy('updated_at', 'desc')
                ->take(30)
                ->get();

            // Selesai / tiket lama yang sudah ditanggapi
            $ticketNotifications = \App\Models\Ticket::with([
                    'user:id,nama_lengkap',
                    'session.ekstrakurikuler.sekolah:kodlan,namasekolah',
                    'session.rombel.ekstrakurikuler.sekolah:kodlan,namasekolah'
                ])
                ->where('status', '!=', \App\Models\Ticket::STATUS_OPEN)
                ->where('has_unread_reply_for_admin', false)
                ->orderBy('updated_at', 'desc')
                ->take(15)
                ->get()
                ->map(fn($ticket) => $this->formatTicketNotification($ticket, true));

            $notifications = $ticketNotifications->concat($systemNotifications)
                ->sortByDesc(fn($n) => is_array($n) ? ($n['updated_at'] ?? $n['created_at']) : ($n->read_at ?? $n->updated_at))
                ->values();
        } else {
            // Unread notifications
            $ticketNotifications = $activeTicketsQuery->orderBy('created_at', 'desc')
                ->take(25)
                ->get()
                ->map(fn($ticket) => $this->formatTicketNotification($ticket, false));

            $systemNotifications = Notification::where('is_read', false)
                ->where(function ($q) use ($user) {
                    $q->whereNull('target_roles')
                      ->orWhere('target_roles', 'like', "%{$user->role}%");
                })
                ->orderBy('created_at', 'desc')
                ->take(30)
                ->get();

            $notifications = $ticketNotifications->concat($systemNotifications)
                ->sortByDesc('created_at')
                ->values();
        }

        return response()->json([
            'status_view' => $viewStatus,
            'unread_count' => $unreadCount,
            'read_count' => $readCount,
            'ticket_count' => $ticketCount,
            'milestone_count' => $systemUnreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Helper to format ticket notifications consistently.
     */
    protected function formatTicketNotification($ticket, bool $isRead = false): array
    {
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
            'is_read' => $isRead,
            'created_at' => $ticket->updated_at ? $ticket->updated_at->toISOString() : $ticket->created_at->toISOString(),
            'updated_at' => $ticket->updated_at ? $ticket->updated_at->toISOString() : $ticket->created_at->toISOString(),
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
     * Mark single notification as unread (revert back to unread).
     */
    public function markAsUnread($notification): JsonResponse
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($notification instanceof Notification) {
            $notification->update([
                'is_read' => false,
                'read_at' => null,
            ]);
        } elseif (is_numeric($notification)) {
            $notif = Notification::find($notification);
            if ($notif) {
                $notif->update([
                    'is_read' => false,
                    'read_at' => null,
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

    /**
     * Admin Notification Center page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['webmaster', 'admin_sistem', 'admin', 'debug_user'])) {
            abort(403, 'Unauthorized');
        }

        $status = $request->input('status', 'all'); // 'all', 'unread', 'read'
        $type = $request->input('type', 'all');     // 'all', 'milestone', 'system'
        $search = $request->input('search');

        $query = Notification::query()->orderBy('created_at', 'desc');

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        if ($type === 'milestone') {
            $query->where('type', 'milestone_report');
        } elseif ($type === 'gateway' || $type === 'gateway_alert') {
            $query->where('type', 'gateway_alert');
        } elseif ($type !== 'all') {
            $query->where('type', $type);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('data', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate(25)->withQueryString();

        $totalCount = Notification::count();
        $unreadCount = Notification::where('is_read', false)->count();
        $readCount = Notification::where('is_read', true)->count();
        $milestoneCount = Notification::where('type', 'milestone_report')->count();
        $gatewayCount = Notification::where('type', 'gateway_alert')->count();

        return view('admin.notifications.index', compact(
            'notifications',
            'status',
            'type',
            'search',
            'totalCount',
            'unreadCount',
            'readCount',
            'milestoneCount',
            'gatewayCount'
        ));
    }
}
