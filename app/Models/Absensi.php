<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = [
        'laporan_mengajar_id','siswa_id','hadir','catatan',
    ];

    public function laporanMengajar()
    {
        return $this->belongsTo(LaporanMengajar::class, 'laporan_mengajar_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function absensis() // Gunakan nama plural untuk relasi hasMany
    {
        return $this->hasMany(Absensi::class, 'laporan_mengajar_id');
    }
}
