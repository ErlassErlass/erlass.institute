<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\SchoolCalendar;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarService
{
    // ─────────────────────────────────────────────
    // KALENDER NASIONAL
    // ─────────────────────────────────────────────

    /**
     * Cek apakah tanggal adalah hari libur nasional.
     * Cuti bersama TIDAK dihitung sebagai libur — sesi tetap bisa berjalan.
     */
    public function isNationalHoliday(string|\DateTimeInterface $date): bool
    {
        return Holiday::isHoliday($date); // Sudah exclude cuti_bersama di model
    }

    /**
     * Ambil detail hari libur nasional pada tanggal tertentu.
     * Cuti bersama TIDAK dikembalikan (bukan libur blocking).
     */
    public function getHolidayOnDate(string|\DateTimeInterface $date): ?Holiday
    {
        return Holiday::getOnDate($date); // Sudah exclude cuti_bersama di model
    }

    /**
     * Ambil semua hari libur nasional dalam range tanggal tertentu.
     * Termasuk cuti_bersama untuk keperluan tampilan kalender,
     * namun cuti_bersama tidak memblok penjadwalan sesi.
     */
    public function getHolidaysInRange(string $start, string $end): Collection
    {
        return Holiday::inDateRange($start, $end)
            ->orderBy('tanggal')
            ->get();
    }

    /**
     * Ambil semua hari libur untuk suatu tahun.
     */
    public function getHolidaysByYear(int $year): Collection
    {
        return Holiday::byYear($year)
            ->orderBy('tanggal')
            ->get();
    }

    /**
     * Ambil semua tanggal merah (is_tanggal_merah = true) dalam range.
     * Mengembalikan collection of date strings.
     */
    public function getTanggalMerahInRange(string $start, string $end): Collection
    {
        return Holiday::inDateRange($start, $end)
            ->tanggalMerah()
            ->pluck('tanggal')
            ->map(fn ($d) => Carbon::parse($d)->toDateString());
    }

    // ─────────────────────────────────────────────
    // KALENDER SEKOLAH
    // ─────────────────────────────────────────────

    /**
     * Cek apakah suatu sekolah memiliki event blocking pada tanggal tertentu.
     */
    public function isBlockingForSchool(string $kodlan, string|\DateTimeInterface $date): bool
    {
        return SchoolCalendar::isBlockingForSchool($kodlan, $date);
    }

    /**
     * Ambil event kalender sekolah yang aktif pada tanggal tertentu.
     */
    public function getSchoolEventsOnDate(string $kodlan, string|\DateTimeInterface $date): Collection
    {
        return SchoolCalendar::activeOn($date)->bySekolah($kodlan)->get();
    }

    /**
     * Ambil semua event kalender sekolah dalam range tanggal.
     */
    public function getSchoolCalendarInRange(string $kodlan, string $start, string $end): Collection
    {
        return SchoolCalendar::bySekolah($kodlan)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_mulai', [$start, $end])
                  ->orWhereBetween('tanggal_selesai', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('tanggal_mulai', '<=', $start)
                         ->where('tanggal_selesai', '>=', $end);
                  });
            })
            ->orderBy('tanggal_mulai')
            ->get();
    }

    // ─────────────────────────────────────────────
    // GABUNGAN — Safe Scheduling
    // ─────────────────────────────────────────────

    /**
     * Cek apakah tanggal aman untuk dijadwalkan sesi ekskul.
     * Aman = bukan libur nasional DAN tidak ada event blocking di sekolah terkait.
     */
    public function isSafeToSchedule(string|\DateTimeInterface $date, string $kodlan): bool
    {
        if ($this->isNationalHoliday($date)) {
            return false;
        }

        if ($this->isBlockingForSchool($kodlan, $date)) {
            return false;
        }

        return true;
    }

    /**
     * Cek apakah keesokan hari adalah hari libur nasional.
     * Digunakan oleh H-1 reminder untuk skip pengiriman jika hari ini libur.
     */
    public function isTomorrowNationalHoliday(): bool
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        return $this->isNationalHoliday($tomorrow);
    }

    /**
     * Dapatkan ringkasan kalender untuk UI (gabungan nasional + sekolah).
     * Mengembalikan array associative tanggal => keterangan.
     */
    public function getCalendarSummary(string $kodlan, string $start, string $end): array
    {
        $result = [];

        // Nasional
        $nationals = $this->getHolidaysInRange($start, $end);
        foreach ($nationals as $h) {
            $key = Carbon::parse($h->tanggal)->toDateString();
            $result[$key][] = [
                'nama'   => $h->nama,
                'jenis'  => $h->jenis_label,
                'tipe'   => 'nasional',
                'warna'  => $h->badge_color,
            ];
        }

        // Sekolah
        $schoolEvents = $this->getSchoolCalendarInRange($kodlan, $start, $end);
        foreach ($schoolEvents as $event) {
            $current = Carbon::parse($event->tanggal_mulai);
            $end_dt  = Carbon::parse($event->tanggal_selesai);

            while ($current->lte($end_dt)) {
                $key = $current->toDateString();
                if ($key >= $start && $key <= $end) {
                    $result[$key][] = [
                        'nama'   => $event->nama,
                        'jenis'  => $event->jenis_label,
                        'tipe'   => 'sekolah',
                        'warna'  => 'primary',
                        'blocking' => $event->is_blocking,
                    ];
                }
                $current->addDay();
            }
        }

        return $result;
    }
}
