<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionConfirmation extends Model
{
    use HasFactory;

    protected $table = 'session_confirmations';

    protected $fillable = [
        'ekstrakurikuler_session_id',
        'user_id_instruktur',
        'status',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    /**
     * Relasi ke model EkstrakurikulerSession.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerSession::class, 'ekstrakurikuler_session_id');
    }

    /**
     * Relasi ke User instruktur terkait.
     */
    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_instruktur');
    }
}
