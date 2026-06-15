<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryRate extends Model
{
    use HasFactory;

    protected $table = 'salary_rates';

    protected $fillable = [
        'level',
        'base_rate',
        'product_category',
        'product_bonus',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'base_rate' => 'decimal:2',
        'product_bonus' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
