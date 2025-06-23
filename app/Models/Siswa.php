<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ✅ 1. TAMBAHKAN INI


class Siswa extends Model
{
    use HasFactory; // ✅ 2. TAMBAHKAN INI

    protected $table = 'siswa';
    protected $fillable = [
        'nama_lengkap',
        'nisn',
        'sekolah_kodlan',
        'rombel',
    ];

    // app/Models/Siswa.php
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }
}
