<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Salesman;

class InvoiceApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'invoice_approvals';

    const STATUS_DRAFT               = 'draft';
    const STATUS_PENDING_OPERASIONAL = 'pending_operasional';
    const STATUS_PENDING_AKUNTING    = 'pending_akunting';
    const STATUS_APPROVED            = 'approved';
    const STATUS_REJECTED            = 'rejected';

    protected $fillable = [
        'sekolah_kodlan',
        'ekstrakurikuler_id',
        'kategori_program',
        'ekstrakurikuler_rombel_id',
        'skema_tagihan',
        'periode_label',
        'tahun_ajaran',
        'periode_nomor',
        'sesi_dari',
        'sesi_sampai',
        'total_rombel',
        'jumlah_siswa_billable',
        'koreksi_siswa_billable',
        'koreksi_catatan',
        'koreksi_by',
        'koreksi_at',
        'jumlah_sesi',
        'nomor_invoice',
        'status',
        'operasional_user_id',
        'operasional_status',
        'operasional_approved_at',
        'operasional_catatan',
        'is_konfirmasi_pic',
        'pic_konfirmasi_nama',
        'pic_konfirmasi_jabatan',
        'pic_konfirmasi_tgl',
        'pic_konfirmasi_catatan',
        'operasional_checklist',
        'bukti_chat_path',
        'siswa_gratis_list',
        'jumlah_siswa_gratis',
        'sesi_verifikasi_data',
        'akunting_user_id',
        'akunting_status',
        'akunting_approved_at',
        'akunting_catatan',
        'is_invoice_tercetak',
        'akunting_checklist',
        'serah_terima_akunting_at',
        'serah_terima_akunting_penerima',
        'serah_terima_akunting_catatan',
        'pdf_generated_at',
        'pdf_path',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'operasional_approved_at'  => 'datetime',
        'akunting_approved_at'     => 'datetime',
        'pdf_generated_at'         => 'datetime',
        'koreksi_at'               => 'datetime',
        'pic_konfirmasi_tgl'       => 'datetime',
        'serah_terima_akunting_at' => 'datetime',
        'is_konfirmasi_pic'        => 'boolean',
        'is_invoice_tercetak'      => 'boolean',
        'operasional_checklist'    => 'array',
        'akunting_checklist'       => 'array',
        'siswa_gratis_list'        => 'array',
        'sesi_verifikasi_data'     => 'array',
        'jumlah_siswa_billable'    => 'integer',
        'koreksi_siswa_billable'   => 'integer',
        'jumlah_siswa_gratis'      => 'integer',
        'total_rombel'             => 'integer',
        'jumlah_sesi'              => 'integer',
        'periode_nomor'            => 'integer',
        'sesi_dari'                => 'integer',
        'sesi_sampai'              => 'integer',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Accessor: Billable Efektif (koreksi override sistem jika ada)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Jumlah siswa billable yang dipakai di invoice.
     * Jika ada item rombel, total diambil dari sum siswa efektif seluruh item rombel.
     */
    public function getBillableEfektifAttribute(): int
    {
        if ($this->koreksi_siswa_billable !== null) {
            return (int) $this->koreksi_siswa_billable;
        }

        if ($this->relationLoaded('items') && $this->items->isNotEmpty()) {
            return (int) $this->items->sum('billable_efektif');
        }

        $base = (int) ($this->jumlah_siswa_billable ?? 0);
        $gratis = (int) ($this->jumlah_siswa_gratis ?? 0);
        return max(0, $base - $gratis);
    }

    /**
     * Apakah invoice ini memiliki koreksi manual yang aktif?
     */
    public function hasKoreksi(): bool
    {
        if (!is_null($this->koreksi_siswa_billable)) {
            return true;
        }

        if ($this->relationLoaded('items')) {
            return $this->items->contains(fn($it) => $it->hasKoreksi());
        }

        return false;
    }

    /**
     * Nama program ekskul / pelatihan untuk invoice ini.
     */
    public function getProgramNamaAttribute(): string
    {
        if (!empty($this->attributes['kategori_program'] ?? null)) {
            return $this->attributes['kategori_program'];
        }

        if ($this->ekstrakurikuler?->kategori_program) {
            return $this->ekstrakurikuler->kategori_program;
        }

        if ($this->rombel?->ekstrakurikuler?->kategori_program) {
            return $this->rombel->ekstrakurikuler->kategori_program;
        }

        if ($this->relationLoaded('items') && $this->items->isNotEmpty()) {
            return $this->items->first()->rombel?->ekstrakurikuler?->kategori_program ?? 'Program Ekskul';
        }

        return 'Program Ekskul';
    }

    /**
     * Nama PIC selalu dinamis diambil dari master Program Ekskul (Single Source of Truth).
     */
    public function getPicNamaAttribute(): string
    {
        if ($this->skema_tagihan === Sekolah::SKEMA_CSR_REGULER_SOGA) {
            return !empty($this->attributes['pic_konfirmasi_nama'])
                ? $this->attributes['pic_konfirmasi_nama']
                : 'CSR SOGA (Solidaritas Erlangga)';
        }

        $pjProgram = $this->ekstrakurikuler?->penanggung_jawab
            ?? $this->rombel?->ekstrakurikuler?->penanggung_jawab
            ?? ($this->relationLoaded('items') && $this->items->isNotEmpty() ? $this->items->first()->rombel?->ekstrakurikuler?->penanggung_jawab : null);

        if (!empty($pjProgram)) {
            return $pjProgram;
        }

        return $this->attributes['pic_konfirmasi_nama'] ?? '';
    }

    /**
     * Jabatan / Peran PIC selalu 'Penanggung Jawab Ekstrakurikuler' sesuai input program ekskul,
     * atau 'Pihak Penanggung Dana CSR' untuk skema CSR SOGA.
     */
    public function getPicJabatanAttribute(): string
    {
        if ($this->skema_tagihan === Sekolah::SKEMA_CSR_REGULER_SOGA) {
            return !empty($this->attributes['pic_konfirmasi_jabatan'])
                ? $this->attributes['pic_konfirmasi_jabatan']
                : 'Pihak Penanggung Dana CSR';
        }

        return 'Penanggung Jawab Ekstrakurikuler';
    }

    /**
     * Salesman / Tim Marketing yang bertanggung jawab atas sekolah atau kegiatan invoice ini.
     */
    public function getSalesAttribute(): ?Salesman
    {
        return $this->rombel?->ekstrakurikuler?->sales
            ?? $this->ekstrakurikuler?->sales
            ?? ($this->relationLoaded('items') && $this->items->isNotEmpty() ? $this->items->first()?->rombel?->ekstrakurikuler?->sales : null);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Relations
    // ─────────────────────────────────────────────────────────────────────────

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceApprovalItem::class, 'invoice_approval_id');
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerRombel::class, 'ekstrakurikuler_rombel_id');
    }

    public function operasionalUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operasional_user_id');
    }

    public function akuntingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'akunting_user_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function koreksiByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'koreksi_by');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────────────────────────────────

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePendingOperasional($query)
    {
        return $query->where('status', 'pending_operasional');
    }

    public function scopePendingAkunting($query)
    {
        return $query->where('status', 'pending_akunting');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeTahunAjaran($query, string $tahunAjaran)
    {
        return $query->where('tahun_ajaran', $tahunAjaran);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Apakah invoice ini sudah fully approved dan siap PDF.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved'
            && $this->operasional_status === 'approved'
            && $this->akunting_status === 'approved';
    }

    /**
     * Apakah PDF sudah pernah digenerate.
     */
    public function hasPdf(): bool
    {
        return !is_null($this->pdf_generated_at);
    }

    /**
     * Apakah invoice ini sedang membutuhkan revisi karena dikembalikan oleh Akunting.
     */
    public function isNeedsRevision(): bool
    {
        return $this->status === self::STATUS_PENDING_OPERASIONAL && $this->akunting_status === 'rejected';
    }

    /**
     * Label status yang readable untuk UI.
     */
    public function statusLabel(): string
    {
        if ($this->isNeedsRevision()) {
            return 'Perlu Revisi Produksi';
        }

        return match ($this->status) {
            'draft'                => 'Draft',
            'pending_operasional'  => 'Menunggu Admin Produksi',
            'pending_akunting'     => 'Menunggu Staff Akunting',
            'approved'             => 'Disetujui Resmi',
            'rejected'             => 'Ditolak',
            default                => ucfirst($this->status),
        };
    }

    /**
     * Warna badge status untuk UI.
     */
    public function statusBadgeClass(): string
    {
        if ($this->isNeedsRevision()) {
            return 'danger';
        }

        return match ($this->status) {
            'draft'                => 'secondary',
            'pending_operasional'  => 'warning text-dark',
            'pending_akunting'     => 'primary',
            'approved'             => 'success',
            'rejected'             => 'danger',
            default                => 'secondary',
        };
    }

    /**
     * Generate nomor invoice otomatis.
     * Saat draft: DRAFT/ERLASS/YYYYMM/KODLAN/NNN (tanpa kata INV)
     * Saat approved: INV/ERLASS/YYYYMM/KODLAN/NNN
     */
    public static function generateNomorInvoice(string $kodlan, string $periodeLabel, bool $isDraft = true): string
    {
        $yearMonth   = now()->format('Ym');
        $cleanKodlan = strtoupper($kodlan);
        $suffix      = "ERLASS/{$yearMonth}/{$cleanKodlan}";

        $lastNumber = self::where(function ($q) use ($suffix) {
            $q->where('nomor_invoice', 'like', "INV/{$suffix}/%")
              ->orWhere('nomor_invoice', 'like', "DRAFT/{$suffix}/%")
              ->orWhere('nomor_invoice', 'like', "DRAFT-INV/{$suffix}/%");
        })->count();

        $seq = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

        return $isDraft ? "DRAFT/{$suffix}/{$seq}" : "INV/{$suffix}/{$seq}";
    }

    /**
     * Finalisasi nomor invoice dari DRAFT menjadi nomor resmi saat disetujui Akunting.
     * Contoh: DRAFT/ERLASS/... -> INV/ERLASS/...
     */
    public function finalizeNomorInvoice(): void
    {
        if (str_starts_with($this->nomor_invoice ?? '', 'DRAFT/')) {
            $this->nomor_invoice = 'INV/' . substr($this->nomor_invoice, 6);
            $this->save();
        } elseif (str_starts_with($this->nomor_invoice ?? '', 'DRAFT-INV/')) {
            $this->nomor_invoice = substr($this->nomor_invoice, 6);
            $this->save();
        } elseif (str_starts_with($this->nomor_invoice ?? '', 'DRAFT-')) {
            $this->nomor_invoice = substr($this->nomor_invoice, 6);
            $this->save();
        }
    }

    /**
     * Teks wajib catatan kontrak yang tampil di setiap PDF.
     */
    public static function catatanKontrakText(): string
    {
        return "⚠️ CATATAN KOMITMEN KONTRAK (TIDAK BOLEH ADA PEMBATALAN):\n"
            . "Seluruh sesi pembelajaran yang telah dijadwalkan mengikat alokasi penugasan instruktur "
            . "dan sarana belajar Erlass Prokreatif Indonesia. Sesi pembelajaran TIDAK DAPAT DIBATALKAN "
            . "secara sepihak untuk pengurangan biaya tagihan. Apabila terdapat kendala operasional "
            . "internal sekolah (seperti kegiatan porseni, ujian sekolah, atau libur insidental), "
            . "pertemuan wajib dialihkan ke tanggal pengganti melalui prosedur Reschedule resmi.";
    }
}
