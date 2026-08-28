<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerSession;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);

        $query = Ticket::query()->forUser($user)->with(['user', 'assignedStaff', 'session']);

        // Filter Category
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search Query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('judul', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($subQ) use ($search) {
                      $subQ->where('nama_lengkap', 'like', "%{$search}%");
                  });
            });
        }

        // Quick Counts for Stat Cards
        $countsQuery = Ticket::query()->forUser($user);
        $totalCount = (clone $countsQuery)->count();
        $openCount = (clone $countsQuery)->where('status', Ticket::STATUS_OPEN)->count();
        $inProgressCount = (clone $countsQuery)->where('status', Ticket::STATUS_IN_PROGRESS)->count();
        $resolvedCount = (clone $countsQuery)->whereIn('status', [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])->count();

        $tickets = $query->latest('updated_at')->paginate(15)->withQueryString();

        return view('tickets.index', compact(
            'tickets',
            'isAdmin',
            'totalCount',
            'openCount',
            'inProgressCount',
            'resolvedCount'
        ));
    }

    /**
     * Show the form for creating a new ticket.
     */
    public function create()
    {
        $user = Auth::user();

        // Ambil sesi mengajar aktif / selesai terakhir yang diampu oleh instruktur ini
        $recentSessions = [];
        if ($user->role === 'instruktur') {
            $recentSessions = EkstrakurikulerSession::where(function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            })
            ->with(['rombel.ekstrakurikuler.sekolah'])
            ->orderBy('tanggal_terjadwal', 'desc')
            ->take(20)
            ->get();
        }

        return view('tickets.create', compact('recentSessions'));
    }

    /**
     * Store a newly created ticket in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|in:jadwal_honor,keluhan_lain,teknis_error',
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'prioritas' => 'nullable|in:low,medium,high,urgent',
            'ekstrakurikuler_session_id' => 'nullable|exists:ekstrakurikuler_session,id',
            'foto_lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ], [
            'kategori.required' => 'Pilih salah satu kategori tiket bantuan.',
            'judul.required' => 'Judul kendala wajib diisi.',
            'deskripsi.required' => 'Deskripsi kendala wajib dijelaskan.',
            'foto_lampiran.max' => 'Ukuran lampiran maksimal 5MB.',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('foto_lampiran')) {
            $lampiranPath = $request->file('foto_lampiran')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'ticket_number' => Ticket::generateTicketNumber(),
            'user_id' => Auth::id(),
            'kategori' => $request->kategori,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'prioritas' => $request->prioritas ?? Ticket::PRIORITAS_MEDIUM,
            'status' => Ticket::STATUS_OPEN,
            'ekstrakurikuler_session_id' => $request->ekstrakurikuler_session_id,
            'foto_lampiran' => $lampiranPath,
            'has_unread_reply_for_user' => false,
            'has_unread_reply_for_admin' => true,
        ]);

        // Dispatch instant notification for Admin / QC Team
        app(\App\Services\TicketNotificationService::class)->notifyTicketCreated($ticket);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', "Tiket {$ticket->ticket_number} berhasil dibuat. Tim Operasional/QC akan segera menindaklanjuti.");
    }

    /**
     * Display the specified ticket with conversation thread.
     */
    public function show($id)
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);

        $ticket = Ticket::with([
            'user',
            'assignedStaff',
            'session.rombel.ekstrakurikuler.sekolah',
            'replies.user'
        ])->findOrFail($id);

        // Security Authorization: Only owner or admin can view
        if (!$isAdmin && $ticket->user_id !== $user->id) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat tiket ini.');
        }

        // Mark unread status and notifications
        if ($isAdmin) {
            $ticket->update(['has_unread_reply_for_admin' => false]);
            app(\App\Services\TicketNotificationService::class)->markTicketNotificationsAsRead($ticket->id);
        } else {
            $ticket->update(['has_unread_reply_for_user' => false]);
        }

        // Available staff for assignment (Admins/Webmasters)
        $staffMembers = $isAdmin ? User::whereIn('role', ['webmaster', 'admin_sistem', 'admin'])->get() : collect();

        return view('tickets.show', compact('ticket', 'isAdmin', 'staffMembers'));
    }

    /**
     * Post a reply to the ticket thread.
     */
    public function reply(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);

        $ticket = Ticket::findOrFail($id);

        if (!$isAdmin && $ticket->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'pesan' => 'required|string',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120',
        ], [
            'pesan.required' => 'Pesan balasan tidak boleh kosong.',
            'lampiran.max' => 'Ukuran lampiran maksimal 5MB.',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('tickets/replies', 'public');
        }

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'pesan' => $request->pesan,
            'lampiran' => $lampiranPath,
            'is_staff_reply' => $isAdmin,
        ]);

        // Update ticket state & unread notifications
        if ($isAdmin) {
            $newStatus = ($ticket->status === Ticket::STATUS_OPEN) ? Ticket::STATUS_IN_PROGRESS : $ticket->status;
            $ticket->update([
                'status' => $newStatus,
                'has_unread_reply_for_user' => true,
                'has_unread_reply_for_admin' => false,
            ]);
            app(\App\Services\TicketNotificationService::class)->markTicketNotificationsAsRead($ticket->id);
        } else {
            $newStatus = ($ticket->status === Ticket::STATUS_RESOLVED) ? Ticket::STATUS_IN_PROGRESS : $ticket->status;
            $ticket->update([
                'status' => $newStatus,
                'has_unread_reply_for_admin' => true,
                'has_unread_reply_for_user' => false,
            ]);
            app(\App\Services\TicketNotificationService::class)->notifyTicketReply($ticket, $reply);
        }

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', 'Balasan berhasil dikirim.');
    }

    /**
     * Update ticket status and assignment (Admin/QC only).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Hanya Admin/QC yang dapat mengubah status tiket.');
        }

        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
            'prioritas' => 'nullable|in:low,medium,high,urgent',
        ]);

        $updateData = [
            'status' => $request->status,
        ];

        if ($request->has('assigned_to')) {
            $updateData['assigned_to'] = $request->assigned_to ?: null;
        }

        if ($request->has('prioritas')) {
            $updateData['prioritas'] = $request->prioritas;
        }

        if ($request->status === Ticket::STATUS_RESOLVED && !$ticket->resolved_at) {
            $updateData['resolved_at'] = now();
        }

        if ($request->status === Ticket::STATUS_CLOSED && !$ticket->closed_at) {
            $updateData['closed_at'] = now();
        }

        $ticket->update($updateData);

        return redirect()->route('tickets.show', $ticket->id)
            ->with('success', "Status tiket berhasil diperbarui menjadi {$ticket->status_label}.");
    }
}
