<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Support\Str;

class TicketNotificationService
{
    /**
     * Trigger notification when a new ticket is created by an instructor.
     */
    public function notifyTicketCreated(Ticket $ticket): ?Notification
    {
        $user = $ticket->user ?? User::find($ticket->user_id);
        $instrukturNama = $user ? $user->nama_lengkap : 'Instruktur';
        $kategoriLabel = $ticket->kategori_label;
        $prioritasLabel = $ticket->prioritas_label;

        $prioritasIcon = match($ticket->prioritas) {
            Ticket::PRIORITAS_URGENT => '🔥 [URGENT]',
            Ticket::PRIORITAS_HIGH => '⚠️ [HIGH]',
            Ticket::PRIORITAS_MEDIUM => '📌 [NORMAL]',
            default => 'ℹ️ [LOW]',
        };

        $sekolahNama = $ticket->session?->rombel?->ekstrakurikuler?->sekolah?->namasekolah ?? null;
        $programNama = $ticket->session?->rombel?->ekstrakurikuler?->kategori_program ?? null;

        $title = "🎫 Tiket Baru: {$ticket->ticket_number} ({$kategoriLabel})";
        $message = "{$prioritasIcon} {$ticket->judul} — Diajukan oleh {$instrukturNama}" . ($sekolahNama ? " ({$sekolahNama})" : "");

        $dataPayload = [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'user_id' => $ticket->user_id,
            'instruktur_nama' => $instrukturNama,
            'kategori' => $ticket->kategori,
            'kategori_label' => $kategoriLabel,
            'prioritas' => $ticket->prioritas,
            'prioritas_label' => $prioritasLabel,
            'judul' => $ticket->judul,
            'deskripsi_snippet' => Str::limit($ticket->deskripsi, 100),
            'sekolah_nama' => $sekolahNama,
            'program_nama' => $programNama,
            'session_id' => $ticket->ekstrakurikuler_session_id,
            'ticket_url' => route('tickets.show', $ticket->id),
            'created_at_human' => now()->diffForHumans(),
        ];

        return Notification::create([
            'type' => 'ticket_created',
            'target_roles' => 'admin,webmaster,admin_sistem',
            'title' => $title,
            'message' => $message,
            'data' => $dataPayload,
            'is_read' => false,
        ]);
    }

    /**
     * Trigger notification when an instructor replies to a ticket.
     */
    public function notifyTicketReply(Ticket $ticket, TicketReply $reply): ?Notification
    {
        $user = $reply->user ?? User::find($reply->user_id);
        $instrukturNama = $user ? $user->nama_lengkap : 'Instruktur';
        
        $title = "💬 Balasan Tiket: {$ticket->ticket_number}";
        $message = "Instruktur {$instrukturNama} mengirim pesan baru pada tiket: \"{$ticket->judul}\"";

        $dataPayload = [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'user_id' => $reply->user_id,
            'instruktur_nama' => $instrukturNama,
            'pesan_snippet' => Str::limit($reply->pesan, 100),
            'judul' => $ticket->judul,
            'prioritas' => $ticket->prioritas,
            'prioritas_label' => $ticket->prioritas_label,
            'ticket_url' => route('tickets.show', $ticket->id),
            'created_at_human' => now()->diffForHumans(),
        ];

        return Notification::create([
            'type' => 'ticket_reply',
            'target_roles' => 'admin,webmaster,admin_sistem',
            'title' => $title,
            'message' => $message,
            'data' => $dataPayload,
            'is_read' => false,
        ]);
    }

    /**
     * Mark all notifications for a specific ticket as read.
     */
    public function markTicketNotificationsAsRead(int $ticketId): void
    {
        try {
            Notification::where('is_read', false)
                ->whereIn('type', ['ticket_created', 'ticket_reply'])
                ->where('data->ticket_id', $ticketId)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        } catch (\Throwable $e) {
            // Fallback for any json driver quirk
            Notification::where('is_read', false)
                ->whereIn('type', ['ticket_created', 'ticket_reply'])
                ->get()
                ->filter(function ($n) use ($ticketId) {
                    return isset($n->data['ticket_id']) && (int) $n->data['ticket_id'] === (int) $ticketId;
                })
                ->each(function ($n) {
                    $n->update(['is_read' => true, 'read_at' => now()]);
                });
        }
    }
}
