<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Salesman;

class InvoiceApprovalItem extends Model
{
    use HasFactory;

    protected $table = 'invoice_approval_items';

    protected $fillable = [
        'invoice_approval_id',
        'ekstrakurikuler_rombel_id',
        'sesi_dari',
        'sesi_sampai',
        'jumlah_sesi',
        'jumlah_siswa_billable',
        'koreksi_siswa_billable',
        'koreksi_catatan',
        'koreksi_by',
        'koreksi_at',
        'siswa_gratis_list',
        'jumlah_siswa_gratis',
    ];

    protected $casts = [
        'sesi_dari'              => 'integer',
        'sesi_sampai'            => 'integer',
        'jumlah_sesi'            => 'integer',
        'jumlah_siswa_billable'  => 'integer',
        'koreksi_siswa_billable' => 'integer',
        'jumlah_siswa_gratis'    => 'integer',
        'siswa_gratis_list'      => 'array',
        'koreksi_at'             => 'datetime',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InvoiceApproval::class, 'invoice_approval_id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerRombel::class, 'ekstrakurikuler_rombel_id');
    }

    public function koreksiUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'koreksi_by');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Accessors & Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Jumlah siswa billable yang berlaku (jika ada koreksi manual, pakai koreksi).
     */
    public function getBillableEfektifAttribute(): int
    {
        if ($this->koreksi_siswa_billable !== null) {
            return (int) $this->koreksi_siswa_billable;
        }

        $base = (int) ($this->jumlah_siswa_billable ?? 0);
        $gratis = (int) ($this->jumlah_siswa_gratis ?? 0);
        return max(0, $base - $gratis);
    }

    /**
     * Apakah item rombel ini memiliki koreksi manual.
     */
    public function hasKoreksi(): bool
    {
        return $this->koreksi_siswa_billable !== null;
    }

    /**
     * Salesman terkait rombel ini.
     */
    public function getSalesAttribute(): ?Salesman
    {
        return $this->rombel?->ekstrakurikuler?->sales
            ?? $this->invoice?->ekstrakurikuler?->sales
            ?? null;
    }
}
