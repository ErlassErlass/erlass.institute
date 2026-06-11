<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderSp extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     */
    protected $table = 'orders_sp';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'nomor_sp',
        'tanggal_sp',
        'sekolah_kodlan',
        'salesman_id',
        'jumlah_peserta_estimasi',
        'jenis_kegiatan',
        'lokasi_pembelajaran',
        'tanggal_mulai_rencana',
        'jumlah_pertemuan',
        'catatan_khusus',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected $casts = [
        'tanggal_sp' => 'date',
        'tanggal_mulai_rencana' => 'date',
        'jumlah_peserta_estimasi' => 'integer',
        'jumlah_pertemuan' => 'integer',
    ];

    /**
     * Konstanta untuk status SP.
     */
    const STATUS_DRAFT = 'draft';
    const STATUS_MENUNGGU_VALIDASI = 'menunggu_validasi';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_BERJALAN = 'berjalan';
    const STATUS_SELESAI = 'selesai';
    const STATUS_BATAL = 'batal';

    /**
     * Relasi ke model Sekolah.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke model Salesman.
     */
    public function salesman(): BelongsTo
    {
        return $this->belongsTo(Salesman::class, 'salesman_id');
    }

    /**
     * Relasi ke model OrderItem.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_sp_id');
    }

    /**
     * Relasi ke model User pencipta.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke model User pembaru.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
