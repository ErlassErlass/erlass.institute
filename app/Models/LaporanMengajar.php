<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class LaporanMengajar extends Model {
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

    // In LaporanMengajar model
protected static function boot() {
    parent::boot();

    static::deleting(function ($laporan) {
        if ($laporan->foto_kegiatan) {
            Storage::disk('public')->delete($laporan->foto_kegiatan);
        }
    });
}

    // Relationships
    public function instruktur() {
        return $this->belongsTo(User::class, 'user_id_instruktur');
    }

    public function sekolah() {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan'); // Assuming sekolah_kodlan is the foreign key
    }

    public function assisten() {
        return $this->belongsTo(User::class, 'user_id_assisten');
    }

    public function absensi() {
        return $this->hasMany(Absensi::class);
    }
}
