<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EkstrakurikulerRombel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'ekstrakurikuler_rombel';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'ekstrakurikuler_id',
        'nama_rombel',
        'nomor_rombel',
        'jumlah_siswa',
        'ruangan',
        'keterangan_ruangan',
        'tanggal_mulai',
        'tanggal_selesai',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'total_pertemuan',
        'frekuensi',
        'pertemuan_selesai',
        'user_id_instruktur',
        'user_id_asisten',
        'status',
        'catatan',
        'created_by',
        'updated_by',
    ];

    /**
     * Casting atribut ke tipe data yang sesuai.
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
        'jumlah_siswa' => 'integer',
        'nomor_rombel' => 'integer',
        'total_pertemuan' => 'integer',
        'pertemuan_selesai' => 'integer',
    ];

    /**
     * Konstanta untuk status rombel
     */
    const STATUS_BELUM_MULAI = 'belum_mulai';

    const STATUS_BERLANGSUNG = 'berlangsung';

    const STATUS_SELESAI = 'selesai';

    const STATUS_DIBATALKAN = 'dibatalkan';

    /**
     * Konstanta untuk hari
     */
    const HARI_SENIN = 'senin';

    const HARI_SELASA = 'selasa';

    const HARI_RABU = 'rabu';

    const HARI_KAMIS = 'kamis';

    const HARI_JUMAT = 'jumat';

    const HARI_SABTU = 'sabtu';

    const HARI_MINGGU = 'minggu';

    /**
     * Konstanta untuk frekuensi
     */
    const FREKUENSI_HARIAN = 'harian';

    const FREKUENSI_MINGGUAN = 'mingguan';

    const FREKUENSI_DUA_MINGGU = 'dua_minggu';

    const FREKUENSI_BULANAN = 'bulanan';

    /**
     * Relasi ke model Ekstrakurikuler.
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class);
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
     * Relasi ke EkstrakurikulerSession.
     * Satu rombel memiliki banyak sesi.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(EkstrakurikulerSession::class);
    }

    /**
     * Relasi ke siswa yang terdaftar di rombel ini.
     */
    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'siswa_ekstrakurikuler', 'ekstrakurikuler_rombel_id', 'siswa_id')
            ->withPivot([
                'ekstrakurikuler_id',
                'status',
                'tanggal_daftar',
                'tanggal_keluar',
                'alasan_keluar',
                'catatan',
            ])
            ->withTimestamps();
    }

    /**
     * Relasi ke siswa yang aktif di rombel ini.
     */
    public function siswaAktif(): BelongsToMany
    {
        return $this->siswa()->wherePivot('status', 'aktif');
    }

    /**
     * Relasi ke enrollments rombel ini.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(SiswaEkstrakurikuler::class, 'ekstrakurikuler_rombel_id');
    }

    /**
     * Relasi ke enrollments aktif di rombel ini.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'aktif');
    }

    /**
     * Scope untuk filter berdasarkan status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter rombel yang sedang berlangsung.
     */
    public function scopeBerlangsung($query)
    {
        return $query->where('status', self::STATUS_BERLANGSUNG);
    }

    /**
     * Scope untuk filter berdasarkan hari.
     */
    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    /**
     * Scope untuk filter berdasarkan instruktur.
     */
    public function scopeByInstruktur($query, $userId)
    {
        return $query->where('user_id_instruktur', $userId);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_mulai', [$startDate, $endDate])
            ->orWhereBetween('tanggal_selesai', [$startDate, $endDate]);
    }

    /**
     * Accessor untuk mendapatkan label status.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_BELUM_MULAI => 'Belum Mulai',
            self::STATUS_BERLANGSUNG => 'Berlangsung',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Accessor untuk mendapatkan label hari.
     */
    public function getHariLabelAttribute(): string
    {
        $labels = [
            self::HARI_SENIN => 'Senin',
            self::HARI_SELASA => 'Selasa',
            self::HARI_RABU => 'Rabu',
            self::HARI_KAMIS => 'Kamis',
            self::HARI_JUMAT => 'Jumat',
            self::HARI_SABTU => 'Sabtu',
            self::HARI_MINGGU => 'Minggu',
        ];

        return $labels[$this->hari] ?? 'Unknown';
    }

    /**
     * Accessor untuk mendapatkan label frekuensi.
     */
    public function getFrekuensiLabelAttribute(): string
    {
        $labels = [
            self::FREKUENSI_HARIAN => 'Harian',
            self::FREKUENSI_MINGGUAN => 'Mingguan',
            self::FREKUENSI_DUA_MINGGU => 'Dua Minggu Sekali',
            self::FREKUENSI_BULANAN => 'Bulanan',
        ];

        return $labels[$this->frekuensi] ?? 'Unknown';
    }

    /**
     * Accessor untuk mendapatkan waktu formatted.
     */
    public function getJadwalWaktuAttribute(): string
    {
        return $this->jam_mulai->format('H:i').' - '.$this->jam_selesai->format('H:i');
    }

    /**
     * Cek apakah rombel sudah dimulai.
     */
    public function isStarted(): bool
    {
        return in_array($this->status, [self::STATUS_BERLANGSUNG, self::STATUS_SELESAI]);
    }

    /**
     * Cek apakah rombel sedang berlangsung.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_BERLANGSUNG;
    }

    /**
     * Cek apakah rombel sudah selesai.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_SELESAI ||
               $this->pertemuan_selesai >= $this->total_pertemuan;
    }

    /**
     * Method untuk menghitung progress pertemuan.
     */
    public function getProgressPersentase(): float
    {
        if ($this->total_pertemuan <= 0) {
            return 0;
        }

        return round(($this->pertemuan_selesai / $this->total_pertemuan) * 100, 2);
    }

    /**
     * Method untuk mendapatkan sisa pertemuan.
     */
    public function getSisaPertemuan(): int
    {
        return max(0, $this->total_pertemuan - $this->pertemuan_selesai);
    }

    /**
     * Method untuk increment pertemuan selesai.
     */
    public function incrementPertemuanSelesai(): bool
    {
        if ($this->pertemuan_selesai < $this->total_pertemuan) {
            $this->pertemuan_selesai++;

            // Auto update status jika sudah mencapai total pertemuan
            if ($this->pertemuan_selesai >= $this->total_pertemuan) {
                $this->status = self::STATUS_SELESAI;
            }

            return $this->save();
        }

        return false;
    }

    /**
     * Catatan: Kolom jumlah_siswa berfungsi murni sebagai Target Kuota Rombel.
     * Untuk mendapatkan jumlah siswa terdaftar saat ini, gunakan getJumlahSiswaAktual().
     */
    public function incrementJumlahSiswa(): bool
    {
        return true;
    }

    public function decrementJumlahSiswa(): bool
    {
        return true;
    }

    public function syncJumlahSiswa(): bool
    {
        return true;
    }

    public function getJumlahSiswaAktual(): int
    {
        return $this->activeEnrollments()->count();
    }

    /**
     * Cek apakah rombel ini aman untuk dihapus:
     * 1. Jumlah siswa terdaftar harus 0 (baik aktif maupun total enrollment)
     * 2. Tidak ada laporan mengajar yang terhubung ke sesi di rombel ini
     * 3. Tidak ada sesi yang sudah selesai / sedang berlangsung
     */
    public function canBeDeleted(): bool
    {
        return $this->getDeleteRestrictionReason() === null;
    }

    /**
     * Dapatkan alasan pembatasan penghapusan rombel (null jika boleh dihapus).
     */
    public function getDeleteRestrictionReason(): ?string
    {
        // Hanya hitung siswa yang AKTIF, konsisten dengan getJumlahSiswaAktual()
        $siswaCount = $this->activeEnrollments()->count();
        if ($siswaCount > 0) {
            return "Memiliki {$siswaCount} siswa aktif terdaftar. Pindahkan atau batalkan siswa terlebih dahulu.";
        }

        if ($this->sessions()->whereHas('laporanMengajar')->exists()) {
            return "Sudah memiliki riwayat Laporan Mengajar.";
        }

        $nonScheduledCount = $this->sessions()->where('status', '!=', 'terjadwal')->count();
        if ($nonScheduledCount > 0) {
            return "Memiliki {$nonScheduledCount} sesi yang sudah selesai atau berlangsung.";
        }

        return null;
    }

    /**
     * Method untuk generate sessions berdasarkan jadwal.
     */
    public function generateSessions(): void
    {
        // Skip auto-generation for Ad-Hoc / Special / Cancelled wrapper contracts
        if ($this->ekstrakurikuler) {
            $catLower = strtolower($this->ekstrakurikuler->kategori_program ?? '');
            $isAdHoc = str_contains($catLower, 'trial') 
                    || str_contains($catLower, 'free')
                    || str_contains($catLower, 'sosialisasi')
                    || str_contains($catLower, 'pameran')
                    || str_contains($catLower, 'lomba')
                    || str_contains($catLower, 'event');
            if ($this->ekstrakurikuler->status === 'dibatalkan' || $isAdHoc) {
                return;
            }
        }

        // Hapus sessions yang sudah ada dan belum dimulai
        $this->sessions()->where('status', 'terjadwal')->delete();

        $currentDate = $this->tanggal_mulai->copy();
        $endDate = $this->tanggal_selesai;
        $sessionCount = 0;

        // Mapping hari ke nomor hari dalam minggu (1=Senin, 7=Minggu)
        $hariMapping = [
            self::HARI_SENIN => 1,
            self::HARI_SELASA => 2,
            self::HARI_RABU => 3,
            self::HARI_KAMIS => 4,
            self::HARI_JUMAT => 5,
            self::HARI_SABTU => 6,
            self::HARI_MINGGU => 7,
        ];

        $targetHari = $hariMapping[$this->hari] ?? $currentDate->dayOfWeekIso;

        // Interval berdasarkan frekuensi
        $intervalDays = match ($this->frekuensi) {
            self::FREKUENSI_HARIAN => 1,
            self::FREKUENSI_MINGGUAN => 7,
            self::FREKUENSI_DUA_MINGGU => 14,
            self::FREKUENSI_BULANAN => 30,
            default => 7
        };

        // Cari hari pertama yang sesuai dengan jadwal
        while ($currentDate->dayOfWeek !== $targetHari && $currentDate->lte($endDate)) {
            $currentDate->addDay();
        }

        // Generate sessions
        while ($currentDate->lte($endDate) && $sessionCount < $this->total_pertemuan) {
            EkstrakurikulerSession::create([
                'ekstrakurikuler_id' => $this->ekstrakurikuler_id,
                'ekstrakurikuler_rombel_id' => $this->id,
                'nomor_pertemuan' => $sessionCount + 1,
                'tanggal_terjadwal' => $currentDate->format('Y-m-d'),
                'jam_mulai_terjadwal' => $this->jam_mulai,
                'jam_selesai_terjadwal' => $this->jam_selesai,
                'user_id_instruktur' => $this->user_id_instruktur,
                'user_id_asisten' => $this->user_id_asisten,
                'status' => 'terjadwal',
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $sessionCount++;
            $currentDate->addDays($intervalDays);
        }
    }

    /**
     * Boot method untuk handle events.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!empty($model->nomor_rombel)) {
                $model->nama_rombel = "Rombel {$model->nomor_rombel}";
            }
        });

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

        // Auto generate sessions setelah rombel dibuat
        static::created(function ($model) {
            $model->generateSessions();
        });
    }
}
