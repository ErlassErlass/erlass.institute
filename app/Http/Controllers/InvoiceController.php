<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerRombel;
use App\Models\InvoiceApproval;
use App\Models\Sekolah;
use App\Models\EkstrakurikulerSession;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'items.rombel.ekstrakurikuler',
            'rombel.ekstrakurikuler.sekolah',
            'operasionalUser',
            'akuntingUser',
        ])->where(function ($q) {
            $q->whereHas('items.rombel.ekstrakurikuler', fn($sub) => $sub->invoiceable())
              ->orWhereHas('rombel.ekstrakurikuler', fn($sub) => $sub->invoiceable())
              ->orWhereNotNull('sekolah_kodlan');
        });

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $invoices         = $query->orderByDesc('created_at')->paginate(25);
        $sekolahs         = Sekolah::has('invoiceableEkstrakurikulers')->orderBy('namasekolah')->get(['kodlan', 'namasekolah', 'skema_tagihan']);
        $eligibleSekolahs = $this->invoiceService->getAllEligibleSekolahs();
        $eligibleRombels  = $eligibleSekolahs; // Alias backward-compatibility

        $statusOptions = [
            'draft'               => 'Draft',
            'pending_operasional' => 'Menunggu Operasional',
            'pending_akunting'    => 'Menunggu Akunting',
            'approved'            => 'Disetujui',
            'rejected'            => 'Ditolak',
        ];

        // Summary counts
        $summary = InvoiceApproval::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('invoice.index', compact('invoices', 'sekolahs', 'statusOptions', 'summary', 'eligibleSekolahs', 'eligibleRombels'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHOW — Detail satu invoice
    // ─────────────────────────────────────────────────────────────────────────

    public function show(InvoiceApproval $invoice)
    {
        $invoice->load([
            'sekolah',
            'items.rombel.ekstrakurikuler.sales',
            'items.koreksiUser',
            'rombel.ekstrakurikuler.sekolah',
            'rombel.ekstrakurikuler.sales',
            'operasionalUser',
            'akuntingUser',
            'createdByUser',
            'koreksiByUser',
        ]);

        return view('invoice.show', compact('invoice'));
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
        // 1. Jika dikirim sekolah_kodlan (Format Utama: Per Sekolah)
        if ($request->filled('sekolah_kodlan')) {
            $validated = $request->validate([
                'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            ]);

            $sekolah = Sekolah::where('kodlan', $validated['sekolah_kodlan'])->firstOrFail();
            $eligible = $this->invoiceService->getEligibleInvoiceForSekolah($sekolah);

            if (!$eligible) {
                return back()->withErrors([
                    'msg' => "Sekolah {$sekolah->namasekolah} belum memenuhi syarat penagihan (pastikan seluruh rombel telah menuntaskan target pertemuannya).",
                ]);
            }

            $invoice = $this->invoiceService->createInvoiceForSekolah($eligible, Auth::id());

            return redirect()->route('invoice.show', $invoice)
                ->with('success', "⚡ Invoice {$invoice->nomor_invoice} berhasil dibuat otomatis untuk {$sekolah->namasekolah} ({$invoice->total_rombel} rombel). Menunggu approval Operasional.");
        }

        // 2. Format single rombel (Backward Compatibility)
        $validated = $request->validate([
            'ekstrakurikuler_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'skema_tagihan'             => 'required|in:bulanan,semester,tahunan,per_4_pertemuan',
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
            'jumlah_siswa_billable' => $billableData['billable_count'],
            'jumlah_sesi'           => $billableData['session_count'],
            'nomor_invoice'         => $nomorInv,
            'status'                => 'pending_operasional',
            'operasional_status'    => 'pending',
            'akunting_status'       => 'pending',
            'created_by'            => Auth::id(),
        ]);

        return redirect()->route('invoice.show', $invoice)
            ->with('success', "Invoice {$nomorInv} berhasil dibuat. Menunggu approval Operasional.");
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPROVE OPERASIONAL
    // ─────────────────────────────────────────────────────────────────────────

    public function approveOperasional(Request $request, InvoiceApproval $invoice)
    {
        // Hanya admin/operasional/supervisor yang boleh approve
        if ($invoice->status !== 'pending_operasional') {
            return back()->withErrors(['msg' => 'Invoice tidak dalam status menunggu approval Operasional.']);
        }

        $validated = $request->validate([
            'action'                 => 'required|in:approved,rejected',
            'catatan'                => 'nullable|string|max:500',
            'is_konfirmasi_pic'      => 'nullable|boolean',
            'pic_konfirmasi_nama'    => 'nullable|string|max:150',
            'pic_konfirmasi_catatan' => 'nullable|string|max:500',
            'operasional_checklist'  => 'nullable|array',
        ]);

        DB::transaction(function () use ($invoice, $validated, $request) {
            $isApproved = $validated['action'] === 'approved';
            $defaultChecklist = [
                'presensi_lengkap'    => true,
                'materi_tersampaikan' => true,
                'billable_sesuai_pic' => true,
            ];

            $invoice->update([
                'operasional_user_id'    => Auth::id(),
                'operasional_status'     => $validated['action'],
                'operasional_approved_at'=> now(),
                'operasional_catatan'    => $validated['catatan'] ?? null,
                'is_konfirmasi_pic'      => $isApproved ? ($request->has('is_konfirmasi_pic') ? $request->boolean('is_konfirmasi_pic') : true) : false,
                'pic_konfirmasi_nama'    => $request->input('pic_konfirmasi_nama'),
                'pic_konfirmasi_catatan' => $request->input('pic_konfirmasi_catatan') ?? $request->input('catatan'),
                'operasional_checklist'  => $request->input('operasional_checklist', $isApproved ? $defaultChecklist : null),
                'status'                 => $isApproved
                    ? 'pending_akunting'
                    : 'rejected',
                'updated_by'             => Auth::id(),
            ]);
        });

        $msg = $validated['action'] === 'approved'
            ? 'Approved oleh Operasional (Pemeriksaan Produk selesai). Menunggu approval Akunting.'
            : 'Invoice ditolak oleh Operasional.';

        return back()->with($validated['action'] === 'approved' ? 'success' : 'warning', $msg);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // APPROVE AKUNTING
    // ─────────────────────────────────────────────────────────────────────────

    public function approveAkunting(Request $request, InvoiceApproval $invoice)
    {
        // Hanya admin/akunting yang boleh approve
        if ($invoice->status !== 'pending_akunting') {
            return back()->withErrors(['msg' => 'Invoice tidak dalam status menunggu approval Akunting.']);
        }

        $validated = $request->validate([
            'action'              => 'required|in:approved,rejected',
            'catatan'             => 'nullable|string|max:500',
            'is_invoice_tercetak' => 'nullable|boolean',
            'akunting_checklist'  => 'nullable|array',
        ]);

        DB::transaction(function () use ($invoice, $validated, $request) {
            $isApproved = $validated['action'] === 'approved';
            $defaultChecklist = [
                'invoice_tercetak'       => true,
                'rekening_valid'         => true,
                'nominal_tarif_sesuai'   => true,
            ];

            $invoice->update([
                'akunting_user_id'    => Auth::id(),
                'akunting_status'     => $validated['action'],
                'akunting_approved_at'=> now(),
                'akunting_catatan'    => $validated['catatan'] ?? null,
                'is_invoice_tercetak' => $isApproved ? ($request->has('is_invoice_tercetak') ? $request->boolean('is_invoice_tercetak') : true) : false,
                'akunting_checklist'  => $request->input('akunting_checklist', $isApproved ? $defaultChecklist : null),
                'status'              => $isApproved
                    ? InvoiceApproval::STATUS_APPROVED
                    : InvoiceApproval::STATUS_REJECTED,
                'updated_by'          => Auth::id(),
            ]);

            // Finalisasi nomor invoice resmi (buang prefix DRAFT- saat approved)
            if ($isApproved) {
                $invoice->finalizeNomorInvoice();
            }
        });

        $msg = $validated['action'] === 'approved'
            ? "✅ Invoice {$invoice->fresh()->nomor_invoice} disetujui resmi (Nomor Final Diterbitkan & Invoice Tercetak). PDF siap diedarkan."
            : 'Invoice ditolak oleh Akunting.';

        return back()->with($validated['action'] === 'approved' ? 'success' : 'warning', $msg);
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice.pdf', [
            'invoice'        => $invoice,
            'isDraft'        => !$invoice->isApproved(),
            'catatanKontrak' => InvoiceApproval::catatanKontrakText(),
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
            'skema_tagihan' => 'required|in:bulanan,semester,tahunan,per_4_pertemuan',
        ]);

        $sekolah->update(['skema_tagihan' => $validated['skema_tagihan']]);

        return back()->with('success',
            "Skema tagihan {$sekolah->namasekolah} diubah ke: " . $sekolah->fresh()->skemaTagihanLabel()
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE: Hitung billable students
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Hitung jumlah siswa billable (hadir ≥ 2 dari 4 sesi) dalam range pertemuan.
     * Jika tidak ada range, hitung seluruh pertemuan dalam rombel.
     */
    private function calculateBillable(int $rombelId, ?int $sesiDari, ?int $sesiSampai): array
    {
        $sessionQuery = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombelId)
            ->where('nomor_pertemuan', '>', 0);

        if ($sesiDari && $sesiSampai) {
            $sessionQuery->whereBetween('nomor_pertemuan', [$sesiDari, $sesiSampai]);
        }

        $sessions     = $sessionQuery->get();
        $sessionCount = $sessions->count();

        if ($sessionCount === 0) {
            return ['billable_count' => 0, 'session_count' => 0];
        }

        $sessionIds = $sessions->pluck('id');
        $laporanIds = \App\Models\LaporanMengajar::whereIn('ekstrakurikuler_session_id', $sessionIds)
            ->pluck('id');

        // Hitung per siswa: berapa sesi hadir?
        $attendanceCounts = Absensi::whereIn('laporan_mengajar_id', $laporanIds)
            ->where('status', 'hadir')
            ->select('siswa_id', DB::raw('COUNT(*) as hadir_count'))
            ->groupBy('siswa_id')
            ->get();

        // Billable = hadir >= 2 dari 4 sesi
        $billableCount = $attendanceCounts->where('hadir_count', '>=', 2)->count();

        return [
            'billable_count' => $billableCount,
            'session_count'  => $sessionCount,
        ];
    }
}
