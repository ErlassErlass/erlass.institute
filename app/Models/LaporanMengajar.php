<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LaporanMengajar extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'laporan_mengajar';

    /**
     * Atribut yang dapat diisi secara massal.
     * Disesuaikan dengan struktur tabel final Anda.
     */
    protected $fillable = [
        'user_id_instruktur',
        'user_id_assisten',
        'sekolah_kodlan', // ✅ HANYA INI yang kita butuhkan untuk relasi sekolah
        'status', // Untuk fitur draft
        'pertemuan_ke',
        'rombel',
        'jadwal_mengajar',
        'jam_mulai',
        'jam_selesai',
        'kategori_pengajaran',
        'materi_pengajaran',
        'jumlah_siswa_hadir',
        'jumlah_siswa_keluar',
        'foto_kegiatan',
        'foto_absensi_siswa',
        'refleksi_siswa',
        'refleksi_capaian',
        'keaktifan',
        'pemahaman_materi',
    ];

    /**
     * Model event untuk menghapus file terkait secara otomatis saat record dihapus.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($laporan) {
            if ($laporan->foto_kegiatan) {
                Storage::disk('public')->delete($laporan->foto_kegiatan);
            }
            if ($laporan->foto_absensi_siswa) {
                Storage::disk('public')->delete($laporan->foto_absensi_siswa);
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

    /**
     * ✅ SATU-SATUNYA relasi ke Sekolah yang BENAR.
     */
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke Absensi. (Gunakan nama plural 'absensis' agar lebih jelas)
     */
    public function absensis() // Gunakan nama plural untuk relasi hasMany
    {
        return $this->hasMany(Absensi::class, 'laporan_mengajar_id');
    }
}