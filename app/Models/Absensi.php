<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'laporan_mengajar_id',
        'siswa_id',
        'hadir',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'hadir' => 'boolean',
    ];

    /**
     * Satu Absensi milik dari satu LaporanMengajar.
     */
    public function laporanMengajar()
    {
        return $this->belongsTo(LaporanMengajar::class, 'laporan_mengajar_id');
    }

    /**
     * Satu Absensi milik dari satu Siswa.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
