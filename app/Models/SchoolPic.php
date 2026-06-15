<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolPic extends Model
{
    use HasFactory;

    protected $table = 'school_pics';

    protected $fillable = [
        'sekolah_kodlan',
        'nama',
        'kontak',
        'email',
        'jabatan',
        'user_id',
    ];

    /**
     * Relasi ke model Sekolah.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke model User (opsional login PIC).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
