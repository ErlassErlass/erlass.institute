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
     *
     * @var string
     */
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
        'jadwal_mengajar',
        'jam_mulai',
        'jam_selesai',
        'kategori_pengajaran', // Anda mungkin lupa menambahkan ini sebelumnya
        'materi_pengajaran',
        'sekolah_nama',
        'sekolah_kota',
        'sekolah_kecamatan',
        'jumlah_siswa_hadir',
        'jumlah_siswa_keluar',
        'foto_kegiatan',
        'foto_absensi_siswa', // Field baru untuk foto absensi
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
            // Hapus foto kegiatan jika ada
            if ($laporan->foto_kegiatan) {
                Storage::disk('public')->delete($laporan->foto_kegiatan);
            }
            
            // ✅ DIPERBAIKI: Hapus foto absensi siswa jika ada, mencegah file sampah.
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
     * Relasi ke Absensi.
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}