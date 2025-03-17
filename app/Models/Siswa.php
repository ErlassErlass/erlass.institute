<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
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
}
