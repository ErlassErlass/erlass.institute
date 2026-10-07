<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\InvoiceApproval;
use App\Models\Sekolah;
use App\Models\EkstrakurikulerSession;
use App\Models\Absensi;
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

        // Filter tab (Semua vs Menunggu Admin Produksi vs Menunggu Staff Akunting vs Disetujui vs Ditolak)
        $currentTab = $request->get('tab', 'all');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } elseif ($currentTab === 'pending_operasional') {
            $query->whereIn('status', [
                InvoiceApproval::STATUS_DRAFT,
                InvoiceApproval::STATUS_PENDING_OPERASIONAL,
            ]);
        } elseif ($currentTab === 'pending_akunting') {
            $query->where('status', InvoiceApproval::STATUS_PENDING_AKUNTING);
        } elseif ($currentTab === 'pending' || $currentTab === 'gantung') {
            $query->whereIn('status', [
                InvoiceApproval::STATUS_DRAFT,
                InvoiceApproval::STATUS_PENDING_OPERASIONAL,
                InvoiceApproval::STATUS_PENDING_AKUNTING,
            ]);
        } elseif ($currentTab === 'approved') {
            $query->where('status', InvoiceApproval::STATUS_APPROVED);
        } elseif ($currentTab === 'rejected') {
            $query->where('status', InvoiceApproval::STATUS_REJECTED);
        }

        // Filter sekolah
        if ($request->filled('sekolah')) {
            $query->where(function ($q) use ($request) {
                $q->where('sekolah_kodlan', $request->sekolah)
                  ->orWhereHas('rombel.ekstrakurikuler', fn($sub) => $sub->where('sekolah_kodlan', $request->sekolah));
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

        $invoices         = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        // Optimasi Performa: Cache filter sekolah (1 jam) & antrean sekolah siap ditagih (5 menit)
        $sekolahs = Cache::remember('invoice_sekolahs_filter_list', 3600, function () {
            return Sekolah::has('invoiceableEkstrakurikulers')->orderBy('namasekolah')->get(['kodlan', 'namasekolah', 'skema_tagihan']);
        });

        if ($request->has('refresh_antrean')) {
            Cache::forget('invoice_eligible_programs_cache');
        }

        $eligibleSekolahs = Cache::remember('invoice_eligible_programs_cache', 300, function () {
            return $this->invoiceService->getAllEligibleSekolahs();
        });
        $eligibleRombels  = $eligibleSekolahs; // Alias backward-compatibility

        $statusOptions = [
            'pending_operasional' => 'Menunggu Admin Produksi',
            'pending_akunting'    => 'Menunggu Staff Akunting',
            'approved'            => 'Disetujui Resmi',
        ];

        // Summary counts
        $summary = InvoiceApproval::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $operasionalPendingCount = ($summary['draft'] ?? 0) + ($summary['pending_operasional'] ?? 0);
        $akuntingPendingCount    = $summary['pending_akunting'] ?? 0;
        $pendingCount            = $operasionalPendingCount + $akuntingPendingCount;
        $approvedCount           = $summary['approved'] ?? 0;
        $totalCount              = $summary->sum();

        return view('invoice.index', compact(
            'invoices', 
            'sekolahs', 
            'statusOptions', 
            'summary', 
            'eligibleSekolahs', 
            'eligibleRombels',
            'currentTab',
            'operasionalPendingCount',
            'akuntingPendingCount',
            'pendingCount',
            'approvedCount',
            'totalCount'
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
        if ($request->isMethod('get')) {
            return redirect()->route('invoice.index');
        }

        // 1. Jika dikirim ekstrakurikuler_id (Format Utama: Per Program)
        if ($request->filled('ekstrakurikuler_id')) {
            $ekskul = Ekstrakurikuler::with('sekolah')->findOrFail($request->ekstrakurikuler_id);
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

        $rombel = EkstrakurikulerRombel::with('ekstrakurikuler.sekolah')->findOrFail($validated['ekstrakurikuler_rombel_id']);
        if (!$rombel->ekstrakurikuler?->isInvoiceable()) {
            return back()->withErrors([
                'msg' => 'Hanya program Ekskul dan Pelatihan yang dapat dibuatkan invoice.',
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
                'akunting_user_id'       => null,
                'akunting_status'        => 'pending',
                'akunting_approved_at'   => null,
                'status'                 => InvoiceApproval::STATUS_PENDING_AKUNTING,
                'updated_by'             => Auth::id(),
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
                'msg' => 'Akses Ditolak: Anda terdaftar sebagai Admin Produksi/Operasional. Hanya Staff Akunting (Rendy) yang berhak menyetujui dan menerbitkan Invoice Resmi.'
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

        DB::transaction(function () use ($invoice, $validated, $isApproved, $request) {
            if ($isApproved) {
                $invoice->update([
                    'akunting_user_id'     => Auth::id(), // otomatis merekam akun staf yang login & klik
                    'akunting_status'      => 'approved',
                    'akunting_approved_at' => now(),
                    'akunting_catatan'     => $validated['catatan'] ?? null,
                    'is_invoice_tercetak'  => $request->boolean('is_invoice_tercetak', true),
                    'akunting_checklist'   => $request->input('akunting_checklist', [
                        'rekening_valid'       => true,
                        'nominal_tarif_sesuai' => true,
                        'berkas_siap_edar'     => true,
                    ]),
                    'status'               => InvoiceApproval::STATUS_APPROVED,
                    'updated_by'           => Auth::id(),
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
        ]);

        // Update timestamp PDF
        $invoice->update(['pdf_generated_at' => now()]);

        // Ambil rincian presensi & laporan mengajar lengkap (format cetak absensi)
        $attendanceData = $this->invoiceService->getAttendanceDataForInvoice($invoice);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice.pdf', [
            'invoice'        => $invoice,
            'isDraft'        => !$invoice->isApproved(),
            'catatanKontrak' => InvoiceApproval::catatanKontrakText(),
            'attendanceData' => $attendanceData,
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
    // UPDATE SKEMA — Admin ubah skema sekolah (dari per_4 ke skema lain)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Admin dapat mengubah skema tagihan sekolah setelah data masuk.
     * Default semua sekolah baru = per_4_pertemuan (rolling batch).
     */
    public function updateSkemaSekolah(Request $request, string $kodlan)
    {
        $sekolah = \App\Models\Sekolah::findOrFail($kodlan);

        $validated = $request->validate([
            'skema_tagihan' => 'required|in:bulanan,semester,tahunan,per_4_pertemuan,csr_reguler_soga',
        ]);

        $sekolah->update(['skema_tagihan' => $validated['skema_tagihan']]);

        if (\Illuminate\Support\Facades\Schema::hasColumn('ekstrakurikuler', 'skema_tagihan')) {
            \App\Models\Ekstrakurikuler::where('sekolah_kodlan', $kodlan)
                ->update(['skema_tagihan' => $validated['skema_tagihan']]);
        }

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
