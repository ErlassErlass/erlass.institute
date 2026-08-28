<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'user_id',
        'kategori',
        'judul',
        'deskripsi',
        'prioritas',
        'status',
        'ekstrakurikuler_session_id',
        'foto_lampiran',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'has_unread_reply_for_user',
        'has_unread_reply_for_admin',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'has_unread_reply_for_user' => 'boolean',
        'has_unread_reply_for_admin' => 'boolean',
    ];

    const KATEGORI_JADWAL_HONOR = 'jadwal_honor';
    const KATEGORI_KELUHAN_LAIN = 'keluhan_lain';
    const KATEGORI_TEKNIS_ERROR = 'teknis_error';

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    const PRIORITAS_LOW = 'low';
    const PRIORITAS_MEDIUM = 'medium';
    const PRIORITAS_HIGH = 'high';
    const PRIORITAS_URGENT = 'urgent';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerSession::class, 'ekstrakurikuler_session_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->oldest();
    }

    /**
     * Get human-readable category label.
     */
    public function getKategoriLabelAttribute(): string
    {
        return match ($this->kategori) {
            self::KATEGORI_JADWAL_HONOR => 'Jadwal / Honor',
            self::KATEGORI_KELUHAN_LAIN => 'Keluhan Lain',
            self::KATEGORI_TEKNIS_ERROR => 'Teknis / Error',
            default => 'Umum',
        };
    }

    /**
     * Get badge styling class for category.
     */
    public function getKategoriBadgeAttribute(): string
    {
        return match ($this->kategori) {
            self::KATEGORI_JADWAL_HONOR => 'bg-warning text-dark',
            self::KATEGORI_KELUHAN_LAIN => 'bg-info text-dark',
            self::KATEGORI_TEKNIS_ERROR => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Get badge styling class for status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'bg-primary text-white',
            self::STATUS_IN_PROGRESS => 'bg-warning text-dark',
            self::STATUS_RESOLVED => 'bg-success text-white',
            self::STATUS_CLOSED => 'bg-secondary text-white',
            default => 'bg-light text-dark border',
        };
    }

    /**
     * Get human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OPEN => 'Menunggu Respon',
            self::STATUS_IN_PROGRESS => 'Sedang Diproses',
            self::STATUS_RESOLVED => 'Selesai Dijawab',
            self::STATUS_CLOSED => 'Ditutup',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get badge styling class for priority.
     */
    public function getPrioritasBadgeAttribute(): string
    {
        return match ($this->prioritas) {
            self::PRIORITAS_URGENT => 'text-danger fw-bold',
            self::PRIORITAS_HIGH => 'text-warning fw-bold',
            self::PRIORITAS_MEDIUM => 'text-primary',
            default => 'text-muted',
        };
    }

    /**
     * Get human-readable priority label.
     */
    public function getPrioritasLabelAttribute(): string
    {
        return match ($this->prioritas) {
            self::PRIORITAS_URGENT => 'Mendesak (Urgent)',
            self::PRIORITAS_HIGH => 'Tinggi (High)',
            self::PRIORITAS_MEDIUM => 'Sedang (Medium)',
            self::PRIORITAS_LOW => 'Rendah (Low)',
            default => ucfirst($this->prioritas ?? 'Medium'),
        };
    }

    /**
     * Scope for role access.
     */
    public function scopeForUser($query, $user)
    {
        if (in_array($user->role, ['webmaster', 'admin_sistem', 'admin'])) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    /**
     * Generate next ticket number.
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TCK-' . date('Ym') . '-';
        $lastTicket = self::withTrashed()
            ->where('ticket_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->ticket_number, -4);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }
}
