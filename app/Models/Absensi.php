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
        'status',
        'hadir',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Backward compatibility accessor for 'hadir'
     */
    public function getHadirAttribute(): bool
    {
        return ($this->status ?? 'alpha') === 'hadir';
    }

    /**
     * Backward compatibility mutator for 'hadir'
     */
    public function setHadirAttribute($value): void
    {
        $this->attributes['status'] = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'hadir' : 'alpha';
    }

    protected $casts = [
        // status is a string/enum, no cast needed
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
