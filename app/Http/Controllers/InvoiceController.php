<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\InvoiceApproval;
use App\Models\Sekolah;
use App\Models\EkstrakurikulerSession;
use App\Models\Absensi;
use App\Models\Salesman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function __construct(protected \App\Services\InvoiceService $invoiceService)
    {
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX — Daftar semua invoice (Admin & Akunting)
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = InvoiceApproval::with([
            'sekolah',
            'items.rombel.ekstrakurikuler.sales',
            'rombel.ekstrakurikuler.sekolah',
            'rombel.ekstrakurikuler.sales',
            'ekstrakurikuler.sales',
            'operasionalUser',
            'akuntingUser',
        ])->where(function ($q) {
            $q->whereHas('items.rombel.ekstrakurikuler', fn($sub) => $sub->invoiceable())
              ->orWhereHas('rombel.ekstrakurikuler', fn($sub) => $sub->invoiceable())
              ->orWhereNotNull('sekolah_kodlan');
        });

        // Filter tab (5 Tahap: Antrean Siap Tagih vs Menunggu Admin Produksi vs Menunggu Revisi vs Menunggu Staff Akunting vs Disetujui)
        $currentTab = $request->get('tab', 'siap_tagih');

        // Dukungan per_page: 25, 50, 75, atau 'all' (semua baris)
        $perPageInput = $request->input('per_page', 25);
        if ($perPageInput === 'all' || $perPageInput === 'semua' || (int) $perPageInput <= 0) {
            $perPage = 999999;
        } else {
            $perPage = in_array((int) $perPageInput, [25, 50, 75, 100]) ? (int) $perPageInput : 25;
        }

        // Optimasi: Jika di Tab Siap Tagih atau Tab Benchmark, lewati query tabel invoice approval biasa
        if ($currentTab === 'siap_tagih' || $currentTab === 'benchmark') {
            $invoices = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage, 1, [
                'path'  => route('invoice.index'),
                'query' => $request->query(),
            ]);
        } else {
            if ($request->filled('status')) {
                if ($request->status === 'revisi') {
                    $query->where('status', InvoiceApproval::STATUS_PENDING_OPERASIONAL)
                          ->where('akunting_status', 'rejected');
                } else {
                    $query->where('status', $request->status);
                }
            } elseif ($currentTab === 'pending_operasional') {
                $query->whereIn('status', [
                    InvoiceApproval::STATUS_DRAFT,
                    InvoiceApproval::STATUS_PENDING_OPERASIONAL,
                ])->where(function ($q) {
                    $q->whereNull('akunting_status')
                      ->orWhere('akunting_status', '!=', 'rejected');
                });
            } elseif ($currentTab === 'revisi' || $currentTab === 'menunggu_revisi') {
                $query->where('status', InvoiceApproval::STATUS_PENDING_OPERASIONAL)
                      ->where('akunting_status', 'rejected');
            } elseif ($currentTab === 'pending_akunting') {
                $query->where('status', InvoiceApproval::STATUS_PENDING_AKUNTING);
            } elseif ($currentTab === 'approved') {
                $query->where('status', InvoiceApproval::STATUS_APPROVED);
            } elseif ($currentTab === 'rejected') {
                $query->where('status', InvoiceApproval::STATUS_REJECTED);
            } elseif ($currentTab === 'pending' || $currentTab === 'gantung') {
                $query->whereIn('status', [
                    InvoiceApproval::STATUS_DRAFT,
                    InvoiceApproval::STATUS_PENDING_OPERASIONAL,
                    InvoiceApproval::STATUS_PENDING_AKUNTING,
                ]);
            }

            // Filter sekolah (mendukung kodlan atau pencarian nama sekolah)
            if ($request->filled('sekolah')) {
                $sekolahVal = $request->sekolah;
                $query->where(function ($q) use ($sekolahVal) {
                    $q->where('sekolah_kodlan', $sekolahVal)
                      ->orWhereHas('rombel.ekstrakurikuler', fn($sub) => $sub->where('sekolah_kodlan', $sekolahVal))
                      ->orWhereHas('sekolah', fn($sub) => $sub->where('namasekolah', 'like', "%{$sekolahVal}%"))
                      ->orWhereHas('rombel.ekstrakurikuler.sekolah', fn($sub) => $sub->where('namasekolah', 'like', "%{$sekolahVal}%"));
                });
            }

            // Filter wilayah (kota / kotkab)
            if ($request->filled('wilayah')) {
                $wilayahVal = $request->wilayah;
                $query->where(function ($q) use ($wilayahVal) {
                    $q->whereHas('sekolah', fn($sub) => $sub->where('kota', $wilayahVal)->orWhere('kotkab', $wilayahVal))
                      ->orWhereHas('rombel.ekstrakurikuler.sekolah', fn($sub) => $sub->where('kota', $wilayahVal)->orWhere('kotkab', $wilayahVal));
                });
            }

            // Filter tahun ajaran
            if ($request->filled('tahun_ajaran')) {
                $query->where('tahun_ajaran', $request->tahun_ajaran);
            }

            // Filter skema
            if ($request->filled('skema')) {
                $query->where('skema_tagihan', $request->skema);
            }

            // Pengurutan (Sorting):
            // Khusus tab 'pending_operasional' (Menunggu Admin Produksi), prioritaskan invoice yang paling lama tertinggal
            // (oldest first: created_at ASC, id ASC) agar tidak terlewat / terlantar di antrean belakang.
            // Tab lainnya tetap default terbaru (newest first: created_at DESC).
            // Mendukung parameter ?sort=asc / ?sort=desc jika pengguna ingin beralih secara manual.
            if ($request->filled('sort')) {
                $sortDir = strtolower($request->get('sort')) === 'desc' ? 'desc' : 'asc';
                $query->orderBy('created_at', $sortDir)->orderBy('id', $sortDir);
            } elseif ($currentTab === 'pending_operasional') {
                $query->orderBy('created_at', 'asc')->orderBy('id', 'asc');
            } else {
                $query->orderByDesc('created_at');
            }

            $invoices = $query->paginate($perPage)->withQueryString();
        }

        // Optimasi Performa: Cache filter sekolah (1 jam) & antrean sekolah siap ditagih (1 jam)
        $sekolahs = Cache::remember('invoice_sekolahs_filter_list', 3600, function () {
            return Sekolah::has('invoiceableEkstrakurikulers')->orderBy('namasekolah')->get(['kodlan', 'namasekolah', 'skema_tagihan']);
        });

        if ($request->has('refresh_antrean')) {
            Cache::forget('invoice_eligible_programs_cache');
        }

        $allEligibleSekolahs = Cache::remember('invoice_eligible_programs_cache', 3600, function () {
            return $this->invoiceService->getAllEligibleSekolahs();
        });

        $eligibleSekolahs = $allEligibleSekolahs;
        if ($request->filled('sekolah')) {
            $sekolahVal = strtolower(trim($request->sekolah));
            $eligibleSekolahs = $eligibleSekolahs->filter(function ($item) use ($sekolahVal) {
                $kodlan = strtolower((string)($item['sekolah_kodlan'] ?? ''));
                $nama = strtolower((string)($item['sekolah_nama'] ?? ''));
                return $kodlan === $sekolahVal || str_contains($nama, $sekolahVal);
            });
        }
        if ($request->filled('wilayah')) {
            $wilayahVal = strtolower(trim($request->wilayah));
            $eligibleSekolahs = $eligibleSekolahs->filter(function ($item) use ($wilayahVal) {
                $kota = strtolower((string)($item['sekolah_kota'] ?? $item['sekolah_kotkab'] ?? ''));
                return $kota === $wilayahVal || str_contains($kota, $wilayahVal);
            });
        }
        if ($request->filled('skema')) {
            $eligibleSekolahs = $eligibleSekolahs->filter(function ($item) use ($request) {
                return ($item['skema_tagihan'] ?? '') === $request->skema;
            });
        }
        $eligibleTotalCount  = $allEligibleSekolahs->count();

        // Paginasi antrean siap tagih
        $page = $currentTab === 'siap_tagih' ? (int) $request->get('page', 1) : 1;
        $totalEligible = $eligibleSekolahs->count();
        $slice = $eligibleSekolahs->slice(($page - 1) * $perPage, $perPage)->values();
        $paginatedEligible = new \Illuminate\Pagination\LengthAwarePaginator(
            $slice,
            $totalEligible,
            $perPage,
            $page,
            ['path' => route('invoice.index'), 'query' => $request->query()]
        );

        $eligibleSekolahs    = $paginatedEligible;
        $eligibleRombels     = $paginatedEligible; // Alias backward-compatibility

        $statusOptions = [
            'pending_operasional' => 'Menunggu Admin Produksi',
            'revisi'              => 'Menunggu Revisi (Dikembalikan Akunting)',
            'pending_akunting'    => 'Menunggu Staff Akunting',
            'approved'            => 'Disetujui Resmi',
        ];

        // Summary counts
        $summary = InvoiceApproval::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $revisiCount = InvoiceApproval::where('status', InvoiceApproval::STATUS_PENDING_OPERASIONAL)
            ->where('akunting_status', 'rejected')
            ->count();

        $operasionalPendingCount = InvoiceApproval::whereIn('status', [
                InvoiceApproval::STATUS_DRAFT,
                InvoiceApproval::STATUS_PENDING_OPERASIONAL,
            ])->where(function ($q) {
                $q->whereNull('akunting_status')
                  ->orWhere('akunting_status', '!=', 'rejected');
            })->count();

        $akuntingPendingCount    = $summary['pending_akunting'] ?? 0;
        $pendingCount            = $operasionalPendingCount + $revisiCount + $akuntingPendingCount;
        $approvedCount           = $summary['approved'] ?? 0;
        $totalCount              = $summary->sum();

        // ── 6. DATA BENCHMARK PENAGIHAN PER SEKOLAH (Tab Benchmark) ──────────
        $benchmarkStats = [
            'total_sekolah' => 0,
            'tercapai' => 0,
            'on_track' => 0,
            'belum_ada' => 0,
            'total_invoices_approved' => 0,
            'by_skema' => [
                'per_4_pertemuan' => ['count' => 0, 'target' => 8, 'label' => 'Per 4 Pertemuan (Min. 8x)'],
                'bulanan'         => ['count' => 0, 'target' => 8, 'label' => 'Bulanan (≤ 8x)'],
                'semester'        => ['count' => 0, 'target' => 2, 'label' => 'Semesteran (2x)'],
                'tahunan'         => ['count' => 0, 'target' => 1, 'label' => 'Tahunan (1x)'],
            ]
        ];

        $benchmarkPaginator = null;
        $allWilayah = Cache::remember('invoice_available_cities', 1800, function() {
            return Sekolah::whereNotNull('kota')->where('kota', '!=', '')->distinct()->orderBy('kota')->pluck('kota');
        });
        $allSalesmen = [];

        if ($currentTab === 'benchmark') {
            $allSalesmen = Cache::remember('benchmark_salesmen_list', 1800, function() {
                return Salesman::orderBy('nama_salesman')->get(['id', 'nama_salesman', 'group_leader', 'area']);
            });

            // Ambil seluruh invoice per sekolah untuk memetakan rincian periode resmi & antrean berjalan
            $invoicesPerSekolah = InvoiceApproval::whereNotNull('sekolah_kodlan')
                ->select('id', 'sekolah_kodlan', 'nomor_invoice', 'skema_tagihan', 'periode_label', 'periode_nomor', 'status', 'sesi_dari', 'sesi_sampai', 'akunting_approved_at')
                ->orderBy('periode_nomor')
                ->orderBy('id')
                ->get()
                ->groupBy('sekolah_kodlan');

            // Ambil seluruh sekolah mitra invoiceable (kecuali CSR SOGA)
            $allBenchmarkSekolahs = Sekolah::with([
                'invoiceableEkstrakurikulers.sales',
                'invoiceableEkstrakurikulers.rombels'
            ])
            ->has('invoiceableEkstrakurikulers')
            ->where(function($q) {
                $q->where('skema_tagihan', '!=', Sekolah::SKEMA_CSR_REGULER_SOGA)
                  ->orWhereNull('skema_tagihan');
            })
            ->orderBy('namasekolah')
            ->get();

            $allBenchmarkRows = collect();

            foreach ($allBenchmarkSekolahs as $s) {
                $kodlan = $s->kodlan;
                $skema = $s->skema_tagihan ?: 'bulanan';
                if ($skema === Sekolah::SKEMA_CSR_REGULER_SOGA) continue;

                $schoolInvoices = $invoicesPerSekolah->get($kodlan, collect());
                $approvedInvoices = $schoolInvoices->where('status', InvoiceApproval::STATUS_APPROVED);
                $pendingInvoices  = $schoolInvoices->whereIn('status', [
                    InvoiceApproval::STATUS_DRAFT,
                    InvoiceApproval::STATUS_PENDING_OPERASIONAL,
                    InvoiceApproval::STATUS_PENDING_AKUNTING,
                ]);

                $realisasi = $approvedInvoices->count();
                $approvedPeriods = $approvedInvoices->pluck('periode_label')->filter()->unique()->values()->all();
                $pendingPeriods  = $pendingInvoices->pluck('periode_label')->filter()->unique()->values()->all();
                $latestApprovedAt = $approvedInvoices->max('akunting_approved_at');

                $benchmarkStats['total_invoices_approved'] += $realisasi;
                $benchmarkStats['total_sekolah']++;

                // Tentukan Salesman
                $salesmanNama = 'Belum Ditentukan';
                $salesmanId = null;
                $firstEkskul = $s->invoiceableEkstrakurikulers->first();
                if ($firstEkskul && $firstEkskul->sales) {
                    $salesmanNama = $firstEkskul->sales->nama_salesman;
                    $salesmanId = $firstEkskul->sales->id;
                }

                $target = match($skema) {
                    'per_4_pertemuan' => 8,
                    'bulanan'         => 8,
                    'semester'        => 2,
                    'tahunan'         => 1,
                    default           => 8,
                };

                if (isset($benchmarkStats['by_skema'][$skema])) {
                    $benchmarkStats['by_skema'][$skema]['count']++;
                }

                // Status Capaian
                if ($realisasi >= $target) {
                    $statusKey = 'tercapai';
                    $statusLabel = 'Tercapai Penuh';
                    $badgeClass = 'bg-success text-white';
                    $benchmarkStats['tercapai']++;
                } elseif ($realisasi > 0) {
                    $statusKey = 'on_track';
                    $statusLabel = "On-Track ({$realisasi}/{$target})";
                    $badgeClass = 'bg-primary text-white';
                    $benchmarkStats['on_track']++;
                } else {
                    $statusKey = 'belum_ada';
                    $statusLabel = 'Belum Ada Invoice';
                    $badgeClass = 'bg-secondary text-white';
                    $benchmarkStats['belum_ada']++;
                }

                $pct = min(100, round(($realisasi / max(1, $target)) * 100));

                $allBenchmarkRows->push([
                    'kodlan'           => $kodlan,
                    'namasekolah'      => $s->namasekolah,
                    'wilayah'          => $s->kota ?: ($s->kotkab ?: '—'),
                    'salesman'         => $salesmanNama,
                    'salesman_id'      => $salesmanId,
                    'skema'            => $skema,
                    'skema_label'      => $s->skemaTagihanLabel(),
                    'target'           => $target,
                    'realisasi'        => $realisasi,
                    'approved_periods' => $approvedPeriods,
                    'pending_periods'  => $pendingPeriods,
                    'persentase'       => $pct,
                    'status_key'       => $statusKey,
                    'status_label'     => $statusLabel,
                    'badge_class'      => $badgeClass,
                    'latest_approved'  => $latestApprovedAt,
                ]);
            }

            // Filtering Benchmark
            if ($request->filled('sekolah')) {
                $sch = strtolower($request->sekolah);
                $allBenchmarkRows = $allBenchmarkRows->filter(function($row) use ($sch) {
                    return str_contains(strtolower($row['kodlan']), $sch) || str_contains(strtolower($row['namasekolah']), $sch);
                });
            }

            if ($request->filled('wilayah')) {
                $wil = strtolower($request->wilayah);
                $allBenchmarkRows = $allBenchmarkRows->filter(function($row) use ($wil) {
                    return strtolower($row['wilayah']) === $wil;
                });
            }

            if ($request->filled('salesman')) {
                $sm = $request->salesman;
                $allBenchmarkRows = $allBenchmarkRows->filter(function($row) use ($sm) {
                    return (string)$row['salesman_id'] === (string)$sm || strtolower($row['salesman']) === strtolower($sm);
                });
            }

            if ($request->filled('skema')) {
                $sk = $request->skema;
                $allBenchmarkRows = $allBenchmarkRows->filter(function($row) use ($sk) {
                    return $row['skema'] === $sk;
                });
            }

            if ($request->filled('status_benchmark')) {
                $st = $request->status_benchmark;
                $allBenchmarkRows = $allBenchmarkRows->filter(function($row) use ($st) {
                    return $row['status_key'] === $st;
                });
            }

            // Pagination Benchmark
            $page = (int) $request->get('page', 1);
            $totalRows = $allBenchmarkRows->count();
            $slice = $allBenchmarkRows->slice(($page - 1) * $perPage, $perPage)->values();
            $benchmarkPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $slice,
                $totalRows,
                $perPage,
                $page,
                ['path' => route('invoice.index'), 'query' => $request->query()]
            );
        }

        return view('invoice.index', compact(
            'invoices', 
            'sekolahs', 
            'statusOptions', 
            'summary', 
            'eligibleSekolahs', 
            'eligibleRombels',
            'currentTab',
            'operasionalPendingCount',
            'revisiCount',
            'akuntingPendingCount',
            'pendingCount',
            'approvedCount',
            'totalCount',
            'eligibleTotalCount',
            'perPage',
            'benchmarkStats',
            'benchmarkPaginator',
            'allWilayah',
            'allSalesmen'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW — Detail satu invoice
    // ─────────────────────────────────────────────────────────────────────────

    public function show(InvoiceApproval $invoice)
    {
        $invoice->load([
            'sekolah',
            'ekstrakurikuler',
            'items.rombel.ekstrakurikuler.sales',
            'items.koreksiUser',
            'rombel.ekstrakurikuler.sekolah',
            'rombel.ekstrakurikuler.sales',
            'operasionalUser',
            'akuntingUser',
            'createdByUser',
            'koreksiByUser',
        ]);

        $attendanceData = $this->invoiceService->getAttendanceDataForInvoice($invoice);

        return view('invoice.show', compact('invoice', 'attendanceData'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE — Form buat invoice baru untuk rombel tertentu
    // ─────────────────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        if (!Auth::user()->canModifyOperationalData()) {
            return redirect()->route('invoice.index')->with('error', 'Akses Read-Only: Pembuatan invoice baru hanya dapat dilakukan oleh Tim Inti Produksi/Operasional.');
        }

        $rombel = null;
        $eligibleBatch = null;
        if ($request->filled('rombel_id')) {
            $rombel = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')
                ->whereHas('ekstrakurikuler', function ($q) {
                    $q->invoiceable();
                })
                ->find($request->rombel_id);

            if (!$rombel) {
                return redirect()->route('invoice.create')
                    ->with('error', 'Program atau rombel ini bukan program Ekskul / Pelatihan yang dapat ditagihkan.');
            }

            $eligibleBatch = $this->invoiceService->getEligibleInvoiceForRombel($rombel);
        }

        $sekolahs = Sekolah::has('invoiceableEkstrakurikulers')->orderBy('namasekolah')->get(['kodlan', 'namasekolah', 'skema_tagihan']);

        return view('invoice.create', compact('sekolahs', 'rombel', 'eligibleBatch'));
    }

    /**
     * JSON Endpoint: Dapatkan daftar rombel yang dapat di-invoice (Hanya Ekskul & Pelatihan)
     */
    public function rombelsBySekolah(Request $request)
    {
        $kodlan = $request->query('sekolah');
        if (!$kodlan) {
            return response()->json([]);
        }

        $rombels = EkstrakurikulerRombel::whereHas('ekstrakurikuler', function ($q) use ($kodlan) {
            $q->where('sekolah_kodlan', $kodlan)
              ->invoiceable();
        })
        ->with('ekstrakurikuler:id,kategori_program,jenis_program')
        ->orderBy('nama_rombel')
        ->get(['id', 'ekstrakurikuler_id', 'nama_rombel', 'jumlah_siswa'])
        ->map(function ($r) {
            return [
                'id'          => $r->id,
                'nama_rombel' => "[{$r->ekstrakurikuler->kategori_program}] {$r->nama_rombel}",
            ];
        });

        return response()->json($rombels);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // QUICK GENERATE — 1-Klik Generate Invoice untuk Sekolah / Rombel yang Siap
    // ─────────────────────────────────────────────────────────────────────────

    public function quickGenerate(Request $request)
    {
        if (!Auth::user()->canModifyOperationalData()) {
            return back()->withErrors(['msg' => 'Akses Read-Only: Pembuatan draft invoice hanya dapat dilakukan oleh Tim Inti Produksi/Operasional.']);
        }

        if ($request->isMethod('get')) {
            return redirect()->route('invoice.index');
        }

        // 1. Jika dikirim ekstrakurikuler_id (Format Utama: Per Program)
        if ($request->filled('ekstrakurikuler_id')) {
            $ekskul = Ekstrakurikuler::withTrashed()->with('sekolah')->findOrFail($request->ekstrakurikuler_id);
            $eligible = $this->invoiceService->getEligibleInvoiceForProgram($ekskul);

            if (!$eligible) {
                return back()->withErrors([
                    'msg' => "Program {$ekskul->kategori_program} di sekolah ini belum memenuhi syarat penagihan.",
                ]);
            }

            $invoice = $this->invoiceService->createInvoiceForSekolah($eligible, Auth::id());

            return redirect()->route('invoice.show', $invoice)
                ->with('success', "⚡ Invoice {$invoice->nomor_invoice} berhasil dibuat untuk {$ekskul->kategori_program} ({$invoice->total_rombel} rombel). Menunggu approval Operasional.");
        }

        // 2. Jika dikirim sekolah_kodlan (Fallback: Ambil program pertama yang siap)
        if ($request->filled('sekolah_kodlan')) {
            $validated = $request->validate([
                'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            ]);

            $sekolah = Sekolah::where('kodlan', $validated['sekolah_kodlan'])->firstOrFail();
            $eligible = $this->invoiceService->getEligibleInvoiceForSekolah($sekolah);

            if (!$eligible) {
                return back()->withErrors([
                    'msg' => "Sekolah {$sekolah->namasekolah} belum memenuhi syarat penagihan (pastikan rombel telah menuntaskan target pertemuannya).",
                ]);
            }

            $invoice = $this->invoiceService->createInvoiceForSekolah($eligible, Auth::id());

            return redirect()->route('invoice.show', $invoice)
                ->with('success', "⚡ Invoice {$invoice->nomor_invoice} berhasil dibuat otomatis untuk {$sekolah->namasekolah} ({$invoice->total_rombel} rombel). Menunggu approval Operasional.");
        }

        // 2. Format single rombel (Backward Compatibility)
        $validated = $request->validate([
            'ekstrakurikuler_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'skema_tagihan'             => 'required|in:bulanan,semester,tahunan,per_4_pertemuan,csr_reguler_soga',
            'periode_label'             => 'required|string|max:100',
            'tahun_ajaran'              => 'nullable|string|max:9',
            'periode_nomor'             => 'nullable|integer|min:1',
            'sesi_dari'                 => 'nullable|integer|min:1',
            'sesi_sampai'               => 'nullable|integer|min:1',
        ]);

        if ($validated['skema_tagihan'] === \App\Models\Sekolah::SKEMA_CSR_REGULER_SOGA) {
            return back()->withErrors([
                'msg' => 'Program CSR SOGA (Solidaritas Erlangga) merupakan program sosial/sponsor dan tidak perlu dibuatkan invoice.',
            ]);
        }

        $rombel = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->findOrFail($validated['ekstrakurikuler_rombel_id']);
        if (!$rombel->ekstrakurikuler?->isInvoiceable()) {
            return back()->withErrors([
                'msg' => 'Hanya program Ekskul dan Pelatihan non-CSR yang dapat dibuatkan invoice.',
            ]);
        }

        $exists = InvoiceApproval::where(function ($q) use ($validated, $rombel) {
                $q->where('ekstrakurikuler_rombel_id', $validated['ekstrakurikuler_rombel_id'])
                  ->orWhere('sekolah_kodlan', $rombel->ekstrakurikuler?->sekolah_kodlan);
            })
            ->where('periode_label', $validated['periode_label'])
            ->where('status', '!=', InvoiceApproval::STATUS_REJECTED)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'msg' => "Invoice untuk rombel / sekolah ini dan periode '{$validated['periode_label']}' sudah ada.",
            ]);
        }

        $invoice = $this->invoiceService->createInvoice($validated, Auth::id());

        return redirect()->route('invoice.show', $invoice)
            ->with('success', "⚡ Invoice {$invoice->nomor_invoice} berhasil dibuat otomatis (1-Klik). Menunggu approval Operasional.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BULK GENERATE — Generate Massal Semua Sekolah yang Siap Ditagih
    // ─────────────────────────────────────────────────────────────────────────

    public function bulkGenerate(Request $request)
    {
        if (!Auth::user()->canModifyOperationalData()) {
            return back()->withErrors(['msg' => 'Akses Read-Only: Pembuatan invoice massal hanya dapat dilakukan oleh Tim Inti Produksi/Operasional.']);
        }

        $eligibleList = $this->invoiceService->getAllEligibleSekolahs();

        if ($eligibleList->isEmpty()) {
            return back()->with('info', 'Saat ini tidak ada sekolah yang siap ditagihkan.');
        }

        $createdCount = 0;
        DB::transaction(function () use ($eligibleList, &$createdCount) {
            foreach ($eligibleList as $item) {
                $exists = InvoiceApproval::where('sekolah_kodlan', $item['sekolah_kodlan'])
                    ->where('periode_label', $item['periode_label'])
                    ->where('status', '!=', InvoiceApproval::STATUS_REJECTED)
                    ->exists();

                if (!$exists) {
                    $this->invoiceService->createInvoiceForSekolah($item, Auth::id());
                    $createdCount++;
                }
            }
        });

        Cache::forget('invoice_eligible_programs_cache');

        return redirect()->route('invoice.index')
            ->with('success', "⚡ Berhasil men-generate {$createdCount} invoice sekolah secara massal. Semua masuk status Menunggu Operasional.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE — Simpan draft invoice baru
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        if (!Auth::user()->canModifyOperationalData()) {
            return back()->withErrors(['msg' => 'Akses Read-Only: Pembuatan invoice baru hanya dapat dilakukan oleh Tim Inti Produksi/Operasional.']);
        }

        $validated = $request->validate([
            'ekstrakurikuler_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'skema_tagihan'             => 'required|in:bulanan,semester,tahunan,per_4_pertemuan',
            'periode_label'             => 'required|string|max:100',
            'tahun_ajaran'              => 'required|string|max:9',
            'periode_nomor'             => 'nullable|integer|min:1',
            'sesi_dari'                 => 'nullable|integer|min:1',
            'sesi_sampai'               => 'nullable|integer|min:1',
        ]);

        $rombel = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->findOrFail($validated['ekstrakurikuler_rombel_id']);
        if (!$rombel->ekstrakurikuler?->isInvoiceable()) {
            return back()->withErrors([
                'ekstrakurikuler_rombel_id' => 'Hanya program Ekskul dan Pelatihan yang dapat dibuatkan invoice.',
            ])->withInput();
        }

        // Cek duplikasi invoice
        $exists = InvoiceApproval::where('ekstrakurikuler_rombel_id', $validated['ekstrakurikuler_rombel_id'])
            ->where('periode_label', $validated['periode_label'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'periode_label' => 'Invoice untuk rombel dan periode ini sudah ada.',
            ])->withInput();
        }

        // Hitung siswa billable dari sistem
        $billableData = $this->calculateBillable(
            $validated['ekstrakurikuler_rombel_id'],
            $validated['sesi_dari'] ?? null,
            $validated['sesi_sampai'] ?? null
        );

        // Generate nomor invoice
        $rombel     = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->find($validated['ekstrakurikuler_rombel_id']);
        $kodlan     = $rombel->ekstrakurikuler->sekolah->kodlan ?? 'UNKN';
        $nomorInv   = InvoiceApproval::generateNomorInvoice($kodlan, $validated['periode_label']);

        $invoice = InvoiceApproval::create([
            ...$validated,
            'sekolah_kodlan'         => $kodlan,
            'ekstrakurikuler_id'     => $rombel->ekstrakurikuler_id,
            'kategori_program'       => $rombel->ekstrakurikuler?->kategori_program,
            'jumlah_siswa_billable'  => $billableData['billable_count'],
            'jumlah_sesi'            => $billableData['session_count'],
            'nomor_invoice'          => $nomorInv,
            'pic_konfirmasi_nama'    => $rombel->ekstrakurikuler?->penanggung_jawab,
            'pic_konfirmasi_jabatan' => 'Penanggung Jawab Ekstrakurikuler',
            'status'                 => 'pending_operasional',
            'operasional_status'     => 'pending',
            'akunting_status'        => 'pending',
            'created_by'             => Auth::id(),
        ]);

        return redirect()->route('invoice.show', $invoice)
            ->with('success', "Invoice {$nomorInv} berhasil dibuat. Menunggu approval Operasional.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPROVE OPERASIONAL
    // ─────────────────────────────────────────────────────────────────────────

    public function approveOperasional(Request $request, InvoiceApproval $invoice)
    {
        if (!Auth::user()->canApproveGate1Invoice()) {
            if (Auth::user()->isExecutive()) {
                return back()->withErrors(['msg' => 'Akses Read-Only: Direksi hanya memiliki hak pemantauan eksekutif.']);
            }
            return back()->withErrors(['msg' => 'Akses Ditolak: Anda tidak memiliki otoritas verifikasi Operasional (Gate 1).']);
        }
        // Boleh approve saat status draft / pending_operasional
        if ($invoice->status === InvoiceApproval::STATUS_APPROVED) {
            return back()->withErrors(['msg' => 'Invoice ini sudah disetujui resmi.']);
        }

        // Fallback cerdas: jika field action kosong dari browser/device, default ke 'approved'
        if (!$request->filled('action')) {
            $request->merge(['action' => 'approved']);
        }

        $validated = $request->validate([
            'action'                 => 'required|in:approved,rejected',
            'catatan'                => 'nullable|string|max:500',
            'pic_konfirmasi_nama'    => 'nullable|string|max:150',
            'pic_konfirmasi_jabatan' => 'nullable|string|max:150',
            'pic_konfirmasi_catatan' => 'nullable|string|max:500',
            'operasional_checklist'  => 'nullable|array',
            'bukti_chat'             => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
            'siswa_gratis_ids'       => 'nullable|array',
            'siswa_gratis_ids.*'     => 'integer',
            'siswa_gratis_alasan'    => 'nullable|array',
            'sesi_verifikasi'        => 'nullable|array',
        ]);

        DB::transaction(function () use ($invoice, $validated, $request) {
            // Produksi hanya memiliki aksi verifikasi & teruskan (koreksi dilakukan langsung melalui fitur koreksi yang tersedia)
            $isApproved = true;

            // 1. Upload Bukti Chat Screenshot
            $buktiChatPath = $invoice->bukti_chat_path;
            if ($request->hasFile('bukti_chat')) {
                $buktiChatPath = $request->file('bukti_chat')->store('invoices/bukti_chat', 'public');
            }

            // 2. Parsing Siswa Gratis (Anak Guru, Kasek, Menteri, Beasiswa)
            $siswaGratisList = [];
            if ($request->filled('siswa_gratis_ids') && is_array($request->siswa_gratis_ids)) {
                $studentIds = array_map('intval', $request->siswa_gratis_ids);
                $students   = \App\Models\Siswa::whereIn('id', $studentIds)->get(['id', 'nama_lengkap']);
                $alasanMap  = (array) $request->input('siswa_gratis_alasan', []);

                foreach ($students as $st) {
                    $alasan = trim($alasanMap[$st->id] ?? 'Anak Guru / Kasek / Kebijakan Khusus');
                    $siswaGratisList[] = [
                        'siswa_id' => $st->id,
                        'nama'     => $st->nama_lengkap,
                        'alasan'   => $alasan ?: 'Anak Guru / Kebijakan Khusus',
                    ];
                }
            }
            $jumlahSiswaGratis = count($siswaGratisList);

            // Sesi verifikasi checklist
            $sesiVerifikasiData = $request->input('sesi_verifikasi', $invoice->sesi_verifikasi_data);

            $defaultChecklist = [
                'presensi_sesi_cocok'  => true,
                'bukti_chat_terlampir' => !empty($buktiChatPath),
                'komitmen_mutlak_pic'  => true,
            ];

            $invoice->update([
                'operasional_user_id'    => Auth::id(), // otomatis nama yang submit
                'operasional_status'     => 'approved',
                'operasional_approved_at'=> now(),
                'operasional_catatan'    => $validated['catatan'] ?? null,
                'is_konfirmasi_pic'      => true,
                'pic_konfirmasi_nama'    => $request->input('pic_konfirmasi_nama') ?: $invoice->pic_nama,
                'pic_konfirmasi_jabatan' => $request->input('pic_konfirmasi_jabatan') ?: $invoice->pic_jabatan,
                'pic_konfirmasi_tgl'     => now(),
                'pic_konfirmasi_catatan' => $request->input('pic_konfirmasi_catatan') ?? $request->input('catatan'),
                'operasional_checklist'  => $request->input('operasional_checklist', $defaultChecklist),
                'bukti_chat_path'        => $buktiChatPath,
                'siswa_gratis_list'      => $siswaGratisList,
                'jumlah_siswa_gratis'    => $jumlahSiswaGratis,
                'sesi_verifikasi_data'   => $sesiVerifikasiData,
                // GATE 1 SELESAI: Diteruskan ke Meja Staff Akunting (reset status akunting jika sebelumnya dikembalikan untuk revisi)
                'serah_terima_akunting_penerima' => $invoice->serah_terima_akunting_penerima ?: (Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Staff Akunting'),
                'serah_terima_akunting_at'       => $invoice->serah_terima_akunting_at ?: now(),
                'akunting_user_id'               => null,
                'akunting_status'                => 'pending',
                'akunting_approved_at'           => null,
                'status'                         => InvoiceApproval::STATUS_PENDING_AKUNTING,
                'updated_by'                     => Auth::id(),
            ]);

            // Nomor invoice tetap DRAFT sampai Gate 2 Akunting menyetujui resmi
        });

        $stafNama = Auth::user()->nama_lengkap ?? Auth::user()->name;
        $msg = "✅ Verifikasi Gate 1 diselesaikan oleh {$stafNama}. Invoice diteruskan ke Meja Staff Akunting (Status: Menunggu Staff Akunting).";

        return back()->with('success', $msg);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LEMBAR TANDA TERIMA BERKAS AKUNTING (Opsional)
    // ─────────────────────────────────────────────────────────────────────────

    public function serahTerimaAkunting(Request $request, InvoiceApproval $invoice)
    {
        $validated = $request->validate([
            'penerima_nama' => 'required|string|max:150',
            'catatan'       => 'nullable|string|max:500',
        ], [
            'penerima_nama.required' => 'Nama staf penerima di bagian Keuangan/Akunting wajib diisi.',
        ]);

        $invoice->update([
            'serah_terima_akunting_at'       => now(),
            'serah_terima_akunting_penerima' => $validated['penerima_nama'],
            'serah_terima_akunting_catatan'  => $validated['catatan'] ?? null,
            'is_invoice_tercetak'            => true,
            'updated_by'                     => Auth::id(),
        ]);

        return back()->with('success', '✅ Catatan serah terima berkas berhasil disimpan.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPROVE AKUNTING (GATE 2: Persetujuan Staff Akunting & Penerbitan Resmi)
    // ─────────────────────────────────────────────────────────────────────────

    public function approveAkunting(Request $request, InvoiceApproval $invoice)
    {
        // Otorisasi Ketat Gate 2: Novan dan Adinda (Tim Operasional) tidak berhak menyetujui Gate 2 (Staff Akunting)
        if (!Auth::user()->canApproveGate2Invoice()) {
            return back()->withErrors([
                'msg' => 'Akses Ditolak: Anda terdaftar sebagai Admin Produksi/Operasional. Hanya Tim Akunting (Pak Witono, Rendy, dkk) yang berhak menyetujui dan menerbitkan Invoice Resmi.'
            ]);
        }

        if ($invoice->operasional_status !== 'approved') {
            return back()->withErrors(['msg' => 'Invoice harus diverifikasi oleh Admin Produksi (Gate 1) terlebih dahulu sebelum dapat disetujui Staff Akunting.']);
        }

        if ($invoice->status === InvoiceApproval::STATUS_APPROVED) {
            return back()->withErrors(['msg' => 'Invoice ini sudah berstatus Disetujui Resmi.']);
        }

        if (!$request->filled('action')) {
            $request->merge(['action' => 'approved']);
        }

        $validated = $request->validate([
            'action'              => 'required|in:approved,rejected',
            'catatan'             => $request->input('action') === 'rejected' ? 'required|string|max:500' : 'nullable|string|max:500',
            'is_invoice_tercetak' => 'nullable|boolean',
            'akunting_checklist'  => 'nullable|array',
        ], [
            'catatan.required' => 'Mohon isi catatan alasan pengembalian invoice ke Admin Produksi.',
        ]);

        $isApproved = $validated['action'] === 'approved';

        $stafNama = Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Staff Akunting';
        DB::transaction(function () use ($invoice, $validated, $isApproved, $request, $stafNama) {
            if ($isApproved) {
                $invoice->update([
                    'akunting_user_id'               => Auth::id(), // otomatis merekam akun staf yang login & klik
                    'akunting_status'                => 'approved',
                    'akunting_approved_at'           => now(),
                    'serah_terima_akunting_penerima' => $invoice->serah_terima_akunting_penerima ?: $stafNama,
                    'serah_terima_akunting_at'       => $invoice->serah_terima_akunting_at ?: now(),
                    'akunting_catatan'               => $validated['catatan'] ?? null,
                    'is_invoice_tercetak'            => $request->boolean('is_invoice_tercetak', true),
                    'akunting_checklist'             => $request->input('akunting_checklist', [
                        'rekening_valid'       => true,
                        'nominal_tarif_sesuai' => true,
                        'berkas_siap_edar'     => true,
                    ]),
                    'status'                         => InvoiceApproval::STATUS_APPROVED,
                    'updated_by'                     => Auth::id(),
                ]);

                // Finalisasi nomor invoice resmi jika disetujui (buang prefix DRAFT/ menjadi INV/)
                $invoice->finalizeNomorInvoice();
            } else {
                // Akunting mengembalikan invoice: Status MUNDUR ke Meja Admin Produksi (Gate 1)
                $invoice->update([
                    'akunting_user_id'     => Auth::id(),
                    'akunting_status'      => 'rejected',
                    'akunting_approved_at' => now(),
                    'akunting_catatan'     => $validated['catatan'],
                    'operasional_status'   => 'pending',
                    'status'               => InvoiceApproval::STATUS_PENDING_OPERASIONAL,
                    'updated_by'           => Auth::id(),
                ]);
            }
        });

        $stafNama = Auth::user()->nama_lengkap ?? Auth::user()->name;
        $msg = $isApproved
            ? "✅ Invoice resmi {$invoice->fresh()->nomor_invoice} berhasil disetujui & diterbitkan oleh {$stafNama} (Staff Akunting)."
            : "↩️ Invoice dikembalikan ke meja Admin Produksi untuk revisi oleh {$stafNama} (Staff Akunting). Alasan: {$validated['catatan']}";

        return back()->with($isApproved ? 'success' : 'warning', $msg);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DOWNLOAD PDF
    // ─────────────────────────────────────────────────────────────────────────

    public function downloadPdf(InvoiceApproval $invoice)
    {
        // Dokumen invoice dapat diunduh baik saat status DRAFT (untuk keperluan konfirmasi Operasional ke PIC Sekolah)
        // maupun saat APPROVED (dokumen penagihan resmi bernomor final).
        if ($invoice->status === InvoiceApproval::STATUS_REJECTED) {
            return back()->withErrors(['msg' => 'Invoice berstatus ditolak dan tidak dapat diunduh.']);
        }

        $invoice->load([
            'sekolah',
            'ekstrakurikuler',
            'items.rombel.ekstrakurikuler',
            'items.rombel.siswaAktif',
            'items.koreksiUser',
            'rombel.ekstrakurikuler.sekolah',
            'rombel.siswaAktif',
            'operasionalUser',
            'akuntingUser',
            'createdByUser',
        ]);

        // Update timestamp PDF
        $invoice->update(['pdf_generated_at' => now()]);

        // Ambil rincian presensi & laporan mengajar lengkap (format cetak absensi)
        $attendanceData = $this->invoiceService->getAttendanceDataForInvoice($invoice);

        // Logo Base64 (Full Logo Erlass)
        $logoPath = file_exists(base_path('full logo.png')) ? base_path('full logo.png') : public_path('images/full-logo.png');
        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;

        // Tentukan Staf Operasional / Pembuat Invoice (Hanya nomor staf yang melakukan generate)
        $dindaUser = \App\Models\User::where('id', 114)->orWhere('nama_lengkap', 'like', '%Adinda%')->first();
        $novanUser = \App\Models\User::where('id', 248)->orWhere('nama_lengkap', 'like', '%Novandi%')->first();

        // Prioritaskan user yang melakukan generate (created_by)
        $workerUserId = $invoice->created_by ?: ($invoice->operasional_user_id ?: null);
        $worker = null;

        if ($workerUserId == 114 || ($invoice->createdByUser && str_contains(strtolower($invoice->createdByUser->nama_lengkap), 'adinda'))) {
            $worker = $dindaUser;
        } elseif ($workerUserId == 248 || ($invoice->createdByUser && str_contains(strtolower($invoice->createdByUser->nama_lengkap), 'novan'))) {
            $worker = $novanUser;
        } elseif ($invoice->createdByUser) {
            $worker = $invoice->createdByUser;
        } elseif ($invoice->operasionalUser) {
            $worker = $invoice->operasionalUser;
        }

        if (!$worker && auth()->check()) {
            if (auth()->id() == 114 || str_contains(strtolower(auth()->user()->nama_lengkap), 'adinda')) $worker = $dindaUser ?: auth()->user();
            elseif (auth()->id() == 248 || str_contains(strtolower(auth()->user()->nama_lengkap), 'novan')) $worker = $novanUser ?: auth()->user();
            else $worker = auth()->user();
        }

        if (!$worker) {
            $worker = $dindaUser;
        }

        $stafNama = $worker?->nama_lengkap ?? 'Adinda Wardania';
        $stafPhone = $worker?->no_telephone;
        if (empty($stafPhone) || $stafPhone === '-') {
            if ($worker?->id == 248 || str_contains(strtolower($stafNama), 'novan')) {
                $stafPhone = '087736052381';
            } else {
                $stafPhone = '082118302927';
            }
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice.pdf', [
            'invoice'        => $invoice,
            'isDraft'        => !$invoice->isApproved(),
            'catatanKontrak' => InvoiceApproval::catatanKontrakText(),
            'attendanceData' => $attendanceData,
            'logoBase64'     => $logoBase64,
            'stafNama'       => $stafNama,
            'stafPhone'      => $stafPhone,
        ]);

        $filename = str_replace('/', '-', $invoice->nomor_invoice) . '.pdf';

        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // KOREKSI BILLABLE — Admin override hitungan sistem (Per Rombel / Header)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Admin dapat mengoreksi jumlah siswa billable jika ada selisih data lapangan.
     * Koreksi dicatat lengkap: user, waktu, alasan — untuk audit trail.
     * Dapat mengoreksi item rombel spesifik atau invoice header.
     */
    public function koreksi(Request $request, InvoiceApproval $invoice)
    {
        if (!Auth::user()->canModifyOperationalData()) {
            return back()->withErrors(['msg' => 'Akses Read-Only: Koreksi invoice hanya dapat dilakukan oleh Tim Inti Produksi/Operasional.']);
        }

        $validated = $request->validate([
            'invoice_approval_item_id' => 'nullable|exists:invoice_approval_items,id',
            'koreksi_siswa_billable'   => 'required|integer|min:0|max:999',
            'koreksi_catatan'          => 'required|string|min:10|max:500',
        ], [
            'koreksi_siswa_billable.required' => 'Jumlah koreksi wajib diisi.',
            'koreksi_catatan.required'        => 'Alasan koreksi wajib diisi (min. 10 karakter).',
            'koreksi_catatan.min'             => 'Alasan koreksi terlalu singkat. Jelaskan penyebab selisih.',
        ]);

        // Tidak boleh koreksi setelah approved
        if ($invoice->status === InvoiceApproval::STATUS_APPROVED) {
            return back()->withErrors(['msg' => 'Invoice yang sudah fully approved tidak dapat dikoreksi.']);
        }

        // Koreksi per item rombel
        if (!empty($validated['invoice_approval_item_id'])) {
            $item = $invoice->items()->findOrFail($validated['invoice_approval_item_id']);
            $item->update([
                'koreksi_siswa_billable' => $validated['koreksi_siswa_billable'],
                'koreksi_catatan'        => $validated['koreksi_catatan'],
                'koreksi_by'             => Auth::id(),
                'koreksi_at'             => now(),
            ]);
            $invoice->update(['updated_by' => Auth::id()]);

            return back()->with('success',
                "Koreksi berhasil untuk rombel {$item->rombel?->nama_rombel}. Billable diubah menjadi {$validated['koreksi_siswa_billable']} siswa."
            );
        }

        // Koreksi invoice header (fallback / single rombel)
        $invoice->update([
            'koreksi_siswa_billable' => $validated['koreksi_siswa_billable'],
            'koreksi_catatan'        => $validated['koreksi_catatan'],
            'koreksi_by'             => Auth::id(),
            'koreksi_at'             => now(),
            'updated_by'             => Auth::id(),
        ]);

        return back()->with('success',
            "Koreksi berhasil. Billable diubah dari {$invoice->jumlah_siswa_billable} → {$validated['koreksi_siswa_billable']} siswa. Alasan: {$validated['koreksi_catatan']}"
        );
    }

    /**
     * Reset koreksi — kembali ke hitungan sistem.
     */
    public function resetKoreksi(Request $request, InvoiceApproval $invoice)
    {
        if (!Auth::user()->canModifyOperationalData()) {
            return back()->withErrors(['msg' => 'Akses Read-Only: Reset koreksi invoice hanya dapat dilakukan oleh Tim Inti Produksi/Operasional.']);
        }

        if ($invoice->status === InvoiceApproval::STATUS_APPROVED) {
            return back()->withErrors(['msg' => 'Invoice yang sudah fully approved tidak dapat diubah.']);
        }

        if ($request->filled('item_id')) {
            $item = $invoice->items()->findOrFail($request->item_id);
            $item->update([
                'koreksi_siswa_billable' => null,
                'koreksi_catatan'        => null,
                'koreksi_by'             => null,
                'koreksi_at'             => null,
            ]);
            return back()->with('success', "Koreksi untuk rombel {$item->rombel?->nama_rombel} dihapus. Kembali menggunakan hitungan sistem.");
        }

        $invoice->update([
            'koreksi_siswa_billable' => null,
            'koreksi_catatan'        => null,
            'koreksi_by'             => null,
            'koreksi_at'             => null,
            'updated_by'             => Auth::id(),
        ]);

        return back()->with('success', 'Koreksi dihapus. Kembali menggunakan hitungan sistem.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE SESI PEMBELAJARAN INVOICE (CHECKLIST SESI SELESAI)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Admin/Operasional dapat memperbarui rentang sesi pertemuan yang ditagihkan
     * pada invoice berdasarkan checklist sesi yang laporannya telah berstatus selesai.
     */
    public function updateSessions(Request $request, InvoiceApproval $invoice)
    {
        if (!Auth::user()->canModifyOperationalData()) {
            return back()->withErrors(['msg' => 'Akses Read-Only: Pembaruan checklist sesi invoice hanya dapat dilakukan oleh Tim Inti Produksi/Operasional.']);
        }

        if ($invoice->isApproved() || $invoice->status === InvoiceApproval::STATUS_APPROVED) {
            return back()->withErrors(['msg' => 'Invoice yang sudah disetujui resmi oleh Akunting tidak dapat diubah sesinya.']);
        }

        $validated = $request->validate([
            'ekstrakurikuler_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'sessions'                  => 'nullable|array',
            'sessions.*'                => 'integer|min:1',
        ]);

        $rombelId = (int) $validated['ekstrakurikuler_rombel_id'];
        $rawSessions = $request->input('sessions', []);
        $selectedSessions = is_array($rawSessions)
            ? array_values(array_unique(array_filter(array_map('intval', $rawSessions))))
            : [];
        sort($selectedSessions);

        $jumlahSesi = count($selectedSessions);
        $sesiDari = $jumlahSesi > 0 ? min($selectedSessions) : null;
        $sesiSampai = $jumlahSesi > 0 ? max($selectedSessions) : null;

        // Validasi keterkaitan rombel dengan invoice
        $hasRombel = ($invoice->ekstrakurikuler_rombel_id == $rombelId) 
            || $invoice->items()->where('ekstrakurikuler_rombel_id', $rombelId)->exists();

        if (!$hasRombel) {
            return back()->withErrors(['msg' => 'Rombel yang dipilih tidak terdaftar pada invoice ini.']);
        }

        // Cek validasi duplikasi sesi pada invoice lain yang sudah resmi disetujui (hanya jika ada sesi terpilih)
        if ($jumlahSesi > 0) {
            $otherInvoices = InvoiceApproval::where('status', InvoiceApproval::STATUS_APPROVED)
                ->where('id', '!=', $invoice->id)
                ->where(function ($q) use ($rombelId) {
                    $q->where('ekstrakurikuler_rombel_id', $rombelId)
                      ->orWhereHas('items', fn($it) => $it->where('ekstrakurikuler_rombel_id', $rombelId));
                })
                ->with('items')
                ->get();

            foreach ($selectedSessions as $sessNo) {
                foreach ($otherInvoices as $otherInv) {
                    $otherItem = $otherInv->items->firstWhere('ekstrakurikuler_rombel_id', $rombelId);
                    $oDari = $otherItem ? $otherItem->sesi_dari : $otherInv->sesi_dari;
                    $oSampai = $otherItem ? $otherItem->sesi_sampai : $otherInv->sesi_sampai;
                    if ($oDari !== null && $oSampai !== null && $sessNo >= $oDari && $sessNo <= $oSampai) {
                        return back()->withErrors([
                            'msg' => "Sesi {$sessNo} sudah pernah ditagihkan pada Invoice {$otherInv->nomor_invoice}. Harap batalkan centang sesi tersebut.",
                        ]);
                    }
                }
            }
        }

        // Hitung ulang billable untuk rombel ini berdasarkan sesi terpilih
        if ($jumlahSesi > 0) {
            $billableData = $this->invoiceService->calculateBillable($rombelId, $sesiDari, $sesiSampai, $selectedSessions);
            $billableCount = $billableData['billable_count'];
        } else {
            $billableCount = 0;
        }

        DB::transaction(function () use ($invoice, $rombelId, $sesiDari, $sesiSampai, $jumlahSesi, $billableCount) {
            $item = $invoice->items()->where('ekstrakurikuler_rombel_id', $rombelId)->first();
            if ($item) {
                $item->update([
                    'sesi_dari'             => $sesiDari,
                    'sesi_sampai'           => $sesiSampai,
                    'jumlah_sesi'           => $jumlahSesi,
                    'jumlah_siswa_billable' => $billableCount,
                ]);
            }

            if ($invoice->items()->count() <= 1) {
                $invoice->update([
                    'sesi_dari'             => $sesiDari,
                    'sesi_sampai'           => $sesiSampai,
                    'jumlah_sesi'           => $jumlahSesi,
                    'jumlah_siswa_billable' => $billableCount,
                    'total_rombel'          => $jumlahSesi > 0 ? 1 : 0,
                    'pdf_generated_at'      => null,
                    'updated_by'            => Auth::id(),
                ]);
            } else {
                $allItems = $invoice->items()->get();
                $activeItems = $allItems->where('jumlah_sesi', '>', 0);

                $minSesi = $activeItems->isNotEmpty() ? $activeItems->min('sesi_dari') : null;
                $maxSesi = $activeItems->isNotEmpty() ? $activeItems->max('sesi_sampai') : null;
                $maxJumlahSesi = $activeItems->isNotEmpty() ? $activeItems->max('jumlah_sesi') : 0;
                $totalBillable = $allItems->sum('jumlah_siswa_billable');
                $totalRombel = $activeItems->count();

                $invoice->update([
                    'sesi_dari'             => $minSesi,
                    'sesi_sampai'           => $maxSesi,
                    'jumlah_sesi'           => $maxJumlahSesi,
                    'jumlah_siswa_billable' => $totalBillable,
                    'total_rombel'          => $totalRombel > 0 ? $totalRombel : $allItems->count(),
                    'pdf_generated_at'      => null,
                    'updated_by'            => Auth::id(),
                ]);
            }
        });

        if ($jumlahSesi > 0) {
            return back()->with('success', "⚡ Sesi pembelajaran berhasil diperbarui (Sesi {$sesiDari} s/d {$sesiSampai}, total {$jumlahSesi} sesi). Siswa billable dihitung ulang secara otomatis ({$billableCount} siswa).");
        } else {
            return back()->with('success', "⚡ Sesi pembelajaran untuk rombel ini berhasil dikosongkan (0 sesi ditagihkan). Siswa billable disesuaikan menjadi 0 siswa.");
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE SKEMA — Admin ubah skema sekolah (dari per_4 ke skema lain)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Admin dapat mengubah skema tagihan sekolah setelah data masuk.
     * Default semua sekolah baru = per_4_pertemuan (rolling batch).
     */
    public function updateSkemaSekolah(Request $request, string $kodlan)
    {
        if (!Auth::user()->canModifyOperationalData() && !Auth::user()->canApproveGate2Invoice()) {
            return back()->withErrors(['msg' => 'Akses Read-Only: Pengubahan skema tagihan hanya dapat dilakukan oleh Tim Produksi atau Akunting.']);
        }

        $sekolah = \App\Models\Sekolah::findOrFail($kodlan);

        $validated = $request->validate([
            'skema_tagihan' => 'required|in:bulanan,semester,tahunan,per_4_pertemuan,csr_reguler_soga',
        ]);

        $sekolah->update(['skema_tagihan' => $validated['skema_tagihan']]);

        if (\Illuminate\Support\Facades\Schema::hasColumn('ekstrakurikuler', 'skema_tagihan')) {
            \App\Models\Ekstrakurikuler::where('sekolah_kodlan', $kodlan)
                ->update(['skema_tagihan' => $validated['skema_tagihan']]);
        }

        // Invalidate cache antrean & filter agar perubahan langsung tampak di UI
        Cache::forget('invoice_eligible_programs_cache');
        Cache::forget('invoice_sekolahs_filter_list');

        return back()->with('success',
            "Skema tagihan {$sekolah->namasekolah} diubah ke: " . $sekolah->fresh()->skemaTagihanLabel()
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: Hitung billable students
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Hitung jumlah siswa billable (hadir ≥ 2 dari 4 sesi) dalam range pertemuan.
     * Menggunakan InvoiceService sebagai single source of truth.
     */
    private function calculateBillable(int $rombelId, ?int $sesiDari, ?int $sesiSampai): array
    {
        return $this->invoiceService->calculateBillable($rombelId, $sesiDari, $sesiSampai);
    }
}
