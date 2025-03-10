<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ADD THIS
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
       use HasFactory; // ADD THIS LINE
    use Notifiable;

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'tanggal_lahir',
        'no_telephone',
        'status',
        'agama',
        'pend_terakhir',
        'kompetensi_1',
        'kompetensi_2',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    // Define relationships
    public function laporanMengajar() {
        return $this->hasMany(LaporanMengajar::class, 'user_id_instruktur');
    }
}
