<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class SiswaEkstrakurikuler extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     */
    protected $table = 'siswa_ekstrakurikuler';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'siswa_id',
        'ekstrakurikuler_id',
        'ekstrakurikuler_rombel_id',
        'status',
        'tanggal_daftar',
        'tanggal_keluar',
        'alasan_keluar',
        'catatan',
        'created_by',
        'updated_by',
    ];

    /**
     * Casting atribut ke tipe data yang sesuai.
     */
    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_keluar' => 'date',
    ];

    /**
     * Konstanta untuk status enrollment
     */
    const STATUS_AKTIF = 'aktif';

    const STATUS_LULUS = 'lulus';

    const STATUS_KELUAR = 'keluar';

    const STATUS_PINDAH = 'pindah';

    const STATUS_NONAKTIF = 'nonaktif';

    /**
     * Relasi ke model Siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke model Ekstrakurikuler.
     */
    public function ekstrakurikuler(): BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }

    /**
     * Relasi ke model EkstrakurikulerRombel.
     */
    public function rombel(): BelongsTo
    {
        return $this->belongsTo(EkstrakurikulerRombel::class, 'ekstrakurikuler_rombel_id');
    }

    /**
     * Relasi ke User yang membuat record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User yang mengupdate record.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk filter berdasarkan status.
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter enrollment aktif.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    /**
     * Scope untuk filter berdasarkan ekstrakurikuler.
     */
    public function scopeByEkstrakurikuler($query, int $ekstrakurikulerId)
    {
        return $query->where('ekstrakurikuler_id', $ekstrakurikulerId);
    }

    /**
     * Scope untuk filter berdasarkan rombel.
     */
    public function scopeByRombel($query, int $rombelId)
    {
        return $query->where('ekstrakurikuler_rombel_id', $rombelId);
    }

    /**
     * Scope untuk filter enrollment dalam rentang tanggal.
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_daftar', [$startDate, $endDate]);
    }

    /**
     * Accessor untuk mendapatkan label status.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_LULUS => 'Lulus',
            self::STATUS_KELUAR => 'Keluar',
            self::STATUS_PINDAH => 'Pindah Rombel',
            self::STATUS_NONAKTIF => 'Non Aktif',
        ];

        return $labels[$this->status] ?? 'Unknown';
    }

    /**
     * Accessor untuk mendapatkan durasi mengikuti program (dalam hari).
     */
    public function getDurasiEnrollmentAttribute(): int
    {
        $endDate = $this->tanggal_keluar ?? now();

        return $this->tanggal_daftar->diffInDays($endDate);
    }

    /**
     * Cek apakah enrollment masih aktif.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_AKTIF;
    }

    /**
     * Cek apakah enrollment sudah berakhir.
     */
    public function isEnded(): bool
    {
        return in_array($this->status, [self::STATUS_LULUS, self::STATUS_KELUAR]);
    }

    /**
     * Method untuk mengaktifkan enrollment.
     */
    public function activate(): bool
    {
        if ($this->status === self::STATUS_NONAKTIF) {
            $this->status = self::STATUS_AKTIF;

            return $this->save();
        }

        return false;
    }

    /**
     * Method untuk menonaktifkan enrollment.
     */
    public function deactivate(?string $alasan = null): bool
    {
        if ($this->status === self::STATUS_AKTIF) {
            $this->status = self::STATUS_NONAKTIF;
            if ($alasan) {
                $this->catatan = $alasan;
            }

            return $this->save();
        }

        return false;
    }

    /**
     * Method untuk mengeluarkan siswa dari program.
     */
    public function withdraw(?string $alasan = null): bool
    {
        if (in_array($this->status, [self::STATUS_AKTIF, self::STATUS_NONAKTIF])) {
            $this->status = self::STATUS_KELUAR;
            $this->tanggal_keluar = now();
            $this->alasan_keluar = $alasan;

            return $this->save();
        }

        return false;
    }

    /**
     * Method untuk memindahkan siswa ke rombel lain.
     */
    public function transfer(int $newRombelId, ?string $alasan = null): bool
    {
        if ($this->status === self::STATUS_AKTIF) {
            // Cek apakah rombel baru ada dalam ekstrakurikuler yang sama
            $newRombel = EkstrakurikulerRombel::where('id', $newRombelId)
                ->where('ekstrakurikuler_id', $this->ekstrakurikuler_id)
                ->first();

            if (! $newRombel) {
                return false;
            }

            $oldRombelId = $this->ekstrakurikuler_rombel_id;

            return DB::transaction(function () use ($newRombelId, $oldRombelId, $alasan) {
                // 1. Update record saat ini menjadi status pindah
                $this->status = self::STATUS_PINDAH;
                $this->tanggal_keluar = now();
                $this->alasan_keluar = 'Pindah ke Rombel ID: ' . $newRombelId;
                $this->catatan = 'Pindah ke Rombel ID: ' . $newRombelId . '. ' . ($alasan ?? '');
                $this->save();

                // 2. Buat record baru untuk rombel tujuan
                self::create([
                    'siswa_id' => $this->siswa_id,
                    'ekstrakurikuler_id' => $this->ekstrakurikuler_id,
                    'ekstrakurikuler_rombel_id' => $newRombelId,
                    'status' => self::STATUS_AKTIF,
                    'tanggal_daftar' => now(),
                    'catatan' => 'Pindahan dari Rombel ID: ' . $oldRombelId,
                ]);

                return true;
            });
        }

        return false;
    }

    /**
     * Method untuk menandai siswa lulus.
     */
    public function graduate(): bool
    {
        if ($this->status === self::STATUS_AKTIF) {
            $this->status = self::STATUS_LULUS;
            $this->tanggal_keluar = now();

            return $this->save();
        }

        return false;
    }

    /**
     * Boot method untuk handle events.
     */
    protected static function boot()
    {
        parent::boot();

        // Set created_by dan updated_by otomatis
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->id();
                $model->updated_by = auth()->id();
            }
        });

        static::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->id();
            }
        });

        // Auto-update jumlah siswa di rombel ketika enrollment berubah
        static::created(function ($model) {
            if ($model->status === self::STATUS_AKTIF) {
                $model->rombel?->incrementJumlahSiswa();
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('status')) {
                $originalStatus = $model->getOriginal('status');
                $newStatus = $model->status;

                // Jika dari non-aktif ke aktif
                if ($originalStatus !== self::STATUS_AKTIF && $newStatus === self::STATUS_AKTIF) {
                    $model->rombel?->incrementJumlahSiswa();
                }
                // Jika dari aktif ke non-aktif
                elseif ($originalStatus === self::STATUS_AKTIF && $newStatus !== self::STATUS_AKTIF) {
                    $model->rombel?->decrementJumlahSiswa();
                }
            }
        });

        static::deleted(function ($model) {
            if ($model->status === self::STATUS_AKTIF) {
                $model->rombel?->decrementJumlahSiswa();
            }
        });
    }
}
