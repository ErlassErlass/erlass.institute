<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DashboardController;

class LaporanMengajar extends Model
{
    use HasFactory;

    protected $table = 'laporan_mengajar';

    /**
     * Clear dashboard cache when reports change.
     */
    protected static function booted(): void
    {
        $bustCache = function ($model) {
            DashboardController::clearCache($model->user_id_instruktur);
        };

        static::created($bustCache);
        static::updated($bustCache);
        static::deleted($bustCache);
    }

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ekstrakurikuler_session_id',
        'user_id_instruktur',
        'user_id_assisten',
        'pertemuan_ke',
        'rombel',
        'sekolah_kodlan',
        'sekolah_nama', // Added for consistency
        'jadwal_mengajar',
        'jam_mulai',
        'jam_selesai',
        'kategori_pengajaran', // Anda mungkin lupa menambahkan ini sebelumnya
        'materi_pengajaran',
        'jumlah_siswa_hadir',
        'jumlah_siswa_keluar',
        'jumlah_siswa_tidak_hadir',
        'foto_kegiatan',
        'foto_absensi_siswa', // Field baru untuk foto absensi
        'file_project', // File project .sb3
        'refleksi_siswa',
        'refleksi_capaian',
        'keaktifan',
        'pemahaman_materi',
        'metadata_json',
    ];

    /**
     * Atribut yang tidak boleh diisi secara massal untuk keamanan.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'jumlah_siswa_hadir' => 0,
        'jumlah_siswa_tidak_hadir' => 0,
        'jumlah_siswa_keluar' => 0,
    ];

    /**
     * Casting atribut ke tipe data yang sesuai.
     */
    protected $casts = [
        'jadwal_mengajar' => 'date',
        'metadata_json' => 'json',
    ];

    /**
     * Model event untuk menghapus file terkait secara otomatis saat record dihapus.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($laporan) {
            // Hapus foto kegiatan jika ada
            if ($laporan->foto_kegiatan) {
                Storage::disk('public')->delete($laporan->foto_kegiatan);
            }

            // ✅ DIPERBAIKI: Hapus foto absensi siswa jika ada, mencegah file sampah.
            if ($laporan->foto_absensi_siswa) {
                Storage::disk('public')->delete($laporan->foto_absensi_siswa);
            }

            // Hapus file project jika ada
            if ($laporan->file_project) {
                Storage::disk('public')->delete($laporan->file_project);
            }
        });
    }

    /**
     * Relasi ke User sebagai Instruktur.
     */
    public function instruktur()
    {
        return $this->belongsTo(User::class, 'user_id_instruktur');
    }

    /**
     * Relasi ke User sebagai Asisten.
     */
    public function asisten()
    {
        return $this->belongsTo(User::class, 'user_id_assisten');
    }

    // app/Models/LaporanMengajar.php
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke Absensi.
     */
    /**
     * Relasi ke Absensi.
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'laporan_mengajar_id');
    }

    /**
     * Alias for backward compatibility if needed, but standardizing on absensi
     */
    public function absensis()
    {
        return $this->absensi();
    }

    /**
     * Relasi ke EkstrakurikulerSession (jika laporan berasal dari ekstrakurikuler).
     */
    public function ekstrakurikulerSession()
    {
        return $this->belongsTo(EkstrakurikulerSession::class, 'ekstrakurikuler_session_id');
    }

    public function session()
    {
        return $this->ekstrakurikulerSession();
    }

    /**
     * Accessor untuk mendapatkan jumlah siswa hadir.
     */
    public function getJumlahHadirAttribute()
    {
        return $this->absensi()->where('status', 'hadir')->count();
    }

    /**
     * Accessor untuk mendapatkan jumlah siswa tidak hadir.
     */
    public function getJumlahTidakHadirAttribute()
    {
        return $this->absensi()->whereIn('status', ['izin', 'sakit', 'alpha'])->count();
    }

    /**
     * Cek apakah laporan ini berasal dari ekstrakurikuler.
     */
    public function isFromEkstrakurikuler(): bool
    {
        return $this->kategori_pengajaran === 'ekstrakurikuler' ||
               ($this->metadata_json && isset($this->metadata_json['source']) && $this->metadata_json['source'] === 'ekstrakurikuler');
    }

    /**
     * Mendapatkan data ekstrakurikuler dari metadata.
     */
    public function getEkstrakurikulerData(): ?array
    {
        if ($this->isFromEkstrakurikuler() && $this->metadata_json) {
            return $this->metadata_json;
        }

        return null;
    }

    /**
     * Mendapatkan nama program ekstrakurikuler.
     */
    public function getEkstrakurikulerName(): ?string
    {
        $data = $this->getEkstrakurikulerData();

        return $data['kategori_program'] ?? $data['nama_program'] ?? null;
    }

    /**
     * Scope untuk filter laporan berdasarkan kategori pengajaran.
     */
    public function scopeByKategori($query, string $kategori)
    {
        return $query->where('kategori_pengajaran', $kategori);
    }

    /**
     * Scope untuk filter laporan ekstrakurikuler.
     */
    public function scopeEkstrakurikuler($query)
    {
        return $query->where('kategori_pengajaran', 'ekstrakurikuler');
    }

    /**
     * Scope untuk filter laporan regular (non-ekstrakurikuler).
     */
    public function scopeRegular($query)
    {
        return $query->where('kategori_pengajaran', '!=', 'ekstrakurikuler')
            ->orWhereNull('kategori_pengajaran');
    }

    /**
     * Accessor for formatted date (dd/mm/yyyy).
     */
    public function getJadwalMengajarFormattedAttribute()
    {
        return $this->jadwal_mengajar ? $this->jadwal_mengajar->format('d/m/Y') : '';
    }

    /**
     * Mutator to handle dd/mm/yyyy input.
     */
    public function setJadwalMengajarAttribute($value)
    {
        if (is_string($value) && preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            $this->attributes['jadwal_mengajar'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } else {
            $this->attributes['jadwal_mengajar'] = $value;
        }
    }

    /**
     * Ensure this LaporanMengajar is linked to an EkstrakurikulerSession so it is 100% included in Payroll.
     */
    public function ensureSessionLinked(): \App\Models\EkstrakurikulerSession
    {
        if ($this->ekstrakurikuler_session_id) {
            $session = \App\Models\EkstrakurikulerSession::find($this->ekstrakurikuler_session_id);
            if ($session) {
                return $session;
            }
        }

        // Cek apakah kategori pengajaran termasuk Kegiatan Ad-Hoc / Khusus / Non-Reguler
        $catLower = strtolower($this->kategori_pengajaran ?? $this->materi_pengajaran ?? '');
        $isAdHocCategory = str_contains($catLower, 'sosialisasi')
            || str_contains($catLower, 'trial')
            || str_contains($catLower, 'pameran')
            || str_contains($catLower, 'lomba')
            || str_contains($catLower, 'pendampingan')
            || str_contains($catLower, 'per-pertemuan')
            || str_contains($catLower, 'per pertemuan')
            || str_contains($catLower, 'event')
            || str_contains($catLower, 'inkul')
            || str_contains($catLower, 'mandiri');

        // 1. Cek apakah ada sesi terjadwal reguler pada tanggal & sekolah tersebut yang belum di-link
        if (! $isAdHocCategory && $this->sekolah_kodlan && $this->jadwal_mengajar) {
            $matchingSession = \App\Models\EkstrakurikulerSession::whereHas('ekstrakurikuler', function ($q) {
                    $q->where('sekolah_kodlan', $this->sekolah_kodlan);
                })
                ->where(function ($q) {
                    $q->whereDate('tanggal_pelaksanaan', $this->jadwal_mengajar)
                      ->orWhereDate('tanggal_terjadwal', $this->jadwal_mengajar);
                })
                ->where('user_id_instruktur', $this->user_id_instruktur)
                ->whereDoesntHave('laporanMengajar')
                ->first();

            if ($matchingSession) {
                $this->update(['ekstrakurikuler_session_id' => $matchingSession->id]);
                return $matchingSession;
            }
        }

        $ekstrakurikulerId = null;
        $rombelId = null;

        if ($this->sekolah_kodlan) {
            $ekskul = \App\Models\Ekstrakurikuler::where('sekolah_kodlan', $this->sekolah_kodlan)->first();
            if (! $ekskul) {
                $ekskul = \App\Models\Ekstrakurikuler::create([
                    'sekolah_kodlan' => $this->sekolah_kodlan,
                    'kategori_program' => $this->kategori_pengajaran ?? 'Reguler',
                    'total_siswa' => 15,
                    'total_ruangan' => 1,
                    'total_rombel' => 1,
                    'total_pertemuan' => 12,
                    'tanggal_mulai' => $this->jadwal_mengajar ?? now(),
                    'tanggal_selesai' => ($this->jadwal_mengajar ? $this->jadwal_mengajar->copy()->addMonths(6) : now()->addMonths(6)),
                    'status' => $isAdHocCategory ? 'dibatalkan' : 'aktif',
                ]);
            }
            $ekstrakurikulerId = $ekskul->id;

            $rombel = \App\Models\EkstrakurikulerRombel::where('ekstrakurikuler_id', $ekskul->id)->first();
            if (! $rombel) {
                $totalSiswa = ($this->jumlah_siswa_hadir + $this->jumlah_siswa_tidak_hadir);
                $rombel = \App\Models\EkstrakurikulerRombel::create([
                    'ekstrakurikuler_id' => $ekskul->id,
                    'nama_rombel' => 'Rombel ' . ($this->rombel ?? '1'),
                    'nomor_rombel' => (int) preg_replace('/[^0-9]/', '', $this->rombel ?? '1') ?: 1,
                    'jumlah_siswa' => max(15, $totalSiswa),
                    'tanggal_mulai' => $this->jadwal_mengajar ?? now(),
                    'tanggal_selesai' => ($this->jadwal_mengajar ? $this->jadwal_mengajar->copy()->addMonths(6) : now()->addMonths(6)),
                    'jam_mulai' => $this->jam_mulai ?? '08:00:00',
                    'jam_selesai' => $this->jam_selesai ?? '09:30:00',
                    'total_pertemuan' => $isAdHocCategory ? 1 : 12,
                    'status' => 'berlangsung',
                ]);
            }
            $rombelId = $rombel->id;
        }

        // Untuk Kegiatan Ad-Hoc / Khusus / Backup, pastikan nomor_pertemuan unik dan tidak bentrok (termasuk soft-deleted)
        if ($isAdHocCategory) {
            $nomorPertemuan = 0;
            if ($rombelId) {
                while (\App\Models\EkstrakurikulerSession::withTrashed()->where('ekstrakurikuler_rombel_id', $rombelId)->where('nomor_pertemuan', $nomorPertemuan)->exists()) {
                    $nomorPertemuan++;
                }
            }
        } else {
            $nomorPertemuan = $this->pertemuan_ke ?? 1;
            if ($rombelId) {
                while (\App\Models\EkstrakurikulerSession::withTrashed()->where('ekstrakurikuler_rombel_id', $rombelId)->where('nomor_pertemuan', $nomorPertemuan)->exists()) {
                    $nomorPertemuan++;
                }
            }
        }

        $session = \App\Models\EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $ekstrakurikulerId,
            'ekstrakurikuler_rombel_id' => $rombelId,
            'nomor_pertemuan' => $nomorPertemuan,
            'tanggal_terjadwal' => $this->jadwal_mengajar,
            'tanggal_pelaksanaan' => $this->jadwal_mengajar,
            'jam_mulai_terjadwal' => $this->jam_mulai ?? '08:00:00',
            'jam_selesai_terjadwal' => $this->jam_selesai ?? '09:30:00',
            'jam_mulai_aktual' => $this->jam_mulai ?? '08:00:00',
            'jam_selesai_aktual' => $this->jam_selesai ?? '09:30:00',
            'status' => \App\Models\EkstrakurikulerSession::STATUS_SELESAI,
            'payment_status' => 'unpaid',
            'user_id_instruktur' => $this->user_id_instruktur,
            'user_id_asisten' => $this->user_id_assisten,
            'topik_materi' => $this->materi_pengajaran ?? $this->kategori_pengajaran ?? 'Laporan Mengajar',
        ]);

        $this->update(['ekstrakurikuler_session_id' => $session->id]);

        $session->checkAndUpdateParentProgramCompletion();

        return $session;
    }

    /**
     * Check if this teaching report is for an Ad-Hoc / Special event session.
     */
    public function isAdHoc(): bool
    {
        if ($this->ekstrakurikulerSession && $this->ekstrakurikulerSession->nomor_pertemuan === 0) {
            return true;
        }

        $catLower = strtolower($this->kategori_pengajaran ?? $this->materi_pengajaran ?? '');
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
}
