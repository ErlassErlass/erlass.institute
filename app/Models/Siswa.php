<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model {
    protected $fillable = [
        'nama_lengkap',
        'nisn',
        'sekolah_kodlan',
        'rombel',
    ];

    // Relationships
    public function sekolah() {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan');
    }

    public function absensi() {
        return $this->hasMany(Absensi::class);
    }
}