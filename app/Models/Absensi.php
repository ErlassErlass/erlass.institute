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
        return $this->belongsTo(LaporanMengajar::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
