<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Notifications\Notifiable;

class Siswa extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'siswa';

    protected $fillable = [
        'nama_lengkap',
        'nisn',
        'sekolah_kodlan',
        'rombel',
        'kelas',
        'no_hp_orangtua', // Added
    ];

    // ... existing guarded ...

    /**
     * Route notifications for the WhatsApp channel.
     *
     * @return string
     */
    public function routeNotificationForWhatsapp($notification)
    {
        return $this->no_hp_orangtua;
    }


    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Relasi ke model Sekolah.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke model Absensi (one-to-many).
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }

    /**
     * Relasi many-to-many ke Ekstrakurikuler melalui pivot table siswa_ekstrakurikuler.
     */
    public function ekstrakurikulers(): BelongsToMany
    {
        return $this->belongsToMany(Ekstrakurikuler::class, 'siswa_ekstrakurikuler')
            ->withPivot([
                'ekstrakurikuler_rombel_id',
                'status',
                'tanggal_daftar',
                'tanggal_keluar',
                'alasan_keluar',
                'catatan',
            ])
            ->withTimestamps();
    }

    /**
     * Relasi ke ekstrakurikuler yang aktif saja.
     */
    public function ekstrakurikulersAktif(): BelongsToMany
    {
        return $this->ekstrakurikulers()->wherePivot('status', 'aktif');
    }

    /**
     * Relasi ke SiswaEkstrakurikuler (pivot model) untuk akses lebih detail.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(SiswaEkstrakurikuler::class);
    }

    /**
     * Relasi ke enrollment yang aktif.
     */
    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('status', 'aktif');
    }

    /**
     * Check if student was absent in a specific report.
     */
    public function wasAbsentIn(LaporanMengajar $report): bool
    {
        return $this->absensis()
            ->where('laporan_mengajar_id', $report->id)
            ->where('hadir', false)
            ->exists();
    }

    /**
     * Cek apakah siswa terdaftar dalam ekstrakurikuler tertentu.
     */
    public function isEnrolledIn(int $ekstrakurikulerId): bool
    {
        return $this->ekstrakurikulersAktif()
            ->where('ekstrakurikuler.id', $ekstrakurikulerId)
            ->exists();
    }

    /**
     * Cek apakah siswa terdaftar dalam rombel ekstrakurikuler tertentu.
     */
    public function isEnrolledInRombel(int $ekstrakurikulerRombelId): bool
    {
        return $this->enrollments()
            ->where('ekstrakurikuler_rombel_id', $ekstrakurikulerRombelId)
            ->where('status', 'aktif')
            ->exists();
    }

    /**
     * Mendapatkan semua ekstrakurikuler yang dapat diikuti oleh siswa (berdasarkan sekolah).
     */
    public function availableEkstrakurikulers()
    {
        return Ekstrakurikuler::where('sekolah_kodlan', $this->sekolah_kodlan)
            ->where('status', Ekstrakurikuler::STATUS_AKTIF);
    }

    /**
     * Mendapatkan jumlah ekstrakurikuler yang diikuti siswa.
     */
    public function getTotalEkstrakurikulersAttribute(): int
    {
        return $this->ekstrakurikulersAktif()->count();
    }

    /**
     * Scope untuk filter siswa berdasarkan ekstrakurikuler.
     */
    public function scopeInEkstrakurikuler($query, int $ekstrakurikulerId)
    {
        return $query->whereHas('ekstrakurikulersAktif', function ($q) use ($ekstrakurikulerId) {
            $q->where('ekstrakurikuler.id', $ekstrakurikulerId);
        });
    }

    /**
     * Scope untuk filter siswa berdasarkan rombel ekstrakurikuler.
     */
    public function scopeInEkstrakurikulerRombel($query, int $ekstrakurikulerRombelId)
    {
        return $query->whereHas('activeEnrollments', function ($q) use ($ekstrakurikulerRombelId) {
            $q->where('ekstrakurikuler_rombel_id', $ekstrakurikulerRombelId);
        });
    }
}
