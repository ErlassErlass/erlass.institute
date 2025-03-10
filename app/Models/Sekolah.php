<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model {
        protected $table = 'sekolah'; // Add this line

    protected $primaryKey = 'kodlan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kodlan',
        'namasekolah',
        'rank',
        'jenjang',
        'sub_jenjang',
        'status',
        'pd',
        'kec',
        'kotkab',
        'kota',
        'provinsi',
    ];

    // Relationships
    public function siswa() {
        return $this->hasMany(Siswa::class, 'sekolah_kodlan');
    }

    public function laporanMengajar() {
        return $this->hasMany(LaporanMengajar::class, 'sekolah_kota', 'kotkab');
    }
}
