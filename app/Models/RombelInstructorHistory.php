<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RombelInstructorHistory extends Model
{
    protected $table = 'rombel_instructor_history';

    protected $fillable = [
        'rombel_id',
        'user_id_instruktur',
        'user_id_asisten',
        'berlaku_dari_sesi',
        'berlaku_sampai_sesi',
        'alasan',
        'diganti_oleh',
    ];

    protected $casts = [
        'berlaku_dari_sesi'   => 'integer',
        'berlaku_sampai_sesi' => 'integer',
    ];

    // ─────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerRombel::class, 'rombel_id');
    }

    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_instruktur');
    }

    public function asisten(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_asisten');
    }

    public function penggantiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diganti_oleh');
    }

    // ─────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────

    /**
     * Scope: get all history records for a given rombel.
     */
    public function scopeForRombel($query, int $rombelId)
    {
        return $query->where('rombel_id', $rombelId);
    }

    /**
     * Scope: check if a user was EVER an instructor or assistant in this rombel.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id_instruktur', $userId)
              ->orWhere('user_id_asisten', $userId);
        });
    }

    // ─────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────

    /**
     * Returns true if this record represents the currently active assignment
     * (i.e. no end session defined yet).
     */
    public function isActive(): bool
    {
        return is_null($this->berlaku_sampai_sesi);
    }

    /**
     * Static helper: record a new instructor assignment for a rombel.
     * Automatically closes the previous open record.
     *
     * @param int       $rombelId         ID rombel yang berganti instruktur
     * @param int       $newInstruktorId  ID instruktur baru
     * @param int|null  $newAsitenId      ID asisten baru (opsional)
     * @param int       $fromSesi         Pertemuan ke berapa instruktur baru mulai aktif
     * @param int       $previousEndSesi  Pertemuan terakhir instruktur lama (= fromSesi - 1)
     * @param string|null $alasan         Alasan pergantian
     * @param int|null  $digantiOleh      ID admin yang mengganti
     */
    public static function recordChange(
        int $rombelId,
        int $newInstruktorId,
        ?int $newAsitenId,
        int $fromSesi,
        int $previousEndSesi,
        ?string $alasan = null,
        ?int $digantiOleh = null
    ): void {
        // Close the currently open record for this rombel (if any)
        static::where('rombel_id', $rombelId)
            ->whereNull('berlaku_sampai_sesi')
            ->update(['berlaku_sampai_sesi' => $previousEndSesi]);

        // Open a new record for the new instructor
        static::create([
            'rombel_id'           => $rombelId,
            'user_id_instruktur'  => $newInstruktorId,
            'user_id_asisten'     => $newAsitenId,
            'berlaku_dari_sesi'   => $fromSesi,
            'berlaku_sampai_sesi' => null, // null = still active
            'alasan'              => $alasan,
            'diganti_oleh'        => $digantiOleh,
        ]);
    }

    /**
     * Static helper: check if a user was EVER an instructor or assistant in a rombel.
     */
    public static function wasEverInstructor(int $rombelId, int $userId): bool
    {
        return static::where('rombel_id', $rombelId)
            ->where(function ($q) use ($userId) {
                $q->where('user_id_instruktur', $userId)
                  ->orWhere('user_id_asisten', $userId);
            })
            ->exists();
    }
}
