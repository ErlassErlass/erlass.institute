<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\InvoiceApproval;
use App\Models\InvoiceApprovalItem;
use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Hitung jumlah siswa billable dari data absensi sistem.
     * Logika: 
     * - Sesuai aturan penagihan Erlass (USER_GUIDE & SOP), siswa dianggap "Billable" 
     *   jika hadir minimal 2 kali dalam periode 4 pertemuan (>= 2 sesi).
     * - Jika sesi < 4, ambang batas minimal 50% kehadiran (atau min 1).
     * - Fallback: jika tidak ada data absensi individual, gunakan rata-rata jumlah siswa
     *   yang hadir dari LaporanMengajar, atau kuota jumlah siswa terdaftar di rombel.
     */
    public function calculateBillable(int $rombelId, ?int $sesiDari = null, ?int $sesiSampai = null): array
    {
        $sessionQuery = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombelId)
            ->where('status', 'selesai');

        if ($sesiDari !== null) {
            $sessionQuery->where('nomor_pertemuan', '>=', $sesiDari);
        }
        if ($sesiSampai !== null) {
            $sessionQuery->where('nomor_pertemuan', '<=', $sesiSampai);
        }

        $sessionIds   = $sessionQuery->pluck('id')->toArray();
        $sessionCount = count($sessionIds);

        if ($sessionCount === 0) {
            $rombel = EkstrakurikulerRombel::find($rombelId);
            return [
                'billable_count' => $rombel?->jumlah_siswa ?? 0,
                'session_count'  => 0,
            ];
        }

        // Query laporan mengajar yang terkait sesi-sesi ini
        $laporanIds = LaporanMengajar::whereIn('ekstrakurikuler_session_id', $sessionIds)->pluck('id');

        // Cek apakah ada record absensi individual
        $hasIndividualAbsensi = Absensi::whereIn('laporan_mengajar_id', $laporanIds)->exists();

        if ($hasIndividualAbsensi) {
            // Hitung frekuensi kehadiran per siswa
            $attendanceCounts = Absensi::whereIn('laporan_mengajar_id', $laporanIds)
                ->where('status', 'hadir')
                ->select('siswa_id', DB::raw('COUNT(*) as hadir_count'))
                ->groupBy('siswa_id')
                ->get();

            // Batas minimal kehadiran (Erlass Rule):
            // Siswa dianggap billable jika hadir minimal 2 kali dalam periode 4 pertemuan (>= 2 sesi).
            // Jika jumlah sesi kurang dari 4, minimal 50% kehadiran (atau minimal 1).
            $minHadir = ($sessionCount >= 4) ? 2 : max(1, (int) ceil($sessionCount / 2));

            $billableCount = $attendanceCounts->where('hadir_count', '>=', $minHadir)->count();
        } else {
            // Fallback: rata-rata jumlah_siswa_hadir dari LaporanMengajar
            $avgHadir = LaporanMengajar::whereIn('id', $laporanIds)
                ->avg('jumlah_siswa_hadir');

            if ($avgHadir && $avgHadir > 0) {
                $billableCount = (int) round($avgHadir);
            } else {
                $rombel = EkstrakurikulerRombel::find($rombelId);
                $billableCount = $rombel?->jumlah_siswa ?? 0;
            }
        }

        return [
            'billable_count' => $billableCount,
            'session_count'  => $sessionCount,
        ];
    }

    /**
     * Cek apakah sebuah program (Ekstrakurikuler) di sekolah siap ditagih (eligible for billing).
     * 
     * Aturan:
     * 1. Invoice dipisahkan per Program (Ekskul/Pelatihan) di suatu Sekolah.
     * 2. Jika suatu program memiliki beberapa rombel (misal Rombel 1 & Rombel 2), seluruh rombel
     *    dalam program tersebut harus sudah memenuhi target pertemuannya.
     * 3. Skema per_4_pertemuan: Semua rombel dalam program harus selesai 4 sesi di batch ini.
     * 4. Skema bulanan: Sesi terjadwal di bulan tersebut telah diselesaikan.
     */
    public function getEligibleInvoiceForProgram(Ekstrakurikuler $ekskul, ?Carbon $asOfDate = null): ?array
    {
        if ($ekskul->status === Ekstrakurikuler::STATUS_DIBATALKAN || 
            $ekskul->status === Ekstrakurikuler::STATUS_DITOLAK || 
            !$ekskul->isInvoiceable()) {
            return null;
        }

        $ekskul->loadMissing('sekolah');
        $sekolah = $ekskul->sekolah;
        if (!$sekolah) {
            return null;
        }

        $asOfDate = $asOfDate ? $asOfDate->copy() : Carbon::now();

        // Seluruh rombel aktif milik program ini
        $rombels = $ekskul->rombels()
            ->where('status', '!=', EkstrakurikulerRombel::STATUS_DIBATALKAN)
            ->with(['sessions', 'ekstrakurikuler'])
            ->get();

        if ($rombels->isEmpty()) {
            return null;
        }

        $skema = $ekskul->skema_tagihan ?: ($sekolah->skema_tagihan ?? Sekolah::SKEMA_BULANAN);

        // Invoices aktif yang sudah ada untuk program ini
        $existingInvoices = InvoiceApproval::where('sekolah_kodlan', $sekolah->kodlan)
            ->where(function ($q) use ($ekskul, $rombels) {
                $q->where('ekstrakurikuler_id', $ekskul->id)
                  ->orWhereIn('ekstrakurikuler_rombel_id', $rombels->pluck('id'))
                  ->orWhereHas('items', fn($sub) => $sub->whereIn('ekstrakurikuler_rombel_id', $rombels->pluck('id')));
            })
            ->where('status', '!=', InvoiceApproval::STATUS_REJECTED)
            ->get();

        // ─────────────────────────────────────────────────────────────────────
        // 1. Skema Per 4 Pertemuan
        // ─────────────────────────────────────────────────────────────────────
        if ($skema === Sekolah::SKEMA_PER_4_PERTEMUAN) {
            $lastBatch = $existingInvoices->max('periode_nomor') ?? 0;
            $nextBatch = $lastBatch + 1;
            $dari      = ($nextBatch - 1) * 4 + 1;
            $sampai    = $nextBatch * 4;

            $items = [];
            $latestTargetDate = null;

            foreach ($rombels as $rombel) {
                // Cek apakah 4 sesi di batch ini sudah selesai
                $completedInBatch = $rombel->sessions()
                    ->whereBetween('nomor_pertemuan', [$dari, $sampai])
                    ->where('status', 'selesai')
                    ->count();

                if ($completedInBatch < 4) {
                    return null;
                }

                $lastSession = $rombel->sessions()->where('nomor_pertemuan', $sampai)->first();
                $tDate = $lastSession?->tanggal_pelaksanaan 
                    ?? $lastSession?->tanggal_terjadwal 
                    ?? $asOfDate;

                if (is_string($tDate)) {
                    $tDate = Carbon::parse($tDate);
                }

                if (!$latestTargetDate || $tDate->gt($latestTargetDate)) {
                    $latestTargetDate = $tDate;
                }

                $billable = $this->calculateBillable($rombel->id, $dari, $sampai);
                $items[] = [
                    'ekstrakurikuler_id'        => $ekskul->id,
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'rombel_nama'               => $rombel->nama_rombel,
                    'kategori_program'          => $ekskul->kategori_program,
                    'sesi_dari'                 => $dari,
                    'sesi_sampai'               => $sampai,
                    'jumlah_sesi'               => $billable['session_count'],
                    'jumlah_siswa_billable'     => $billable['billable_count'],
                ];
            }

            $latestTargetDate = $latestTargetDate ?? $asOfDate;
            $daysOverdue = max(0, (int) $latestTargetDate->diffInDays($asOfDate, false));

            $bulanNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $bulanTerakhirNama = $bulanNames[$latestTargetDate->month] ?? $latestTargetDate->translatedFormat('F');
            $bulanLaporanTerakhir = "{$bulanTerakhirNama} {$latestTargetDate->year}";

            return [
                'sekolah_kodlan'            => $sekolah->kodlan,
                'sekolah_nama'              => $sekolah->namasekolah,
                'ekstrakurikuler_id'        => $ekskul->id,
                'kategori_program'          => $ekskul->kategori_program,
                'ekstrakurikuler_rombel_id' => $items[0]['ekstrakurikuler_rombel_id'] ?? null,
                'rombel_nama'               => count($items) === 1 ? $items[0]['rombel_nama'] : (count($items) . ' Rombel'),
                'skema_tagihan'             => Sekolah::SKEMA_PER_4_PERTEMUAN,
                'periode_label'             => "Inv Bulan {$nextBatch}",
                'bulan_laporan_terakhir'    => $bulanLaporanTerakhir,
                'periode_nomor'             => $nextBatch,
                'tahun_ajaran'              => $ekskul->tahun_ajaran ?? '2026/2027',
                'sesi_dari'                 => $dari,
                'sesi_sampai'               => $sampai,
                'target_date'               => $latestTargetDate->toDateString(),
                'target_date_formatted'     => $latestTargetDate->translatedFormat('d M Y'),
                'days_overdue'              => $daysOverdue,
                'keterlambatan_label'       => $daysOverdue > 0 ? "Terlambat {$daysOverdue} hari" : "Hari Ini (Jatuh Tempo)",
                'keterlambatan_badge'       => $daysOverdue >= 7 ? 'bg-danger text-white' : ($daysOverdue > 0 ? 'bg-warning text-dark' : 'bg-info text-white'),
                'total_rombel'              => count($items),
                'total_siswa_billable'      => array_sum(array_column($items, 'jumlah_siswa_billable')),
                'items'                     => $items,
            ];
        }

        // ─────────────────────────────────────────────────────────────────────
        // 2. Skema Bulanan
        // ─────────────────────────────────────────────────────────────────────
        if ($skema === Sekolah::SKEMA_BULANAN) {
            $allCompletedSessions = EkstrakurikulerSession::whereIn('ekstrakurikuler_rombel_id', $rombels->pluck('id'))
                ->where('status', 'selesai')
                ->whereNotNull('tanggal_terjadwal')
                ->orderBy('tanggal_terjadwal')
                ->get();

            if ($allCompletedSessions->isEmpty()) {
                return null;
            }

            $months = $allCompletedSessions->groupBy(fn($s) => $s->tanggal_terjadwal->format('Y-m'));
            $bulanNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            foreach ($months as $ym => $sessionsInMonth) {
                [$year, $month] = explode('-', $ym);
                $year  = (int)$year;
                $month = (int)$month;
                $monthName = $bulanNames[$month] ?? Carbon::create($year, $month, 1)->format('F');
                $periodeLabel = "{$monthName} {$year}";

                // Cek apakah invoice program untuk bulan ini sudah ada
                // Pengecekan 1: cocokkan label periode (untuk invoice bulanan baru)
                $alreadyInvoiced = $existingInvoices->contains(function ($inv) use ($periodeLabel) {
                    return str_contains(strtolower($inv->periode_label), strtolower($periodeLabel));
                });

                // Pengecekan 2: jika tidak cocok via label, cek apakah sesi di bulan ini
                // sudah tercakup oleh invoice lama (misal dengan skema per_4_pertemuan).
                // Ini menangani kasus migrasi skema sekolah dari per_4_pertemuan ke bulanan.
                if (!$alreadyInvoiced && $existingInvoices->isNotEmpty()) {
                    $monthStart2 = Carbon::create($year, $month, 1)->startOfMonth();
                    $monthEnd2   = $monthStart2->copy()->endOfMonth();

                    // Nomor sesi yang ada di bulan ini (dari semua rombel ekskul ini)
                    $sessionNosInMonth = EkstrakurikulerSession::whereIn('ekstrakurikuler_rombel_id', $rombels->pluck('id'))
                        ->whereBetween('tanggal_terjadwal', [$monthStart2->toDateString(), $monthEnd2->toDateString()])
                        ->where('status', 'selesai')
                        ->pluck('nomor_pertemuan')
                        ->unique()
                        ->values();

                    if ($sessionNosInMonth->isNotEmpty()) {
                        $alreadyInvoiced = $existingInvoices->contains(function ($inv) use ($sessionNosInMonth) {
                            // Jika invoice memiliki sesi_dari & sesi_sampai, cek overlap
                            if ($inv->sesi_dari !== null && $inv->sesi_sampai !== null) {
                                return $sessionNosInMonth->contains(function ($no) use ($inv) {
                                    return $no >= $inv->sesi_dari && $no <= $inv->sesi_sampai;
                                });
                            }
                            return false;
                        });
                    }
                }

                if ($alreadyInvoiced) {
                    continue;
                }

                $monthStart  = Carbon::create($year, $month, 1)->startOfMonth();
                $monthEnd    = $monthStart->copy()->endOfMonth();
                $isPastMonth = $monthEnd->lt($asOfDate);
                $isCurrentMonth = $asOfDate->isSameMonth($monthStart);

                $isEndOfMonth = false;
                if ($isPastMonth) {
                    $isEndOfMonth = true;
                } elseif ($isCurrentMonth) {
                    if ($asOfDate->day >= 24) {
                        $isEndOfMonth = true;
                    } else {
                        $totalSched = EkstrakurikulerSession::whereIn('ekstrakurikuler_rombel_id', $rombels->pluck('id'))
                            ->whereBetween('tanggal_terjadwal', [$monthStart->toDateString(), $monthEnd->toDateString()])
                            ->count();
                        $finished = EkstrakurikulerSession::whereIn('ekstrakurikuler_rombel_id', $rombels->pluck('id'))
                            ->whereBetween('tanggal_terjadwal', [$monthStart->toDateString(), $monthEnd->toDateString()])
                            ->whereIn('status', ['selesai', 'dibatalkan', 'libur', 'diganti'])
                            ->count();
                        if ($totalSched > 0 && $finished >= $totalSched) {
                            $isEndOfMonth = true;
                        }
                    }
                }

                if (!$isEndOfMonth) {
                    continue;
                }

                $items = [];
                $latestTargetDate = null;
                foreach ($rombels as $rombel) {
                    $rombelSessions = $rombel->sessions()
                        ->whereBetween('tanggal_terjadwal', [$monthStart->toDateString(), $monthEnd->toDateString()])
                        ->where('status', 'selesai')
                        ->get();

                    if ($rombelSessions->isEmpty()) {
                        continue;
                    }

                    $sesiDari   = $rombelSessions->min('nomor_pertemuan');
                    $sesiSampai = $rombelSessions->max('nomor_pertemuan');
                    $lastSess   = $rombelSessions->sortByDesc('tanggal_terjadwal')->first();
                    $tDate      = $lastSess?->tanggal_pelaksanaan ?? $lastSess?->tanggal_terjadwal ?? $asOfDate;
                    if (is_string($tDate)) $tDate = Carbon::parse($tDate);

                    if (!$latestTargetDate || $tDate->gt($latestTargetDate)) {
                        $latestTargetDate = $tDate;
                    }

                    $billable = $this->calculateBillable($rombel->id, $sesiDari, $sesiSampai);
                    $items[] = [
                        'ekstrakurikuler_id'        => $ekskul->id,
                        'ekstrakurikuler_rombel_id' => $rombel->id,
                        'rombel_nama'               => $rombel->nama_rombel,
                        'kategori_program'          => $ekskul->kategori_program,
                        'sesi_dari'                 => $sesiDari,
                        'sesi_sampai'               => $sesiSampai,
                        'jumlah_sesi'               => $billable['session_count'],
                        'jumlah_siswa_billable'     => $billable['billable_count'],
                    ];
                }

                if (empty($items)) {
                    continue;
                }

                $latestTargetDate = $latestTargetDate ?? $monthEnd;
                $daysOverdue = max(0, (int) $latestTargetDate->diffInDays($asOfDate, false));

                return [
                    'sekolah_kodlan'            => $sekolah->kodlan,
                    'sekolah_nama'              => $sekolah->namasekolah,
                    'ekstrakurikuler_id'        => $ekskul->id,
                    'kategori_program'          => $ekskul->kategori_program,
                    'ekstrakurikuler_rombel_id' => $items[0]['ekstrakurikuler_rombel_id'] ?? null,
                    'rombel_nama'               => count($items) === 1 ? $items[0]['rombel_nama'] : (count($items) . ' Rombel'),
                    'skema_tagihan'             => Sekolah::SKEMA_BULANAN,
                    'periode_label'             => $periodeLabel,
                    'bulan_laporan_terakhir'    => $latestTargetDate->translatedFormat('F Y'),
                    'periode_nomor'             => $month,
                    'tahun_ajaran'              => $ekskul->tahun_ajaran ?? '2026/2027',
                    'target_date'               => $latestTargetDate->toDateString(),
                    'target_date_formatted'     => $latestTargetDate->translatedFormat('d M Y'),
                    'days_overdue'              => $daysOverdue,
                    'keterlambatan_label'       => $daysOverdue > 0 ? "Terlambat {$daysOverdue} hari" : "Hari Ini (Jatuh Tempo)",
                    'keterlambatan_badge'       => $daysOverdue >= 7 ? 'bg-danger text-white' : ($daysOverdue > 0 ? 'bg-warning text-dark' : 'bg-info text-white'),
                    'total_rombel'              => count($items),
                    'total_siswa_billable'      => array_sum(array_column($items, 'jumlah_siswa_billable')),
                    'items'                     => $items,
                ];
            }

            return null;
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. Skema Semesteran
        // ─────────────────────────────────────────────────────────────────────
        if ($skema === Sekolah::SKEMA_SEMESTER) {
            $year = $asOfDate->year;
            $month = $asOfDate->month;
            if ($month >= 7) {
                $periodeLabel = "Semester 1 — Jul–Des {$year}";
                $startDate = Carbon::create($year, 7, 1)->startOfDay();
                $endDate = Carbon::create($year, 12, 31)->endOfDay();
            } else {
                $periodeLabel = "Semester 2 — Jan–Jun {$year}";
                $startDate = Carbon::create($year, 1, 1)->startOfDay();
                $endDate = Carbon::create($year, 6, 30)->endOfDay();
            }

            $alreadyInvoiced = $existingInvoices->contains(function ($inv) use ($periodeLabel) {
                return str_contains(strtolower($inv->periode_label), strtolower($periodeLabel));
            });

            if ($alreadyInvoiced) {
                return null;
            }

            $items = [];
            $latestTargetDate = null;
            foreach ($rombels as $rombel) {
                $rombelSessions = $rombel->sessions()
                    ->whereBetween('tanggal_terjadwal', [$startDate->toDateString(), $endDate->toDateString()])
                    ->where('status', 'selesai')
                    ->get();

                if ($rombelSessions->isEmpty()) {
                    continue;
                }

                $sesiDari   = $rombelSessions->min('nomor_pertemuan');
                $sesiSampai = $rombelSessions->max('nomor_pertemuan');
                $lastSess   = $rombelSessions->sortByDesc('tanggal_terjadwal')->first();
                $tDate      = $lastSess?->tanggal_pelaksanaan ?? $lastSess?->tanggal_terjadwal ?? $asOfDate;
                if (is_string($tDate)) $tDate = Carbon::parse($tDate);

                if (!$latestTargetDate || $tDate->gt($latestTargetDate)) {
                    $latestTargetDate = $tDate;
                }

                $billable = $this->calculateBillable($rombel->id, $sesiDari, $sesiSampai);
                $items[] = [
                    'ekstrakurikuler_id'        => $ekskul->id,
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'rombel_nama'               => $rombel->nama_rombel,
                    'kategori_program'          => $ekskul->kategori_program,
                    'sesi_dari'                 => $sesiDari,
                    'sesi_sampai'               => $sesiSampai,
                    'jumlah_sesi'               => $billable['session_count'],
                    'jumlah_siswa_billable'     => $billable['billable_count'],
                ];
            }

            if (empty($items)) {
                return null;
            }

            $latestTargetDate = $latestTargetDate ?? $endDate;
            $daysOverdue = max(0, (int) $latestTargetDate->diffInDays($asOfDate, false));

            return [
                'sekolah_kodlan'            => $sekolah->kodlan,
                'sekolah_nama'              => $sekolah->namasekolah,
                'ekstrakurikuler_id'        => $ekskul->id,
                'kategori_program'          => $ekskul->kategori_program,
                'ekstrakurikuler_rombel_id' => $items[0]['ekstrakurikuler_rombel_id'] ?? null,
                'rombel_nama'               => count($items) === 1 ? $items[0]['rombel_nama'] : (count($items) . ' Rombel'),
                'skema_tagihan'             => Sekolah::SKEMA_SEMESTER,
                'periode_label'             => $periodeLabel,
                'bulan_laporan_terakhir'    => $latestTargetDate->translatedFormat('F Y'),
                'periode_nomor'             => $month >= 7 ? 1 : 2,
                'tahun_ajaran'              => $ekskul->tahun_ajaran ?? '2026/2027',
                'target_date'               => $latestTargetDate->toDateString(),
                'target_date_formatted'     => $latestTargetDate->translatedFormat('d M Y'),
                'days_overdue'              => $daysOverdue,
                'keterlambatan_label'       => $daysOverdue > 0 ? "Terlambat {$daysOverdue} hari" : "Hari Ini (Jatuh Tempo)",
                'keterlambatan_badge'       => $daysOverdue >= 7 ? 'bg-danger text-white' : ($daysOverdue > 0 ? 'bg-warning text-dark' : 'bg-info text-white'),
                'total_rombel'              => count($items),
                'total_siswa_billable'      => array_sum(array_column($items, 'jumlah_siswa_billable')),
                'items'                     => $items,
            ];
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. Skema Tahunan
        // ─────────────────────────────────────────────────────────────────────
        if ($skema === Sekolah::SKEMA_TAHUNAN) {
            $year = $asOfDate->year;
            $periodeLabel = "Tahun {$year}";

            $alreadyInvoiced = $existingInvoices->contains(function ($inv) use ($periodeLabel) {
                return str_contains(strtolower($inv->periode_label), strtolower($periodeLabel));
            });

            if ($alreadyInvoiced) {
                return null;
            }

            $items = [];
            $latestTargetDate = null;
            foreach ($rombels as $rombel) {
                $rombelSessions = $rombel->sessions()
                    ->where('status', 'selesai')
                    ->whereYear('tanggal_terjadwal', $year)
                    ->get();

                if ($rombelSessions->isEmpty()) {
                    continue;
                }

                $sesiDari   = $rombelSessions->min('nomor_pertemuan');
                $sesiSampai = $rombelSessions->max('nomor_pertemuan');
                $lastSess   = $rombelSessions->sortByDesc('tanggal_terjadwal')->first();
                $tDate      = $lastSess?->tanggal_pelaksanaan ?? $lastSess?->tanggal_terjadwal ?? $asOfDate;
                if (is_string($tDate)) $tDate = Carbon::parse($tDate);

                if (!$latestTargetDate || $tDate->gt($latestTargetDate)) {
                    $latestTargetDate = $tDate;
                }

                $billable = $this->calculateBillable($rombel->id, $sesiDari, $sesiSampai);
                $items[] = [
                    'ekstrakurikuler_id'        => $ekskul->id,
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'rombel_nama'               => $rombel->nama_rombel,
                    'kategori_program'          => $ekskul->kategori_program,
                    'sesi_dari'                 => $sesiDari,
                    'sesi_sampai'               => $sesiSampai,
                    'jumlah_sesi'               => $billable['session_count'],
                    'jumlah_siswa_billable'     => $billable['billable_count'],
                ];
            }

            if (empty($items)) {
                return null;
            }

            $latestTargetDate = $latestTargetDate ?? $asOfDate;
            $daysOverdue = max(0, (int) $latestTargetDate->diffInDays($asOfDate, false));

            return [
                'sekolah_kodlan'            => $sekolah->kodlan,
                'sekolah_nama'              => $sekolah->namasekolah,
                'ekstrakurikuler_id'        => $ekskul->id,
                'kategori_program'          => $ekskul->kategori_program,
                'ekstrakurikuler_rombel_id' => $items[0]['ekstrakurikuler_rombel_id'] ?? null,
                'rombel_nama'               => count($items) === 1 ? $items[0]['rombel_nama'] : (count($items) . ' Rombel'),
                'skema_tagihan'             => Sekolah::SKEMA_TAHUNAN,
                'periode_label'             => $periodeLabel,
                'bulan_laporan_terakhir'    => $latestTargetDate->translatedFormat('F Y'),
                'periode_nomor'             => $year,
                'tahun_ajaran'              => $ekskul->tahun_ajaran ?? "{$year}/" . ($year + 1),
                'target_date'               => $latestTargetDate->toDateString(),
                'target_date_formatted'     => $latestTargetDate->translatedFormat('d M Y'),
                'days_overdue'              => $daysOverdue,
                'keterlambatan_label'       => $daysOverdue > 0 ? "Terlambat {$daysOverdue} hari" : "Hari Ini (Jatuh Tempo)",
                'keterlambatan_badge'       => $daysOverdue >= 7 ? 'bg-danger text-white' : ($daysOverdue > 0 ? 'bg-warning text-dark' : 'bg-info text-white'),
                'total_rombel'              => count($items),
                'total_siswa_billable'      => array_sum(array_column($items, 'jumlah_siswa_billable')),
                'items'                     => $items,
            ];
        }

        // ─────────────────────────────────────────────────────────────────────
        // 5. Skema CSR Reguler SOGA (Solidaritas Erlangga)
        // ─────────────────────────────────────────────────────────────────────
        if ($skema === Sekolah::SKEMA_CSR_REGULER_SOGA) {
            // Rule: "setelah semua laporan mengajar selesai"
            // 1. Cek apakah program ini sudah pernah dibuatkan invoice aktif
            if ($existingInvoices->isNotEmpty()) {
                return null;
            }

            // 2. Ambil seluruh sesi milik semua rombel di program ini
            $allSessions = EkstrakurikulerSession::whereIn('ekstrakurikuler_rombel_id', $rombels->pluck('id'))
                ->get();

            if ($allSessions->isEmpty()) {
                return null;
            }

            // Evaluasi: tidak boleh ada sesi yang masih berstatus 'terjadwal' atau 'ditunda'
            $pendingSessions = $allSessions->whereIn('status', ['terjadwal', 'ditunda'])->count();
            $completedSessions = $allSessions->where('status', 'selesai')->count();

            // Syarat: Minimal 1 sesi selesai dan SEMUA sesi telah berstatus selesai (tidak ada pending)
            if ($pendingSessions > 0 || $completedSessions === 0) {
                return null;
            }

            $items = [];
            $latestTargetDate = null;
            foreach ($rombels as $rombel) {
                $rombelSessions = $rombel->sessions()
                    ->where('status', 'selesai')
                    ->get();

                if ($rombelSessions->isEmpty()) {
                    continue;
                }

                $sesiDari   = $rombelSessions->min('nomor_pertemuan') ?? 1;
                $sesiSampai = $rombelSessions->max('nomor_pertemuan') ?? $rombelSessions->count();
                $lastSess   = $rombelSessions->sortByDesc('tanggal_terjadwal')->first();
                $tDate      = $lastSess?->tanggal_pelaksanaan ?? $lastSess?->tanggal_terjadwal ?? $asOfDate;
                if (is_string($tDate)) $tDate = Carbon::parse($tDate);

                if (!$latestTargetDate || $tDate->gt($latestTargetDate)) {
                    $latestTargetDate = $tDate;
                }

                $billable = $this->calculateBillable($rombel->id, $sesiDari, $sesiSampai);
                $items[] = [
                    'ekstrakurikuler_id'        => $ekskul->id,
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'rombel_nama'               => $rombel->nama_rombel,
                    'kategori_program'          => $ekskul->kategori_program,
                    'sesi_dari'                 => $sesiDari,
                    'sesi_sampai'               => $sesiSampai,
                    'jumlah_sesi'               => $billable['session_count'],
                    'jumlah_siswa_billable'     => $billable['billable_count'],
                ];
            }

            if (empty($items)) {
                return null;
            }

            $latestTargetDate = $latestTargetDate ?? $asOfDate;
            $daysOverdue = max(0, (int) $latestTargetDate->diffInDays($asOfDate, false));
            $periodeLabel = "CSR SOGA — " . ($ekskul->tahun_ajaran ?? '2026/2027');

            return [
                'sekolah_kodlan'            => $sekolah->kodlan,
                'sekolah_nama'              => $sekolah->namasekolah,
                'ditagihkan_ke'             => 'CSR SOGA (Solidaritas Erlangga)',
                'ekstrakurikuler_id'        => $ekskul->id,
                'kategori_program'          => $ekskul->kategori_program,
                'ekstrakurikuler_rombel_id' => $items[0]['ekstrakurikuler_rombel_id'] ?? null,
                'rombel_nama'               => count($items) === 1 ? $items[0]['rombel_nama'] : (count($items) . ' Rombel'),
                'skema_tagihan'             => Sekolah::SKEMA_CSR_REGULER_SOGA,
                'periode_label'             => $periodeLabel,
                'bulan_laporan_terakhir'    => $latestTargetDate->translatedFormat('F Y'),
                'periode_nomor'             => 1,
                'tahun_ajaran'              => $ekskul->tahun_ajaran ?? '2026/2027',
                'target_date'               => $latestTargetDate->toDateString(),
                'target_date_formatted'     => $latestTargetDate->translatedFormat('d M Y'),
                'days_overdue'              => $daysOverdue,
                'keterlambatan_label'       => $daysOverdue > 0 ? "Terlambat {$daysOverdue} hari" : "Hari Ini (Jatuh Tempo)",
                'keterlambatan_badge'       => $daysOverdue >= 7 ? 'bg-danger text-white' : ($daysOverdue > 0 ? 'bg-warning text-dark' : 'bg-info text-white'),
                'total_rombel'              => count($items),
                'total_siswa_billable'      => array_sum(array_column($items, 'jumlah_siswa_billable')),
                'items'                     => $items,
            ];
        }

        return null;
    }

    /**
     * Dapatkan daftar proposal invoice untuk sebuah sekolah (dipisah per program).
     */
    public function getEligibleInvoicesForSekolah(Sekolah $sekolah, ?Carbon $asOfDate = null): Collection
    {
        $ekskuls = $sekolah->ekstrakurikulers()
            ->whereNotIn('status', [Ekstrakurikuler::STATUS_DIBATALKAN, Ekstrakurikuler::STATUS_DITOLAK])
            ->invoiceable()
            ->with(['sekolah', 'rombels.sessions'])
            ->get();

        $results = collect();
        foreach ($ekskuls as $ekskul) {
            $eligible = $this->getEligibleInvoiceForProgram($ekskul, $asOfDate);
            if ($eligible) {
                $eligible['sekolah'] = $sekolah;
                $results->push($eligible);
            }
        }

        return $results;
    }

    /**
     * Backward-compatibility: ambil eligible program pertama untuk sekolah tersebut.
     */
    public function getEligibleInvoiceForSekolah(Sekolah $sekolah, ?Carbon $asOfDate = null): ?array
    {
        return $this->getEligibleInvoicesForSekolah($sekolah, $asOfDate)->first();
    }

    /**
     * Dapatkan semua program yang saat ini eligible (siap ditagih),
     * dipisahkan per program dan diurutkan berdasarkan keterlambatan (paling overdue di atas).
     */
    public function getAllEligiblePrograms(?Carbon $asOfDate = null): Collection
    {
        $sekolahs = Sekolah::has('invoiceableEkstrakurikulers')->get();
        $eligibleList = collect();

        foreach ($sekolahs as $sekolah) {
            $programEligibles = $this->getEligibleInvoicesForSekolah($sekolah, $asOfDate);
            foreach ($programEligibles as $el) {
                $eligibleList->push($el);
            }
        }

        return $eligibleList->sortBy([
            ['days_overdue', 'desc'],
            ['target_date', 'asc'],
            ['sekolah_nama', 'asc'],
        ])->values();
    }

    /**
     * Alias getAllEligibleSekolahs untuk memanggil getAllEligiblePrograms.
     */
    public function getAllEligibleSekolahs(?Carbon $asOfDate = null): Collection
    {
        return $this->getAllEligiblePrograms($asOfDate);
    }

    /**
     * Alias backward-compatibility untuk getAllEligibleRombels.
     */
    public function getAllEligibleRombels(?Carbon $asOfDate = null): Collection
    {
        return $this->getAllEligiblePrograms($asOfDate);
    }

    /**
     * Dapatkan status eligible untuk rombel tertentu (berdasarkan programnya).
     */
    public function getEligibleInvoiceForRombel(EkstrakurikulerRombel $rombel, ?Carbon $asOfDate = null): ?array
    {
        $ekskul = $rombel->ekstrakurikuler;
        if (!$ekskul || $ekskul->status === 'dibatalkan' || !$ekskul->isInvoiceable()) {
            return null;
        }

        $eligibleProgram = $this->getEligibleInvoiceForProgram($ekskul, $asOfDate);
        if (!$eligibleProgram) {
            return null;
        }

        $rombelItem = collect($eligibleProgram['items'])->firstWhere('ekstrakurikuler_rombel_id', $rombel->id);
        if (!$rombelItem) {
            return null;
        }

        return array_merge($eligibleProgram, [
            'ekstrakurikuler_rombel_id' => $rombel->id,
            'rombel_nama'               => $rombel->nama_rombel,
            'sesi_dari'                 => $rombelItem['sesi_dari'],
            'sesi_sampai'               => $rombelItem['sesi_sampai'],
            'jumlah_siswa_billable'     => $rombelItem['jumlah_siswa_billable'],
        ]);
    }

    /**
     * Eksekusi pembuatan invoice resmi (dengan rincian item per rombel).
     * Nomor invoice berstatus DRAFT saat pending approval.
     */
    public function createInvoiceForSekolah(array|string $data, ?int $userId = null): InvoiceApproval
    {
        if (is_string($data)) {
            $kodlan = $data;
            $sekolah = Sekolah::where('kodlan', $kodlan)->firstOrFail();
            $eligible = $this->getEligibleInvoiceForSekolah($sekolah);
            if (!$eligible) {
                throw new \InvalidArgumentException("Sekolah {$kodlan} belum memenuhi syarat tagihan.");
            }
            $data = $eligible;
        }

        $userId = $userId ?? \Illuminate\Support\Facades\Auth::id() ?? 1;

        $kodlan = $data['sekolah_kodlan'];
        $sekolah = Sekolah::where('kodlan', $kodlan)->firstOrFail();
        $periodeLabel = $data['periode_label'];

        $items = $data['items'] ?? [];
        $totalSiswa = 0;
        $maxSesi = 0;

        foreach ($items as $item) {
            $totalSiswa += ($item['jumlah_siswa_billable'] ?? 0);
            $maxSesi = max($maxSesi, $item['jumlah_sesi'] ?? 0);
        }

        $primaryRombelId = $data['ekstrakurikuler_rombel_id'] ?? ($items[0]['ekstrakurikuler_rombel_id'] ?? null);
        $ekskulId = $data['ekstrakurikuler_id'] ?? null;
        $kategoriProgram = $data['kategori_program'] ?? null;

        if (!$ekskulId && $primaryRombelId) {
            $rombelModel = EkstrakurikulerRombel::find($primaryRombelId);
            $ekskulId = $rombelModel?->ekstrakurikuler_id;
            $kategoriProgram = $kategoriProgram ?: $rombelModel?->ekstrakurikuler?->kategori_program;
        }

        // Cek apakah invoice untuk sekolah & program & periode ini sudah ada
        $existingQuery = InvoiceApproval::where('sekolah_kodlan', $kodlan)
            ->where('periode_label', $periodeLabel)
            ->where('status', '!=', InvoiceApproval::STATUS_REJECTED);

        if (!empty($ekskulId)) {
            $existingQuery->where('ekstrakurikuler_id', $ekskulId);
        }

        $existing = $existingQuery->first();
        if ($existing) {
            return $existing;
        }

        // Format nomor draft: DRAFT/ERLASS/YYYYMM/KODLAN/NNN (tanpa kata INV)
        $nomorDraft = InvoiceApproval::generateNomorInvoice($kodlan, $periodeLabel, true);

        $ekskulObj = $ekskulId ? Ekstrakurikuler::find($ekskulId) : null;
        $defaultPicNama = $ekskulObj?->penanggung_jawab;
        $defaultPicJabatan = 'Penanggung Jawab Ekstrakurikuler';

        if (($data['skema_tagihan'] ?? '') === Sekolah::SKEMA_CSR_REGULER_SOGA) {
            $defaultPicNama = 'CSR SOGA (Solidaritas Erlangga)';
            $defaultPicJabatan = 'Pihak Penanggung Dana CSR';
        }

        return DB::transaction(function () use ($kodlan, $data, $items, $totalSiswa, $maxSesi, $nomorDraft, $userId, $primaryRombelId, $ekskulId, $kategoriProgram, $defaultPicNama, $defaultPicJabatan) {
            $invoice = InvoiceApproval::create([
                'sekolah_kodlan'            => $kodlan,
                'ekstrakurikuler_id'        => $ekskulId,
                'kategori_program'          => $kategoriProgram,
                'ekstrakurikuler_rombel_id' => $primaryRombelId,
                'skema_tagihan'             => $data['skema_tagihan'],
                'periode_label'             => $data['periode_label'],
                'periode_nomor'             => $data['periode_nomor'] ?? null,
                'tahun_ajaran'              => $data['tahun_ajaran'] ?? '2026/2027',
                'sesi_dari'                 => $data['sesi_dari'] ?? null,
                'sesi_sampai'               => $data['sesi_sampai'] ?? null,
                'total_rombel'              => count($items),
                'jumlah_siswa_billable'     => $totalSiswa,
                'jumlah_sesi'               => $maxSesi,
                'nomor_invoice'             => $nomorDraft,
                'pic_konfirmasi_nama'       => $defaultPicNama,
                'pic_konfirmasi_jabatan'    => $defaultPicJabatan,
                'status'                    => InvoiceApproval::STATUS_PENDING_OPERASIONAL,
                'operasional_status'        => 'pending',
                'akunting_status'           => 'pending',
                'created_by'                => $userId,
            ]);

            foreach ($items as $item) {
                InvoiceApprovalItem::create([
                    'invoice_approval_id'       => $invoice->id,
                    'ekstrakurikuler_rombel_id' => $item['ekstrakurikuler_rombel_id'],
                    'sesi_dari'                 => $item['sesi_dari'] ?? null,
                    'sesi_sampai'               => $item['sesi_sampai'] ?? null,
                    'jumlah_sesi'               => $item['jumlah_sesi'] ?? 0,
                    'jumlah_siswa_billable'     => $item['jumlah_siswa_billable'] ?? 0,
                ]);
            }

            \Illuminate\Support\Facades\Cache::forget('invoice_eligible_programs_cache');

            return $invoice;
        });
    }

    /**
     * Backward compatibility untuk createInvoice tunggal.
     */
    public function createInvoice(array $data, int $userId): InvoiceApproval
    {
        if (isset($data['sekolah_kodlan']) && isset($data['items'])) {
            return $this->createInvoiceForSekolah($data, $userId);
        }

        // Jika dipanggil dengan format lama (rombel tunggal)
        $rombel = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->findOrFail($data['ekstrakurikuler_rombel_id']);
        if (!$rombel->ekstrakurikuler?->isInvoiceable()) {
            throw new \InvalidArgumentException("Program '{$rombel->ekstrakurikuler?->kategori_program}' tidak dapat dibuatkan invoice.");
        }

        $kodlan = $rombel->ekstrakurikuler->sekolah->kodlan ?? 'UNKN';
        $billableData = $this->calculateBillable(
            $rombel->id,
            $data['sesi_dari'] ?? null,
            $data['sesi_sampai'] ?? null
        );

        $sekolahData = [
            'sekolah_kodlan'     => $kodlan,
            'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
            'kategori_program'   => $rombel->ekstrakurikuler->kategori_program,
            'skema_tagihan'      => $data['skema_tagihan'],
            'periode_label'      => $data['periode_label'],
            'periode_nomor'      => $data['periode_nomor'] ?? null,
            'tahun_ajaran'       => $data['tahun_ajaran'] ?? ($rombel->ekstrakurikuler->tahun_ajaran ?? '2026/2027'),
            'sesi_dari'          => $data['sesi_dari'] ?? null,
            'sesi_sampai'        => $data['sesi_sampai'] ?? null,
            'items'              => [
                [
                    'ekstrakurikuler_id'        => $rombel->ekstrakurikuler_id,
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'rombel_nama'               => $rombel->nama_rombel,
                    'kategori_program'          => $rombel->ekstrakurikuler->kategori_program,
                    'sesi_dari'                 => $data['sesi_dari'] ?? null,
                    'sesi_sampai'               => $data['sesi_sampai'] ?? null,
                    'jumlah_sesi'               => $billableData['session_count'],
                    'jumlah_siswa_billable'     => $billableData['billable_count'],
                ]
            ],
        ];

        return $this->createInvoiceForSekolah($sekolahData, $userId);
    }

    /**
     * Mengambil data rincian presensi & laporan mengajar lengkap (mengadopsi format cetak absensi)
     * untuk setiap rombel dalam invoice yang bersangkutan.
     */
    public function getAttendanceDataForInvoice(InvoiceApproval $invoice): array
    {
        $invoice->loadMissing([
            'sekolah',
            'ekstrakurikuler.sales',
            'rombel.ekstrakurikuler.sales',
            'rombel.instruktur',
            'items.rombel.ekstrakurikuler.sales',
            'items.rombel.instruktur',
            'items.rombel.siswa',
        ]);

        $rombelCollection = collect();
        if ($invoice->items && $invoice->items->isNotEmpty()) {
            foreach ($invoice->items as $item) {
                if ($item->rombel) {
                    $rombelCollection->push([
                        'rombel'      => $item->rombel,
                        'sesi_dari'   => $item->sesi_dari ?: $invoice->sesi_dari,
                        'sesi_sampai' => $item->sesi_sampai ?: $invoice->sesi_sampai,
                    ]);
                }
            }
        } elseif ($invoice->rombel) {
            $rombelCollection->push([
                'rombel'      => $invoice->rombel,
                'sesi_dari'   => $invoice->sesi_dari,
                'sesi_sampai' => $invoice->sesi_sampai,
            ]);
        }

        $results = [];

        foreach ($rombelCollection as $entry) {
            $rombel     = $entry['rombel'];
            $sesiDari   = $entry['sesi_dari'];
            $sesiSampai = $entry['sesi_sampai'];
            $ekskul     = $rombel->ekstrakurikuler;
            $sekolah    = $ekskul?->sekolah ?? $invoice->sekolah;

            // Query sesi-sesi yang ditagihkan
            $querySessions = $rombel->sessions()
                ->where('nomor_pertemuan', '>', 0)
                ->with(['laporanMengajar.absensis.siswa', 'instruktur', 'asisten'])
                ->orderBy('nomor_pertemuan');

            if ($sesiDari && $sesiSampai) {
                $querySessions->whereBetween('nomor_pertemuan', [$sesiDari, $sesiSampai]);
            } else {
                $querySessions->take(4);
            }

            $sessions = $querySessions->get();

            // Identifikasi siswa yang aktif di rombel + yang tercatat pernah hadir
            $attendedStudentIds = $sessions->flatMap(function ($s) {
                return $s->laporanMengajar?->absensis?->pluck('siswa_id') ?? collect();
            })->filter()->unique()->values()->toArray();

            $students = $rombel->siswa()
                ->where(function ($query) use ($attendedStudentIds) {
                    $query->where('siswa_ekstrakurikuler.status', 'aktif');
                    if (!empty($attendedStudentIds)) {
                        $query->orWhereIn('siswa.id', $attendedStudentIds);
                    }
                })
                ->orderBy('nama_lengkap')
                ->get();

            $existingStudentIds = $students->pluck('id')->toArray();
            $missingStudentIds = array_diff($attendedStudentIds, $existingStudentIds);
            if (!empty($missingStudentIds)) {
                $missingStudents = Siswa::whereIn('id', $missingStudentIds)->get();
                $students = $students->concat($missingStudents)->sortBy('nama_lengkap')->values();
            }

            // Mapping absensi [sessionId][studentId] => 1/0
            $attendanceMap = [];
            foreach ($sessions as $s) {
                if ($s->laporanMengajar) {
                    foreach ($s->laporanMengajar->absensis as $record) {
                        $attendanceMap[$s->id][$record->siswa_id] = ($record->status === 'hadir' ? 1 : 0);
                    }

                    // Matching nama jika ID berbeda
                    $usedRecordIds = [];
                    foreach ($students as $st) {
                        if (!isset($attendanceMap[$s->id][$st->id])) {
                            $stNameClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $st->nama_lengkap)));
                            foreach ($s->laporanMengajar->absensis as $record) {
                                if (in_array($record->id, $usedRecordIds)) {
                                    continue;
                                }
                                $recName = $record->siswa?->nama_lengkap;
                                if ($recName) {
                                    $recNameClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $recName)));
                                    if ($stNameClean !== '' && $stNameClean === $recNameClean) {
                                        $attendanceMap[$s->id][$st->id] = ($record->status === 'hadir' ? 1 : 0);
                                        $usedRecordIds[] = $record->id;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // Rincian Laporan Mengajar per Sesi
            $sessionReports = [];
            foreach ($sessions as $s) {
                $lap = $s->laporanMengajar;
                $tgl = $s->tanggal_pelaksanaan ?? $s->tanggal_terjadwal;
                if ($tgl && is_string($tgl)) {
                    $tgl = Carbon::parse($tgl);
                }

                $sessionReports[] = [
                    'session_id'      => $s->id,
                    'nomor_pertemuan' => $s->nomor_pertemuan,
                    'tanggal'         => $tgl ? $tgl->translatedFormat('d F Y') : '-',
                    'tanggal_short'   => $tgl ? $tgl->format('d/m') : '-',
                    'instruktur'      => $s->instruktur?->nama_lengkap ?? $s->instruktur?->name ?? ($rombel->instruktur?->nama_lengkap ?? 'Instruktur Erlass'),
                    'materi'          => $lap?->materi ?: ($s->topik_materi ?: 'Materi pembelajaran modul'),
                    'total_hadir'     => $lap ? $lap->absensis->where('status', 'hadir')->count() : 0,
                    'total_absen'     => $lap ? $lap->absensis->where('status', '!=', 'hadir')->count() : 0,
                ];
            }

            $firstInstruktur = $sessions->first()?->instruktur?->nama_lengkap 
                ?? $sessions->first()?->instruktur?->name 
                ?? ($rombel->instruktur?->nama_lengkap ?? 'Instruktur Pengajar');

            $results[] = [
                'rombel'          => $rombel,
                'program_nama'    => $ekskul?->kategori_program ?? 'Program Ekskul',
                'rombel_nama'     => $rombel->nama_rombel,
                'school_name'     => $sekolah?->namasekolah ?? '-',
                'pic_name'        => $invoice->pic_nama ?: ($ekskul?->penanggung_jawab ?: '-'),
                'pic_jabatan'     => $invoice->pic_jabatan,
                'sales_name'      => $ekskul?->sales?->nama_lengkap ?? $ekskul?->sales?->name ?? '-',
                'instructor_name' => $firstInstruktur,
                'academic_year'   => $invoice->tahun_ajaran ?? ($ekskul?->tahun_ajaran ?? '2026/2027'),
                'sessions'        => $sessions,
                'students'        => $students,
                'attendanceMap'   => $attendanceMap,
                'sessionReports'  => $sessionReports,
            ];
        }

        return $results;
    }
}
