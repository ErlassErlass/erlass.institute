<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItemSession extends Model
{
    use HasFactory;

    protected $table = 'payroll_item_session';

    protected $fillable = [
        'payroll_item_id',
        'ekstrakurikuler_session_id',
        'user_id',
        'role', // 'utama' | 'asisten'
        'base_fee',
        'transport_fee',
        'penalty_fee',
        'bonus_fee',
        'net_fee',
        'override_fee',
    ];

    protected $casts = [
        'base_fee' => 'decimal:2',
        'transport_fee' => 'decimal:2',
        'penalty_fee' => 'decimal:2',
        'bonus_fee' => 'decimal:2',
        'net_fee' => 'decimal:2',
        'override_fee' => 'decimal:2',
    ];

    public function payrollItem(): BelongsTo
    {
        return $this->belongsTo(PayrollItem::class, 'payroll_item_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerSession::class, 'ekstrakurikuler_session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
