<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $table = 'holidays';

    protected $fillable = [
        'tanggal',
        'nama',
        'jenis',
        'is_tanggal_merah',
        'tahun',
        'catatan',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'is_tanggal_merah' => 'boolean',
    ];

    // ─────────────────────────────────────────────
    // Konstanta Jenis
    // ─────────────────────────────────────────────
    const JENIS_LIBUR_NASIONAL = 'libur_nasional';
    const JENIS_CUTI_BERSAMA   = 'cuti_bersama';
    const JENIS_LIBUR_AGAMA    = 'libur_agama';
    const JENIS_HARI_BESAR     = 'hari_besar';

    // ─────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────

    /** Filter berdasarkan tahun */
    public function scopeByYear(Builder $query, int $year): Builder
    {
        return $query->where('tahun', $year);
    }

    /** Filter hanya tanggal merah */
    public function scopeTanggalMerah(Builder $query): Builder
    {
        return $query->where('is_tanggal_merah', true);
    }

    /** Filter berdasarkan jenis */
    public function scopeByJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis', $jenis);
    }

    /** Filter dalam range tanggal */
    public function scopeInDateRange(Builder $query, string $start, string $end): Builder
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    /** Label jenis yang ramah dibaca */
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            self::JENIS_LIBUR_NASIONAL => 'Libur Nasional',
            self::JENIS_CUTI_BERSAMA   => 'Cuti Bersama',
            self::JENIS_LIBUR_AGAMA    => 'Libur Agama',
            self::JENIS_HARI_BESAR     => 'Hari Besar',
            default                    => ucfirst($this->jenis),
        };
    }

    /** Warna badge untuk UI */
    public function getBadgeColorAttribute(): string
    {
        return match ($this->jenis) {
            self::JENIS_LIBUR_NASIONAL => 'danger',
            self::JENIS_CUTI_BERSAMA   => 'warning',
            self::JENIS_LIBUR_AGAMA    => 'info',
            self::JENIS_HARI_BESAR     => 'secondary',
            default                    => 'secondary',
        };
    }

    // ─────────────────────────────────────────────
    // Static Helper
    // ─────────────────────────────────────────────

    /**
     * Cek apakah suatu tanggal adalah hari libur nasional.
     * Catatan: cuti_bersama TIDAK dihitung sebagai hari libur blocking.
     */
    public static function isHoliday(string|\DateTimeInterface $date): bool
    {
        $d = $date instanceof \DateTimeInterface
            ? Carbon::instance($date)->toDateString()
            : Carbon::parse($date)->toDateString();

        return static::whereDate('tanggal', $d)
            ->where('jenis', '!=', self::JENIS_CUTI_BERSAMA)
            ->exists();
    }

    /**
     * Ambil holiday pada tanggal tertentu (atau null jika bukan hari libur).
     * Catatan: cuti_bersama TIDAK dikembalikan — bukan libur blocking.
     */
    public static function getOnDate(string|\DateTimeInterface $date): ?self
    {
        $d = $date instanceof \DateTimeInterface
            ? Carbon::instance($date)->toDateString()
            : Carbon::parse($date)->toDateString();

        return static::whereDate('tanggal', $d)
            ->where('jenis', '!=', self::JENIS_CUTI_BERSAMA)
            ->first();
    }
}
