<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     */
    protected $table = 'products';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'jenis',
        'harga',
        'durasi_bulan',
        'jenis_kegiatan',
        'standar_durasi_menit',
        'tanggal',
        'is_aktif',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected $casts = [
        'harga' => 'decimal:2',
        'durasi_bulan' => 'integer',
        'standar_durasi_menit' => 'integer',
        'tanggal' => 'date',
        'is_aktif' => 'boolean',
    ];

    /**
     * Relasi ke model OrderItem.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }
}
