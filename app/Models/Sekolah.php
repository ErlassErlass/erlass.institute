<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'sekolah';

    /**
     * Mendefinisikan 'kodlan' sebagai Primary Key.
     */
    protected $primaryKey = 'kodlan';

    /**
     * Memberitahu Laravel bahwa Primary Key ini bukan angka auto-increment.
     */
    public $incrementing = false;

    /**
     * Memberitahu Laravel bahwa tipe data Primary Key adalah string.
     */
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi secara massal, cocok dengan daftar kolom Anda.
     */
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

    /**
     * Relasi ke model Siswa.
     * Satu Sekolah memiliki banyak Siswa.
     */
    public function siswa()
    {
        // Foreign key di tabel 'siswa' adalah 'sekolah_kodlan'
        // Local key (primary key) di tabel 'sekolah' ini adalah 'kodlan'
        return $this->hasMany(Siswa::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke model Ekstrakurikuler.
     * Satu Sekolah memiliki banyak Program Ekstrakurikuler.
     */
    public function ekstrakurikuler()
    {
        return $this->hasMany(Ekstrakurikuler::class, 'sekolah_kodlan', 'kodlan');
    }
}
