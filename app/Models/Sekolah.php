<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    /**
     * Boot the model.
     */
    protected static function booted()
    {
        static::saved(function () {
            \Cache::forget('sekolah_available_regions');
            \Cache::forget('sekolah_available_cities');
        });

        static::deleted(function () {
            \Cache::forget('sekolah_available_regions');
            \Cache::forget('sekolah_available_cities');
        });
    }

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'sekolah';

    const SKEMA_BULANAN          = 'bulanan';
    const SKEMA_SEMESTER         = 'semester';
    const SKEMA_TAHUNAN          = 'tahunan';
    const SKEMA_PER_4_PERTEMUAN  = 'per_4_pertemuan';
    const SKEMA_CSR_REGULER_SOGA = 'csr_reguler_soga';

    /**
     * Mendefinisikan 'kodlan' sebagai Primary Key.
     */
    protected $primaryKey = 'kodlan';

    /**
     * Memberitahu Laravel bahwa Primary Key ini bukan angka auto-increment.
     */
    public $incrementing = false;

    /**
     * Memberitahu Laravel bahwa tipe data Primary Key adalah string.
     */
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi secara massal, cocok dengan daftar kolom Anda.
     */
    protected $fillable = [
        'kodlan',
        'namasekolah',
        'rank',
        'jenjang',
        'sub_jenjang',
        'status',
        'pd',
        'kec',
        'kotkab',
        'kota',
        'provinsi',
        'alamat',
        'alamat_lengkap',
        'lokasi_default',
        'kustom_transport_fee',
        'jarak_km',
        'is_sekolah_bayar_instruktur',
        'skema_tagihan',
    ];

    /**
     * Casting tipe data kolom.
     */
    protected $casts = [
        'kustom_transport_fee'        => 'decimal:2',
        'jarak_km'                    => 'decimal:2',
        'is_sekolah_bayar_instruktur' => 'boolean',
    ];

    /**
     * Label human-readable untuk skema tagihan.
     */
    public function skemaTagihanLabel(): string
    {
        return match ($this->skema_tagihan ?? 'bulanan') {
            'bulanan'          => 'Bulanan (Kalender)',
            'semester'         => 'Per Semester (~16 sesi)',
            'tahunan'          => 'Per Tahun (~32 sesi)',
            'per_4_pertemuan'  => 'Per 4 Pertemuan (Rolling Batch)',
            'csr_reguler_soga' => 'CSR Reguler SOGA (Solidaritas Erlangga)',
            default            => 'Bulanan (Kalender)',
        };
    }

    /**
     * Relasi ke model Siswa.
     * Satu Sekolah memiliki banyak Siswa.
     */
    public function siswa()
    {
        // Foreign key di tabel 'siswa' adalah 'sekolah_kodlan'
        // Local key (primary key) di tabel 'sekolah' ini adalah 'kodlan'
        return $this->hasMany(Siswa::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke model Ekstrakurikuler.
     * Satu Sekolah memiliki banyak Program Ekstrakurikuler.
     */
    public function ekstrakurikuler()
    {
        return $this->hasMany(Ekstrakurikuler::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Alias plural untuk relasi ke Ekstrakurikuler.
     */
    public function ekstrakurikulers()
    {
        return $this->hasMany(Ekstrakurikuler::class, 'sekolah_kodlan', 'kodlan');
    }

    /**
     * Relasi ke Ekstrakurikuler yang dapat di-invoice (Hanya Ekskul dan Pelatihan).
     */
    public function invoiceableEkstrakurikulers()
    {
        return $this->hasMany(Ekstrakurikuler::class, 'sekolah_kodlan', 'kodlan')->invoiceable();
    }

    /**
     * Accessor untuk mengembalikan nama lokasi lengkap (Kecamatan & Kota/Kabupaten).
     */
    public function getFormattedLokasiAttribute(): string
    {
        $parts = [];

        if (!empty($this->kec)) {
            $parts[] = 'Kec. ' . $this->kec;
        }

        if (!empty($this->kota)) {
            $kotaStr = \Illuminate\Support\Str::title($this->kota);
            if (!\Illuminate\Support\Str::startsWith(strtolower($kotaStr), ['kota', 'kab'])) {
                $prefix = (strtolower($this->kotkab ?? '') === 'kabupaten') ? 'Kab. ' : 'Kota ';
                $kotaStr = $prefix . $kotaStr;
            }
            $parts[] = $kotaStr;
        } elseif (!empty($this->kotkab) && !in_array(strtolower($this->kotkab), ['kota', 'kabupaten'])) {
            $parts[] = $this->kotkab;
        }

        if (empty($parts) && !empty($this->provinsi)) {
            $parts[] = $this->provinsi;
        }

        return !empty($parts) ? implode(', ', $parts) : ($this->kotkab ?? 'Lokasi N/A');
    }

    /**
     * Relasi ke InvoiceApproval melalui Ekstrakurikuler → Rombel.
     * Digunakan untuk laporan rekap invoice per sekolah.
     */
    public function invoiceApprovals()
    {
        return $this->hasManyThrough(
            \App\Models\InvoiceApproval::class,
            Ekstrakurikuler::class,
            'sekolah_kodlan', // FK di ekstrakurikuler
            'ekstrakurikuler_rombel_id', // FK di invoice_approvals (via rombel)
            'kodlan',         // Local key di sekolah
            'id'              // Local key di ekstrakurikuler
        );
    }
}
