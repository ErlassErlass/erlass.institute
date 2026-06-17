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
        'total_base_fee',
        'total_product_bonus',
        'total_penalty',
        'total_bonus',
        'total_transport_fee',
        'net_salary',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_base_fee' => 'decimal:2',
        'total_product_bonus' => 'decimal:2',
        'total_penalty' => 'decimal:2',
        'total_bonus' => 'decimal:2',
        'total_transport_fee' => 'decimal:2',
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

    public function sessions(): HasMany
    {
        return $this->hasMany(EkstrakurikulerSession::class, 'payroll_item_id');
    }
}
