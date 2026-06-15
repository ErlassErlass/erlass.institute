<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCard extends Model
{
    use HasFactory;

    protected $table = 'report_cards';

    protected $fillable = [
        'siswa_id',
        'ekstrakurikuler_id',
        'ekstrakurikuler_rombel_id',
        'student_score_id',
        'periode',
        'file_path',
        'generated_at',
        'generated_by',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
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
     * Relasi ke model StudentScore.
     */
    public function studentScore(): BelongsTo
    {
        return $this->belongsTo(StudentScore::class, 'student_score_id');
    }

    /**
     * Relasi ke User yang men-generate report.
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
