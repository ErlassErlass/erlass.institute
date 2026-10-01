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
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * Hitung jumlah siswa billable dari data absensi sistem.
     * Logika: Jumlah siswa unik yang berstatus 'hadir' di sesi yang termasuk
     * dalam range yang ditentukan untuk rombel tersebut.
     * 
     * Fallback: jika tidak ada data absensi individual, gunakan rata-rata jumlah siswa
     * yang hadir dari LaporanMengajar, atau jumlah siswa terdaftar di rombel.
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

        // Hitung siswa unik yang hadir di sesi-sesi tersebut
        $uniqueHadir = Absensi::whereIn('laporan_mengajar_id', $laporanIds)
            ->where('status', 'hadir')
            ->distinct('siswa_id')
            ->count('siswa_id');

        if ($uniqueHadir > 0) {
            $billableCount = $uniqueHadir;
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
     * Cek apakah sebuah sekolah siap ditagih (eligible for billing) dan kembalikan metadatanya jika siap.
     * 
     * Aturan:
     * 1. 1 Invoice diterbitkan per Sekolah, berisi rincian item untuk seluruh rombel di sekolah tersebut.
     * 2. Tunggu SEMUA rombel (Ekskul & Pelatihan) di sekolah tersebut selesai dulu baru diterbitkan 1 invoice bersamaan.
     * 3. Skema per_4_pertemuan: Semua rombel harus sudah menyelesaikan 4 sesi berikutnya.
     * 4. Skema bulanan: Semua rombel harus sudah menyelesaikan seluruh sesi terjadwal di bulan tersebut (di akhir bulan).
     */
    public function getEligibleInvoiceForSekolah(Sekolah $sekolah, ?Carbon $asOfDate = null): ?array
    {
        $asOfDate = $asOfDate ? $asOfDate->copy() : Carbon::now();

        // Dapatkan seluruh rombel aktif milik sekolah ini yang berstatus Ekskul / Pelatihan
        $rombels = EkstrakurikulerRombel::whereHas('ekstrakurikuler', function ($q) use ($sekolah) {
            $q->where('sekolah_kodlan', $sekolah->kodlan)
              ->whereNotIn('status', [Ekstrakurikuler::STATUS_DIBATALKAN, Ekstrakurikuler::STATUS_DITOLAK])
              ->invoiceable();
        })
        ->where('status', '!=', EkstrakurikulerRombel::STATUS_DIBATALKAN)
        ->with(['sessions', 'ekstrakurikuler'])
        ->get();

        if ($rombels->isEmpty()) {
            return null;
        }

        $skema = $sekolah->skema_tagihan ?? Sekolah::SKEMA_PER_4_PERTEMUAN;

        // Invoices aktif yang sudah ada untuk sekolah ini
        $existingInvoices = InvoiceApproval::where('sekolah_kodlan', $sekolah->kodlan)
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

                // Syarat: Tunggu SEMUA rombel di sekolah selesai 4 sesi
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
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'rombel_nama'               => $rombel->nama_rombel,
                    'kategori_program'          => $rombel->ekstrakurikuler->kategori_program,
                    'sesi_dari'                 => $dari,
                    'sesi_sampai'               => $sampai,
                    'jumlah_sesi'               => $billable['session_count'],
                    'jumlah_siswa_billable'     => $billable['billable_count'],
                ];
            }

            $latestTargetDate = $latestTargetDate ?? $asOfDate;
            $daysOverdue = max(0, (int) $latestTargetDate->diffInDays($asOfDate, false));

            return [
                'sekolah_kodlan'            => $sekolah->kodlan,
                'sekolah_nama'              => $sekolah->namasekolah,
                'ekstrakurikuler_rombel_id' => $items[0]['ekstrakurikuler_rombel_id'] ?? null,
                'rombel_nama'               => count($items) === 1 ? $items[0]['rombel_nama'] : (count($items) . ' Rombel'),
                'kategori_program'          => count($items) === 1 ? $items[0]['kategori_program'] : 'Gabungan Program',
                'skema_tagihan'             => Sekolah::SKEMA_PER_4_PERTEMUAN,
                'periode_label'             => "Inv Bulan {$nextBatch}",
                'periode_nomor'             => $nextBatch,
                'tahun_ajaran'              => $rombels->first()->ekstrakurikuler->tahun_ajaran ?? '2026/2027',
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
        // 2. Skema Bulanan (Akhir Bulan & Semua Rombel Selesai)
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

                // Cek apakah invoice sekolah untuk bulan ini sudah ada
                $alreadyInvoiced = $existingInvoices->contains(function ($inv) use ($periodeLabel) {
                    return str_contains(strtolower($inv->periode_label), strtolower($periodeLabel));
                });

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

                // Syarat: Tunggu SEMUA rombel di sekolah tersebut selesai dulu
                $allRombelsDone = true;
                $items = [];
                $latestTargetDate = null;

                foreach ($rombels as $rombel) {
                    $rombelSessionsInMonth = $rombel->sessions()
                        ->whereBetween('tanggal_terjadwal', [$monthStart->toDateString(), $monthEnd->toDateString()])
                        ->get();

                    $hasPending = $rombelSessionsInMonth->contains(fn($s) => !in_array($s->status, ['selesai', 'dibatalkan', 'libur', 'diganti']));
                    $completedRombel = $rombelSessionsInMonth->where('status', 'selesai');

                    if ($hasPending || $completedRombel->isEmpty()) {
                        $allRombelsDone = false;
                        break;
                    }

                    $sesiDari   = $completedRombel->min('nomor_pertemuan');
                    $sesiSampai = $completedRombel->max('nomor_pertemuan');
                    $lastSess   = $completedRombel->sortByDesc('tanggal_terjadwal')->first();
                    $tDate      = $lastSess?->tanggal_pelaksanaan ?? $lastSess?->tanggal_terjadwal ?? $monthEnd;

                    if (is_string($tDate)) {
                        $tDate = Carbon::parse($tDate);
                    }

                    if (!$latestTargetDate || $tDate->gt($latestTargetDate)) {
                        $latestTargetDate = $tDate;
                    }

                    $billable = $this->calculateBillable($rombel->id, $sesiDari, $sesiSampai);
                    $items[] = [
                        'ekstrakurikuler_rombel_id' => $rombel->id,
                        'rombel_nama'               => $rombel->nama_rombel,
                        'kategori_program'          => $rombel->ekstrakurikuler->kategori_program,
                        'sesi_dari'                 => $sesiDari,
                        'sesi_sampai'               => $sesiSampai,
                        'jumlah_sesi'               => $billable['session_count'],
                        'jumlah_siswa_billable'     => $billable['billable_count'],
                    ];
                }

                if (!$allRombelsDone || empty($items)) {
                    continue;
                }

                $latestTargetDate = $latestTargetDate ?? $asOfDate;
                $daysOverdue = max(0, (int) $latestTargetDate->diffInDays($asOfDate, false));

                return [
                    'sekolah_kodlan'            => $sekolah->kodlan,
                    'sekolah_nama'              => $sekolah->namasekolah,
                    'ekstrakurikuler_rombel_id' => $items[0]['ekstrakurikuler_rombel_id'] ?? null,
                    'rombel_nama'               => count($items) === 1 ? $items[0]['rombel_nama'] : (count($items) . ' Rombel'),
                    'kategori_program'          => count($items) === 1 ? $items[0]['kategori_program'] : 'Gabungan Program',
                    'skema_tagihan'             => Sekolah::SKEMA_BULANAN,
                    'periode_label'             => $periodeLabel,
                    'periode_nomor'             => $month,
                    'tahun_ajaran'              => $rombels->first()->ekstrakurikuler->tahun_ajaran ?? '2026/2027',
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

        return null;
    }

    /**
     * Dapatkan semua sekolah yang saat ini eligible (siap ditagih),
     * diurutkan berdasarkan prioritas keterlambatan (paling lama terlambat di atas).
     */
    public function getAllEligibleSekolahs(?Carbon $asOfDate = null): Collection
    {
        $sekolahs = Sekolah::has('invoiceableEkstrakurikulers')->get();
        $eligibleList = collect();

        foreach ($sekolahs as $sekolah) {
            $eligible = $this->getEligibleInvoiceForSekolah($sekolah, $asOfDate);
            if ($eligible) {
                $eligible['sekolah'] = $sekolah;
                $eligibleList->push($eligible);
            }
        }

        // Urutkan berdasarkan keterlambatan: days_overdue DESC (paling wajib/terlambat di paling atas)
        return $eligibleList->sortBy([
            ['days_overdue', 'desc'],
            ['target_date', 'asc'],
            ['sekolah_nama', 'asc'],
        ])->values();
    }

    /**
     * Alias backward-compatibility untuk getAllEligibleRombels.
     */
    public function getAllEligibleRombels(?Carbon $asOfDate = null): Collection
    {
        return $this->getAllEligibleSekolahs($asOfDate);
    }

    /**
     * Dapatkan status eligible untuk rombel tertentu (berdasarkan sekolahnya).
     */
    public function getEligibleInvoiceForRombel(EkstrakurikulerRombel $rombel, ?Carbon $asOfDate = null): ?array
    {
        $ekskul = $rombel->ekstrakurikuler;
        if (!$ekskul || $ekskul->status === 'dibatalkan' || !$ekskul->isInvoiceable()) {
            return null;
        }

        $sekolah = $ekskul->sekolah;
        if (!$sekolah) return null;

        $eligibleSekolah = $this->getEligibleInvoiceForSekolah($sekolah, $asOfDate);
        if (!$eligibleSekolah) return null;

        // Cari item rombel ini di dalam list item sekolah
        $rombelItem = collect($eligibleSekolah['items'])->firstWhere('ekstrakurikuler_rombel_id', $rombel->id);
        if (!$rombelItem) return null;

        return array_merge($eligibleSekolah, [
            'ekstrakurikuler_rombel_id' => $rombel->id,
            'rombel_nama'               => $rombel->nama_rombel,
            'sesi_dari'                 => $rombelItem['sesi_dari'],
            'sesi_sampai'               => $rombelItem['sesi_sampai'],
            'jumlah_siswa_billable'     => $rombelItem['jumlah_siswa_billable'],
        ]);
    }

    /**
     * Eksekusi pembuatan invoice resmi per sekolah (dengan rincian item per rombel).
     * Nomor invoice berstatus DRAFT saat pending approval ("nomornya draft saja ya").
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

        // Cek apakah invoice sekolah untuk periode ini sudah ada
        $existing = InvoiceApproval::where('sekolah_kodlan', $kodlan)
            ->where('periode_label', $periodeLabel)
            ->where('status', '!=', InvoiceApproval::STATUS_REJECTED)
            ->first();

        if ($existing) {
            return $existing;
        }

        $items = $data['items'] ?? [];
        $totalSiswa = 0;
        $maxSesi = 0;

        foreach ($items as $item) {
            $totalSiswa += ($item['jumlah_siswa_billable'] ?? 0);
            $maxSesi = max($maxSesi, $item['jumlah_sesi'] ?? 0);
        }

        // Format nomor draft: DRAFT-INV/ERLASS/YYYYMM/KODLAN/NNN
        $nomorDraft = InvoiceApproval::generateNomorInvoice($kodlan, $periodeLabel, true);
        $primaryRombelId = $data['ekstrakurikuler_rombel_id'] ?? ($items[0]['ekstrakurikuler_rombel_id'] ?? null);

        return DB::transaction(function () use ($kodlan, $data, $items, $totalSiswa, $maxSesi, $nomorDraft, $userId, $primaryRombelId) {
            $invoice = InvoiceApproval::create([
                'sekolah_kodlan'            => $kodlan,
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
            'sekolah_kodlan' => $kodlan,
            'skema_tagihan'  => $data['skema_tagihan'],
            'periode_label'  => $data['periode_label'],
            'periode_nomor'  => $data['periode_nomor'] ?? null,
            'tahun_ajaran'   => $data['tahun_ajaran'] ?? ($rombel->ekstrakurikuler->tahun_ajaran ?? '2026/2027'),
            'sesi_dari'      => $data['sesi_dari'] ?? null,
            'sesi_sampai'    => $data['sesi_sampai'] ?? null,
            'items'          => [
                [
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
}
