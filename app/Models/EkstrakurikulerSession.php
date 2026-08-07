<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EkstrakurikulerSession extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'ekstrakurikuler_session';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'ekstrakurikuler_id',
        'ekstrakurikuler_rombel_id',
        'nomor_pertemuan',
        'tanggal_terjadwal',
        'jam_mulai_terjadwal',
        'jam_selesai_terjadwal',
        'tanggal_pelaksanaan',
        'jam_mulai_aktual',
        'jam_selesai_aktual',
        'status',
        'user_id_instruktur',
        'user_id_asisten',
        'topik_materi',
        'deskripsi_kegiatan',
        'catatan',
        'alasan_pembatalan',
        'tanggal_pengganti',
        'created_by',
        'updated_by',
        'payment_status',
        'payroll_item_id',
        'actual_checkin_status',
        'actual_checkin_penalty',
        'calculated_fee',
        'override_fee',
        'transport_fee',
        'reminder_h1_sent_at',
        'reminder_h0_sent_at',
    ];

    /**
     * Casting atribut ke tipe data yang sesuai.
     */
    protected $casts = [
        'tanggal_terjadwal' => 'date',
        'tanggal_pelaksanaan' => 'date',
        'tanggal_pengganti' => 'date',
        'jam_mulai_terjadwal' => 'datetime:H:i',
        'jam_selesai_terjadwal' => 'datetime:H:i',
        'jam_mulai_aktual' => 'datetime:H:i',
        'jam_selesai_aktual' => 'datetime:H:i',
        'nomor_pertemuan' => 'integer',
        'transport_fee' => 'decimal:2',
        'reminder_h1_sent_at' => 'datetime',
        'reminder_h0_sent_at' => 'datetime',
    ];

    /**
     * Konstanta untuk status session
     */
    const STATUS_TERJADWAL = 'terjadwal';

    const STATUS_BERLANGSUNG = 'berlangsung';

    const STATUS_SELESAI = 'selesai';

    const STATUS_DIBATALKAN = 'dibatalkan';

    const STATUS_DITUNDA = 'ditunda';

    const STATUS_TIDAK_HADIR = 'tidak_hadir';

    const STATUS_LIBUR = 'libur';       // Sesi libur karena hari libur nasional/sekolah

    const STATUS_DIGANTI = 'diganti';   // Sesi sudah diganti ke jadwal baru

    /**
     * Relasi ke model Ekstrakurikuler.
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    /**
     * Relasi ke model EkstrakurikulerRombel.
     */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerRombel::class, 'ekstrakurikuler_rombel_id');
    }

    /**
     * Relasi ke User untuk instruktur.
     */
    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_instruktur');
    }

    /**
     * Relasi ke User untuk asisten.
     */
    public function asisten(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_asisten');
    }

    /**
     * Relasi ke PayrollItem.
     */
    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class, 'payroll_item_id');
    }

    /**
     * Relasi ke LaporanMengajar jika sudah terintegrasi.
     */
    public function lateReportRequests()
    {
        return $this->hasMany(LateReportRequest::class, "session_id");
    }

    public function latestApprovedLateReportRequest()
    {
        return $this->hasOne(LateReportRequest::class, "session_id")
            ->where("status", "approved")
            ->latest();
    }

    public function laporanMengajar()
    {
        return $this->hasOne(LaporanMengajar::class, 'ekstrakurikuler_session_id');
    }

    /**
     * Relasi ke User yang membuat record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang mengupdate record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk filter berdasarkan status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter session yang terjadwal hari ini.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_terjadwal', today());
    }

    /**
     * Scope untuk filter session dalam rentang tanggal.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_terjadwal', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter berdasarkan instruktur.
     */
    public function scopeByInstruktur($query, $userId)
    {
        return $query->where('user_id_instruktur', $userId);
    }

    /**
     * Scope untuk filter session yang sudah selesai.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_SELESAI);
    }

    /**
     * Scope untuk filter session yang belum dilaksanakan.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_TERJADWAL);
    }

    /**
     * Accessor untuk mendapatkan label status.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_TERJADWAL   => 'Terjadwal',
            self::STATUS_BERLANGSUNG => 'Berlangsung',
            self::STATUS_SELESAI     => 'Selesai',
            self::STATUS_DIBATALKAN  => 'Dibatalkan',
            self::STATUS_DITUNDA     => 'Ditunda',
            self::STATUS_TIDAK_HADIR => 'Tidak Hadir',
            self::STATUS_LIBUR       => 'Libur',
            self::STATUS_DIGANTI     => 'Diganti',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Accessor untuk mendapatkan waktu terjadwal formatted.
     */
    public function getJadwalWaktuAttribute(): string
    {
        return $this->jam_mulai_terjadwal->format('H:i').' - '.$this->jam_selesai_terjadwal->format('H:i');
    }

    /**
     * Accessor untuk mendapatkan waktu aktual formatted.
     */
    public function getWaktuAktualAttribute(): ?string
    {
        if (! $this->jam_mulai_aktual || ! $this->jam_selesai_aktual) {
            return null;
        }

        return $this->jam_mulai_aktual->format('H:i').' - '.$this->jam_selesai_aktual->format('H:i');
    }

    /**
     * Accessor untuk mendapatkan durasi terjadwal dalam menit.
     */
    public function getDurasiTerjadwalAttribute(): int
    {
        return $this->jam_mulai_terjadwal->diffInMinutes($this->jam_selesai_terjadwal);
    }

    /**
     * Accessor untuk mendapatkan durasi aktual dalam menit.
     */
    public function getDurasiAktualAttribute(): ?int
    {
        if (! $this->jam_mulai_aktual || ! $this->jam_selesai_aktual) {
            return null;
        }

        return $this->jam_mulai_aktual->diffInMinutes($this->jam_selesai_aktual);
    }

    /**
     * Accessor untuk mendapatkan objek Carbon waktu mulai lengkap (tanggal + jam_mulai_terjadwal).
     */
    public function getWaktuMulaiFullAttribute(): ?Carbon
    {
        if (!$this->tanggal_terjadwal) {
            return null;
        }

        $date = $this->tanggal_terjadwal->copy();
        if ($this->jam_mulai_terjadwal) {
            $date->setTimeFrom($this->jam_mulai_terjadwal);
        } else {
            $date->startOfDay();
        }

        return $date;
    }

    /**
     * Accessor untuk mendapatkan objek Carbon waktu selesai lengkap (tanggal + jam_selesai_terjadwal).
     */
    public function getWaktuSelesaiFullAttribute(): ?Carbon
    {
        if (!$this->tanggal_terjadwal) {
            return null;
        }

        $date = $this->tanggal_terjadwal->copy();
        if ($this->jam_selesai_terjadwal) {
            $date->setTimeFrom($this->jam_selesai_terjadwal);
        } else {
            $date->endOfDay();
        }

        return $date;
    }

    /**
     * Cek apakah session sudah berlalu (lewat jam selesai atau tanggal sebelumnya).
     */
    public function isPast(): bool
    {
        if (!$this->tanggal_terjadwal) {
            return false;
        }

        // Jika tanggal sebelum hari ini (kemarin atau sebelumnya)
        if ($this->tanggal_terjadwal->lt(now()->startOfDay())) {
            return true;
        }

        // Jika tanggal adalah hari ini, hanya dianggap past/terlambat jika waktu selesai sudah lewat
        if ($this->tanggal_terjadwal->isToday()) {
            $waktuSelesai = $this->waktu_selesai_full;
            if ($waktuSelesai) {
                return now()->greaterThan($waktuSelesai);
            }
            return false;
        }

        return false;
    }

    /**
     * Cek apakah session adalah hari ini.
     */
    public function isToday(): bool
    {
        return $this->tanggal_terjadwal->isToday();
    }

    /**
     * Cek apakah session dapat dimulai.
     */
    public function canStart(): bool
    {
        return $this->status === self::STATUS_TERJADWAL &&
               ($this->isToday() || $this->isPast());
    }

    /**
     * Cek apakah session dapat diselesaikan.
     */
    /**
     * Cek apakah session dapat diselesaikan.
     */
    public function canComplete(): bool
    {
        return in_array($this->status, [self::STATUS_BERLANGSUNG, self::STATUS_TERJADWAL]);
    }

    /**
     * Cek apakah session dapat dibatalkan.
     */
    public function canCancel(): bool
    {
        return false; // Fitur batal sesi dinonaktifkan
    }

    /**
     * Cek apakah session dapat ditunda.
     */
    public function canReschedule(): bool
    {
        return in_array($this->status, [
            self::STATUS_TERJADWAL,
            self::STATUS_DITUNDA,
            self::STATUS_DIBATALKAN,
            self::STATUS_TIDAK_HADIR,
        ]);
    }

    /**
     * Cek apakah session dapat ditunda (menggantung).
     */
    public function canPostpone(): bool
    {
        return $this->status === self::STATUS_TERJADWAL;
    }

    /**
     * Method untuk memulai session.
     */
    public function start(array $data = []): bool
    {
        if (! $this->canStart()) {
            return false;
        }

        $this->status = self::STATUS_BERLANGSUNG;
        $this->tanggal_pelaksanaan = now()->toDateString();
        $this->jam_mulai_aktual = now()->format('H:i');

        if (! empty($data['topik_materi'])) {
            $this->topik_materi = $data['topik_materi'];
        }

        if (! empty($data['deskripsi_kegiatan'])) {
            $this->deskripsi_kegiatan = $data['deskripsi_kegiatan'];
        }

        return $this->save();
    }

    /**
     * Method untuk menyelesaikan session.
     */
    public function complete(array $data = []): bool
    {
        if (! $this->canComplete()) {
            return false;
        }

        $this->status = self::STATUS_SELESAI;
        
        // Set tanggal pelaksanaan jika belum ada
        if (! $this->tanggal_pelaksanaan) {
            $this->tanggal_pelaksanaan = now()->toDateString();
        }

        // Set waktu aktual
        if (! $this->jam_mulai_aktual) {
            // Jika langsung complete tanpa start, gunakan waktu jadwal atau now
            $this->jam_mulai_aktual = $this->jam_mulai_terjadwal; 
        }
        $this->jam_selesai_aktual = now()->format('H:i');

        if (! empty($data['catatan'])) {
            $this->catatan = $data['catatan'];
        }

        if (! empty($data['deskripsi_kegiatan'])) {
            $this->deskripsi_kegiatan = $data['deskripsi_kegiatan'];
        }

        // Laporan Mengajar relation is inverted, handled report-side

        $saved = $this->save();

        // Update progress rombel dan auto-create laporan mengajar
        if ($saved) {
            $this->rombel->incrementPertemuanSelesai();

            // Otomatis ubah status program utama menjadi 'selesai' jika seluruh sesi sudah 100% selesai
            $this->checkAndUpdateParentProgramCompletion();

            // Auto-create laporan mengajar jika belum ada
            if (! $this->laporanMengajar()->exists() && isset($data['auto_create_laporan']) && $data['auto_create_laporan']) {
                $this->autoCreateLaporanMengajar();
            }
        }

        return $saved;
    }

    /**
     * Otomatis memperbarui status Ekstrakurikuler induk menjadi 'selesai'
     * jika seluruh sesi pertemuan pada program tersebut sudah selesai.
     */
    public function checkAndUpdateParentProgramCompletion(): void
    {
        $ekstrakurikuler = $this->ekstrakurikuler;
        if (! $ekstrakurikuler || $ekstrakurikuler->status === Ekstrakurikuler::STATUS_SELESAI) {
            return;
        }

        $totalSessions = self::where('ekstrakurikuler_id', $ekstrakurikuler->id)->count();
        if ($totalSessions === 0) {
            return;
        }

        $completedSessions = self::where('ekstrakurikuler_id', $ekstrakurikuler->id)
            ->where('status', self::STATUS_SELESAI)
            ->count();

        if ($completedSessions >= $totalSessions) {
            $ekstrakurikuler->update([
                'status' => Ekstrakurikuler::STATUS_SELESAI,
                'updated_by' => auth()->id() ?? $ekstrakurikuler->updated_by,
            ]);

            \Illuminate\Support\Facades\Log::info("Program Ekstrakurikuler #{$ekstrakurikuler->id} ({$ekstrakurikuler->kategori_program}) otomatis diselesaikan karena 100% sesi telah selesai.");
        }
    }

    /**
     * Method untuk membatalkan session.
     */
    public function cancel(?string $alasan = null): bool
    {
        return false; // Pembatalan dinonaktifkan
    }

    /**
     * Method untuk menunda session ke tanggal lain.
     */
    public function reschedule(Carbon $newDate, ?string $alasan = null): bool
    {
        if (! $this->canReschedule()) {
            return false;
        }

        // Pindahkan tanggal terjadwal langsung ke tanggal baru
        $this->tanggal_terjadwal = $newDate->toDateString();
        $this->status = self::STATUS_TERJADWAL;
        $this->alasan_pembatalan = null;
        $this->tanggal_pengganti = null;

        if ($alasan) {
            $this->catatan = trim(($this->catatan ? $this->catatan . "\n" : "") . "Rescheduled: " . $alasan);
        }

        return $this->save();
    }

    /**
     * Method untuk menunda session (menggantung).
     */
    public function postpone(?string $alasan = null): bool
    {
        if (! $this->canPostpone()) {
            return false;
        }

        $this->status = self::STATUS_DITUNDA;
        $this->alasan_pembatalan = $alasan;

        return $this->save();
    }

    /**
     * Method untuk menandai instruktur tidak hadir.
     */
    public function markAbsent(?string $alasan = null): bool
    {
        if (! in_array($this->status, [self::STATUS_TERJADWAL, self::STATUS_BERLANGSUNG])) {
            return false;
        }

        $this->status = self::STATUS_TIDAK_HADIR;
        $this->tanggal_pelaksanaan = now()->toDateString();
        $this->alasan_pembatalan = $alasan;

        return $this->save();
    }

    /**
     * Method untuk membuat laporan mengajar dari session ini.
     */
    public function createLaporanMengajar(array $data = []): ?LaporanMengajar
    {
        if ($this->status !== self::STATUS_SELESAI || $this->laporanMengajar()->exists()) {
            return null;
        }

        $ekstrakurikuler = $this->ekstrakurikuler;
        $sekolah = $ekstrakurikuler->sekolah;
        $rombel = $this->rombel;

        $laporanData = array_merge([
            'user_id_instruktur' => $this->user_id_instruktur ?? auth()->id(),
            'user_id_assisten' => $this->user_id_asisten,
            'pertemuan_ke' => $this->nomor_pertemuan,
            'rombel' => $rombel->nama_rombel,
            'jadwal_mengajar' => $this->tanggal_pelaksanaan ?? $this->tanggal_terjadwal ?? now(),
            'jam_mulai' => $this->jam_mulai_aktual,
            'jam_selesai' => $this->jam_selesai_aktual,
            'kategori_pengajaran' => 'ekstrakurikuler',
                'materi_pengajaran' => $this->topik_materi ?? $ekstrakurikuler->kategori_program ?? 'Materi Ekstrakurikuler',
                'sekolah_kodlan' => $ekstrakurikuler->sekolah_kodlan,
                'jumlah_siswa_hadir' => $rombel->getJumlahSiswaAktual(),
                'jumlah_siswa_keluar' => 0,
                'refleksi_siswa' => '-',
                'refleksi_capaian' => '-',
                'keaktifan' => 'aktif', // Default enum
                'pemahaman_materi' => 'paham', // Default enum
                'metadata_json' => json_encode([
                    'ekstrakurikuler_session_id' => $this->id,
                    'ekstrakurikuler_id' => $this->ekstrakurikuler_id,
                    'ekstrakurikuler_rombel_id' => $this->ekstrakurikuler_rombel_id,
                    'kategori_program' => $ekstrakurikuler->kategori_program,
                    'source' => 'ekstrakurikuler',
                ]),
        ], $data);

        $laporan = LaporanMengajar::create($laporanData);

        if ($laporan) {
            $laporan->update(['ekstrakurikuler_session_id' => $this->id]);
        }

        return $laporan;
    }

    /**
     * Method untuk auto-generate laporan mengajar ketika session selesai.
     */
    public function autoCreateLaporanMengajar(): ?LaporanMengajar
    {
        if ($this->status === self::STATUS_SELESAI && ! $this->laporanMengajar()->exists()) {
            return $this->createLaporanMengajar([
                'deskripsi_kegiatan' => $this->deskripsi_kegiatan,
                'catatan' => $this->catatan,
            ]);
        }

        return $this->laporanMengajar;
    }

    /**
     * Cek apakah session ini berasal dari ekstrakurikuler.
     */
    public function isEkstrakurikulerSession(): bool
    {
        return true; // Semua instance dari model ini adalah ekstrakurikuler session
    }

    /**
     * Check if this session is an Ad-Hoc / Special event session.
     */
    public function isAdHoc(): bool
    {
        if ($this->nomor_pertemuan === 0) {
            return true;
        }

        $catLower = strtolower(
            $this->laporanMengajar->kategori_pengajaran 
            ?? $this->topik_materi 
            ?? $this->rombel->ekstrakurikuler->kategori_program 
            ?? ''
        );

        return str_contains($catLower, 'sosialisasi')
            || str_contains($catLower, 'trial')
            || str_contains($catLower, 'pameran')
            || str_contains($catLower, 'lomba')
            || str_contains($catLower, 'pendampingan')
            || str_contains($catLower, 'per-pertemuan')
            || str_contains($catLower, 'per pertemuan')
            || str_contains($catLower, 'event')
            || str_contains($catLower, 'inkul')
            || str_contains($catLower, 'mandiri');
    }

    /**
     * Boot method untuk handle events.
     */
    protected static function boot()
    {
        parent::boot();

        // Set created_by dan updated_by otomatis
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });
    }
}
