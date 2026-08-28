<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollItem extends Model
{
    use HasFactory;

    protected $table = 'payroll_items';

    protected $fillable = [
        'payroll_batch_id',
        'user_id_instruktur',
        'total_sessions',
        'total_sessions_utama',
        'total_sessions_asisten',
        'total_base_fee',
        'total_asisten_fee',
        'total_product_bonus',
        'total_penalty',
        'total_bonus',
        'total_transport_fee',
        'total_gross_salary',
        'tax_rate',
        'tax_amount',
        'net_salary',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_base_fee' => 'decimal:2',
        'total_asisten_fee' => 'decimal:2',
        'total_product_bonus' => 'decimal:2',
        'total_penalty' => 'decimal:2',
        'total_bonus' => 'decimal:2',
        'total_transport_fee' => 'decimal:2',
        'total_gross_salary' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PayrollBatch::class, 'payroll_batch_id');
    }

    public function instruktur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id_instruktur');
    }

    public function payrollItemSessions(): HasMany
    {
        return $this->hasMany(PayrollItemSession::class, 'payroll_item_id');
    }

    public function sessions()
    {
        return $this->belongsToMany(
            EkstrakurikulerSession::class,
            'payroll_item_session',
            'payroll_item_id',
            'ekstrakurikuler_session_id'
        )->withPivot([
            'role',
            'base_fee',
            'transport_fee',
            'penalty_fee',
            'bonus_fee',
            'net_fee',
            'override_fee'
        ])->withTimestamps();
    }
}

