<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Warning extends Model
{
    use HasFactory;

    protected $table = 'warnings';

    protected $fillable = [
        'warning_type',
        'sourceable_type',
        'sourceable_id',
        'severity',
        'status',
        'resolved_by',
        'resolved_at',
        'notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Relasi polimorfik ke objek sumber warning (e.g. Session, Rombel, dll).
     */
    public function sourceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke User yang menyelesaikan warning.
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
