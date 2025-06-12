<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $fillable = [
        'laporan_mengajar_id','siswa_id','hadir',
        'e_signature_instruktur','e_signature_pic',
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
