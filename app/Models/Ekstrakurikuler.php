<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ekstrakurikuler extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'ekstrakurikuler';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nama_program',
        'kategori_program',
        'deskripsi',
        'user_id_sales',
        'region',
        'sekolah_kodlan',
        'alamat_lengkap',
        'google_maps_link',
        'jarak_km',
        'kepala_sekolah',
        'penanggung_jawab',
        'no_telepon',
        'koneksi_internet',
        'proyektor',
        'keterangan_proyektor',
        'kabel_hdmi',
        'kabel_vga',
        'keterangan_kabel',
        'total_siswa',
        'total_ruangan',
        'total_rombel',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_pertemuan',
        'frekuensi',
        'status',
        'tanggal_disetujui',
        'disetujui_oleh',
        'created_by',
        'updated_by',
    ];

    /**
     * Casting atribut ke tipe data yang sesuai.
     */
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'tanggal_disetujui' => 'datetime',
        'jarak_km' => 'decimal:2',
        'total_siswa' => 'integer',
        'total_ruangan' => 'integer',
        'total_rombel' => 'integer',
        'total_pertemuan' => 'integer',
    ];

    /**
     * Konstanta untuk kategori program
     */
    const KATEGORI_CODING_SCRATCH = 'Coding Scratch';
    const KATEGORI_ENGLISH_COURSE = 'English Course';
    const KATEGORI_MICROBIT_LEARNING = 'Micro:bit Learning Kit';
    const KATEGORI_PICTOBLOX_AI = 'Pictoblox AI';
    const KATEGORI_ROBOTIK_EXPLORER = 'Robotik Explorer';
    const KATEGORI_ROBOTIK_JIMU = 'Robotik Jimu';

    /**
     * Konstanta untuk status program
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_AKTIF = 'aktif';
    const STATUS_SELESAI = 'selesai';
    const STATUS_DIBATALKAN = 'dibatalkan';

    /**
     * Konstanta untuk frekuensi
     */
    const FREKUENSI_HARIAN = 'harian';
    const FREKUENSI_MINGGUAN = 'mingguan';
    const FREKUENSI_DUA_MINGGU = 'dua_minggu';
    const FREKUENSI_BULANAN = 'bulanan';

    /**
     * Konstanta untuk fasilitas
     */
    const FASILITAS_ADA = 'ada';
    const FASILITAS_TIDAK_ADA = 'tidak_ada';
    const FASILITAS_TIDAK_DIKETAHUI = 'tidak_diketahui';

    /**
     * Relasi ke model Sekolah.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke User untuk sales.
     */
    public function sales(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_sales');
    }


    /**
     * Relasi ke User yang menyetujui.
     */
    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
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
     * Relasi ke EkstrakurikulerRombel.
     * Satu ekstrakurikuler memiliki banyak rombel.
     */
    public function rombels(): HasMany
    {
        return $this->hasMany(EkstrakurikulerRombel::class);
    }

    /**
     * Relasi ke EkstrakurikulerSession melalui rombels.
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(EkstrakurikulerSession::class);
    }

    /**
     * Relasi many-to-many ke Siswa melalui pivot table siswa_ekstrakurikuler.
     */
    public function siswa(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'siswa_ekstrakurikuler')
                    ->withPivot([
                        'ekstrakurikuler_rombel_id',
                        'status',
                        'tanggal_daftar',
                        'tanggal_keluar',
                        'alasan_keluar',
                        'catatan'
                    ])
                    ->withTimestamps();
    }

    /**
     * Relasi ke siswa yang aktif saja.
     */
    public function siswaAktif(): BelongsToMany
    {
        return $this->siswa()->wherePivot('status', 'aktif');
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
     * Scope untuk filter berdasarkan status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter ekstrakurikuler aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    /**
     * Scope untuk filter berdasarkan sekolah.
     */
    public function scopeBySekolah($query, $kodlan)
    {
        return $query->where('sekolah_kodlan', $kodlan);
    }

    /**
     * Scope untuk filter berdasarkan region.
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope untuk filter berdasarkan kota melalui relasi sekolah.
     */
    public function scopeByKota($query, $kota)
    {
        return $query->whereHas('sekolah', function($q) use ($kota) {
            $q->where('kota', $kota);
        });
    }

    /**
     * Accessor untuk mendapatkan label status.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_DIAJUKAN => 'Diajukan',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? 'Unknown';
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
     * Cek apakah program bisa disetujui.
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    /**
     * Cek apakah program bisa diaktifkan.
     */
    public function canBeActivated(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    /**
     * Cek apakah program sedang berjalan.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    /**
     * Method untuk menghitung total siswa dari semua rombel.
     */
    public function getTotalSiswaFromRombels(): int
    {
        return $this->rombels()->sum('jumlah_siswa');
    }

    /**
     * Method untuk menghitung total siswa yang terdaftar aktif.
     */
    public function getTotalSiswaAktif(): int
    {
        return $this->activeEnrollments()->count();
    }

    /**
     * Method untuk mendapatkan siswa berdasarkan rombel tertentu.
     */
    public function getSiswaByRombel(int $rombelId)
    {
        return $this->siswaAktif()->wherePivot('ekstrakurikuler_rombel_id', $rombelId);
    }

    /**
     * Method untuk menghitung progress pertemuan.
     */
    public function getProgressPertemuan(): array
    {
        $totalSession = $this->sessions()->count();
        $completedSession = $this->sessions()->where('status', 'selesai')->count();
        
        return [
            'total' => $totalSession,
            'selesai' => $completedSession,
            'persentase' => $totalSession > 0 ? round(($completedSession / $totalSession) * 100, 2) : 0
        ];
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