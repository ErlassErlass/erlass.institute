<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Instruktur & Assisten relationships
use App\Models\Sekolah; // Sekolah relationship
use App\Models\Absensi; // Absensi relationship

class LaporanMengajar extends Model
{
    protected $table = 'laporan_mengajar';

    protected $fillable = [
        'user_id_instruktur',
        'user_id_assisten',
        'pertemuan_ke',
        'rombel',
        'jadwal_mengajar',
        'jam_mulai',
        'jam_selesai',
        'kategori_pengajaran',
        'materi_pengajaran',
        'sekolah_kota',
        'sekolah_kecamatan',
        'sekolah_nama',
        'jumlah_siswa_hadir',
        'jumlah_siswa_keluar',
        'foto_kegiatan',
        'refleksi_siswa',
        'refleksi_capaian',
        'keaktifan',
        'pemahaman_materi',
    ];

    // Automatically delete foto_kegiatan on deletion
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($laporan) {
            if ($laporan->foto_kegiatan) {
                Storage::disk('public')->delete($laporan->foto_kegiatan);
            }
        });
    }

    // Instruktur Relationship
    public function instruktur()
    {
        return $this->belongsTo(User::class, 'user_id_instruktur');
    }

    // Assisten Relationship
    public function assisten()
    {
        return $this->belongsTo(User::class, 'user_id_assisten');
    }

    // Sekolah Relationship
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_nama', 'namasekolah');
    }

    // Absensi Relationship
    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

}
