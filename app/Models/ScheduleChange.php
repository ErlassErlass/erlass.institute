<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleChange extends Model
{
    use HasFactory;

    protected $table = 'schedule_changes';

    protected $fillable = [
        'ekstrakurikuler_session_id',
        'requested_by',
        'original_date',
        'original_start_time',
        'original_end_time',
        'proposed_date',
        'proposed_start_time',
        'proposed_end_time',
        'reason',
        'academic_approver_id',
        'academic_approved_at',
        'school_pic_approver_id',
        'school_pic_approved_at',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'original_date' => 'date',
        'proposed_date' => 'date',
        'academic_approved_at' => 'datetime',
        'school_pic_approved_at' => 'datetime',
    ];

    /**
     * Relasi ke model EkstrakurikulerSession.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerSession::class, 'ekstrakurikuler_session_id');
    }

    /**
     * Relasi ke User pembuat pengajuan.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Relasi ke User akademis Erlass yang memvalidasi.
     */
    public function academicApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'academic_approver_id');
    }

    /**
     * Relasi ke SchoolPic yang menyetujui jadwal dari pihak sekolah.
     */
    public function schoolPicApprover(): BelongsTo
    {
        return $this->belongsTo(SchoolPic::class, 'school_pic_approver_id');
    }
}
