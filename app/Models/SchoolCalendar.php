<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolCalendar extends Model
{
    protected $table = 'school_calendars';

    protected $fillable = [
        'sekolah_kodlan',
        'tanggal_mulai',
        'tanggal_selesai',
        'nama',
        'jenis',
        'is_blocking',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai'  => 'date',
        'tanggal_selesai' => 'date',
        'is_blocking'    => 'boolean',
    ];

    // ─────────────────────────────────────────────
    // Konstanta Jenis
    // ─────────────────────────────────────────────
    const JENIS_LIBUR_SEKOLAH    = 'libur_sekolah';
    const JENIS_UJIAN            = 'ujian';
    const JENIS_KEGIATAN_SEKOLAH = 'kegiatan_sekolah';
    const JENIS_LAINNYA          = 'lainnya';

    // ─────────────────────────────────────────────
    // Relasi
    // ─────────────────────────────────────────────

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class, 'sekolah_kodlan', 'kodlan');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─────────────────────────────────────────────
    // Scopes
    // ─────────────────────────────────────────────

    public function scopeBySekolah(Builder $query, string $kodlan): Builder
    {
        return $query->where('sekolah_kodlan', $kodlan);
    }

    public function scopeBlocking(Builder $query): Builder
    {
        return $query->where('is_blocking', true);
    }

    /** Event yang aktif pada suatu tanggal tertentu */
    public function scopeActiveOn(Builder $query, string|\DateTimeInterface $date): Builder
    {
        $d = $date instanceof \DateTimeInterface
            ? Carbon::instance($date)->toDateString()
            : Carbon::parse($date)->toDateString();

        return $query->where('tanggal_mulai', '<=', $d)
                     ->where('tanggal_selesai', '>=', $d);
    }

    /** Untuk sekolah tertentu, cek apakah tanggal di-blok */
    public function scopeBlockingOn(Builder $query, string $kodlan, string|\DateTimeInterface $date): Builder
    {
        return $query->bySekolah($kodlan)->blocking()->activeOn($date);
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            self::JENIS_LIBUR_SEKOLAH    => 'Libur Sekolah',
            self::JENIS_UJIAN            => 'Ujian',
            self::JENIS_KEGIATAN_SEKOLAH => 'Kegiatan Sekolah',
            self::JENIS_LAINNYA          => 'Lainnya',
            default                      => ucfirst($this->jenis),
        };
    }

    public function getDurationDaysAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    // ─────────────────────────────────────────────
    // Static Helper
    // ─────────────────────────────────────────────

    /** Cek apakah suatu sekolah memiliki event blocking pada tanggal tertentu */
    public static function isBlockingForSchool(string $kodlan, string|\DateTimeInterface $date): bool
    {
        return static::blockingOn($kodlan, $date)->exists();
    }
}
