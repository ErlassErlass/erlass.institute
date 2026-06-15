<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollBatch extends Model
{
    use HasFactory;

    protected $table = 'payroll_batches';

    protected $fillable = [
        'code',
        'periode',
        'status',
        'notes',
        'processed_at',
        'processed_by',
        'paid_at',
        'paid_by',
    ];

    protected $casts = [
        'periode' => 'date',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_batch_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
