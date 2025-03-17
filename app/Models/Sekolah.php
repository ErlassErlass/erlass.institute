<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    protected $table = 'sekolah'; // Specify the table name explicitly

    protected $primaryKey = 'kodlan'; // Define primary key (string)
    public $incrementing = false; // Disable auto-increment
    protected $keyType = 'string'; // Primary key type is string

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

   // app/Models/Sekolah.php
public function siswa()
{
    return $this->hasMany(Siswa::class, 'sekolah_kodlan', 'kodlan');
}

    // Relationship: Schools have many teaching reports (via kotkab)
    public function laporanMengajar()
    {
        return $this->hasMany(LaporanMengajar::class, 'sekolah_kota', 'kotkab');
    }
}