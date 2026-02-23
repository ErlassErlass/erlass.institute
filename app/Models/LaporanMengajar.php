<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LaporanMengajar extends Model
{
    use HasFactory;

    protected $table = 'laporan_mengajar';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
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
        return $this->hasOne(EkstrakurikulerSession::class, 'laporan_mengajar_id');
    }

    /**
     * Accessor untuk mendapatkan jumlah siswa hadir.
     */
    public function getJumlahHadirAttribute()
    {
        return $this->absensi()->where('hadir', true)->count();
    }

    /**
     * Accessor untuk mendapatkan jumlah siswa tidak hadir.
     */
    public function getJumlahTidakHadirAttribute()
    {
        return $this->absensi()->where('hadir', false)->count();
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

        return $data['nama_program'] ?? null;
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
}
