<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPortfolio extends Model
{
    use HasFactory;

    protected $table = 'student_portfolios';

    protected $fillable = [
        'siswa_id',
        'ekstrakurikuler_id',
        'ekstrakurikuler_rombel_id',
        'tipe_file',
        'judul',
        'deskripsi',
        'file_path',
        'url_eksternal',
        'pertemuan_ke',
        'created_by',
    ];

    /**
     * Relasi ke model Siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke model Ekstrakurikuler.
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class, 'ekstrakurikuler_id');
    }

    /**
     * Relasi ke model EkstrakurikulerRombel.
     */
    public function ekstrakurikulerRombel(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerRombel::class, 'ekstrakurikuler_rombel_id');
    }

    /**
     * Relasi ke User pembuat portfolio.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get file icon helper based on type of file
     */
    public function getFileIconAttribute(): string
    {
        return match ($this->tipe_file) {
            'sb3' => 'file-code-o',
            'hex' => 'microchip',
            'python' => 'terminal',
            'video' => 'file-video-o',
            'gambar' => 'file-image-o',
            default => 'file-o'
        };
    }
}
