<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     */
    protected $table = 'order_items';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'order_sp_id',
        'product_id',
        'harga_satuan',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected $casts = [
        'harga_satuan' => 'decimal:2',
    ];

    /**
     * Relasi ke model OrderSp.
     */
    public function orderSp(): BelongsTo
    {
        return $this->belongsTo(OrderSp::class, 'order_sp_id');
    }

    /**
     * Relasi ke model Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
