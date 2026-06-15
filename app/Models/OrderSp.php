<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
        'approved_by',
        'approved_at',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected $casts = [
        'tanggal_sp' => 'date',
        'tanggal_mulai_rencana' => 'date',
        'jumlah_peserta_estimasi' => 'integer',
        'jumlah_pertemuan' => 'integer',
        'approved_at' => 'datetime',
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

    // Alias for consistency
    public function items(): HasMany
    {
        return $this->orderItems();
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

    /**
     * Relasi ke model User yang menyetujui.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Setujui SP dan otomatis generate program Ekstrakurikuler + Rombel dasar.
     *
     * @param  \App\Models\User  $user  Admin yang menyetujui.
     * @return void
     */
    public function approve(User $user): void
    {
        DB::transaction(function () use ($user) {
            // 1. Update status SP
            $this->update([
                'status' => self::STATUS_DISETUJUI,
                'approved_by' => $user->id,
                'approved_at' => now(),
                'updated_by' => $user->id,
            ]);

            // 2. Auto-generate program Ekstrakurikuler untuk setiap item produk
            $this->loadMissing('orderItems.product');

            foreach ($this->orderItems as $item) {
                $product = $item->product;
                if (!$product) {
                    continue;
                }

                Ekstrakurikuler::create([
                    'kategori_program' => $product->nama_produk,
                    'sekolah_kodlan' => $this->sekolah_kodlan,
                    'total_siswa' => $this->jumlah_peserta_estimasi,
                    'total_pertemuan' => $this->jumlah_pertemuan,
                    'tanggal_mulai' => $this->tanggal_mulai_rencana,
                    'user_id_sales' => $this->salesman?->user_id,
                    'jenis_pembayaran' => Ekstrakurikuler::PEMBAYARAN_PER_SISWA_BULAN,
                    'koneksi_internet' => Ekstrakurikuler::FASILITAS_TIDAK_DIKETAHUI,
                    'proyektor' => Ekstrakurikuler::FASILITAS_TIDAK_DIKETAHUI,
                    'kabel_hdmi' => Ekstrakurikuler::FASILITAS_TIDAK_DIKETAHUI,
                    'kabel_vga' => Ekstrakurikuler::FASILITAS_TIDAK_DIKETAHUI,
                    'frekuensi' => Ekstrakurikuler::FREKUENSI_MINGGUAN,
                    'status' => Ekstrakurikuler::STATUS_DIAJUKAN,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
            }
        });
    }

    /**
     * Cek apakah SP bisa disetujui.
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_MENUNGGU_VALIDASI;
    }
}

