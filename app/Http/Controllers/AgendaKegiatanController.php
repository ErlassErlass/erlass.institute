<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAgendaExportJob;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\InvoiceApproval;
use App\Models\Salesman;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgendaKegiatanController extends Controller
{
    /**
     * Tampilkan halaman utama Agenda Kegiatan (publik).
     */
    public function index()
    {
        $wilayahList = Cache::remember('agenda_wilayah_list', 3600, function () {
            return Sekolah::query()
                ->join('ekstrakurikuler', 'sekolah.kodlan', '=', 'ekstrakurikuler.sekolah_kodlan')
                ->join('ekstrakurikuler_rombel', 'ekstrakurikuler.id', '=', 'ekstrakurikuler_rombel.ekstrakurikuler_id')
                ->join('ekstrakurikuler_session', 'ekstrakurikuler_rombel.id', '=', 'ekstrakurikuler_session.ekstrakurikuler_rombel_id')
                ->where('ekstrakurikuler_session.status', 'selesai')
                ->whereNotNull('sekolah.kota')
                ->distinct()
                ->orderBy('sekolah.kota')
                ->pluck('sekolah.kota');
        });

        $programList = Cache::remember('agenda_program_list', 3600, function () {
            return \App\Models\Ekstrakurikuler::query()
                ->distinct()
                ->orderBy('kategori_program')
                ->pluck('kategori_program');
        });

        $instrukturList = Cache::remember('agenda_instruktur_list', 3600, function () {
            return \App\Models\User::query()
                ->where('role', 'instruktur')
                ->where('verification_status', 'approved')
                ->orderBy('nama_lengkap')
                ->select('id', 'nama_lengkap')
                ->get();
        });

        $salesmenList = Cache::remember('agenda_salesmen_list', 3600, function () {
            return Salesman::query()
                ->orderBy('nama_salesman')
                ->get(['id', 'nama_salesman', 'group_leader', 'area']);
        });

        $currentSalesman = null;
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->salesman) {
                $currentSalesman = $user->salesman;
            } else {
                $currentSalesman = Salesman::where('nama_salesman', $user->nama_lengkap)->first();
            }
        }

        $totalSesi = EkstrakurikulerSession::where('status', 'selesai')->count();

        $totalInvoiceTercetak = InvoiceApproval::where(function ($q) {
            $q->where('status', InvoiceApproval::STATUS_APPROVED)
              ->orWhere('is_invoice_tercetak', true);
        })->count();

        return view('agenda-kegiatan.index', compact(
            'wilayahList',
            'programList',
            'instrukturList',
            'salesmenList',
            'currentSalesman',
            'totalSesi',
            'totalInvoiceTercetak'
        ));
    }

    /**
     * AJAX: Cascading dropdown filter.
     * - Jika ada ?kota=X  => return list sekolah di kota X
     * - Jika kota kosong => return seluruh sekolah yang memiliki sesi selesai
     * - Jika ada ?sekolah_kodlan=X => return list rombel di sekolah X
     */
    public function filter(Request $request): JsonResponse
    {
        $type = $request->input('type', $request->has('sekolah_kodlan') ? 'rombel' : 'sekolah');

        if ($type === 'rombel' || $request->has('sekolah_kodlan')) {
            $query = EkstrakurikulerRombel::query()
                ->join('ekstrakurikuler', 'ekstrakurikuler_rombel.ekstrakurikuler_id', '=', 'ekstrakurikuler.id')
                ->join('ekstrakurikuler_session', 'ekstrakurikuler_rombel.id', '=', 'ekstrakurikuler_session.ekstrakurikuler_rombel_id')
                ->where('ekstrakurikuler_session.status', 'selesai');

            if ($request->filled('sekolah_kodlan')) {
                $query->where('ekstrakurikuler.sekolah_kodlan', $request->sekolah_kodlan);

                $rombels = $query->select(
                        'ekstrakurikuler_rombel.id',
                        'ekstrakurikuler_rombel.nama_rombel',
                        'ekstrakurikuler.kategori_program'
                    )
                    ->distinct()
                    ->orderBy('ekstrakurikuler_rombel.nama_rombel')
                    ->get();

                $hasMultiplePrograms = $rombels->pluck('kategori_program')->unique()->count() > 1;

                $result = $rombels->map(function ($r) use ($hasMultiplePrograms) {
                    return [
                        'id' => (string) $r->id,
                        'nama_rombel' => $hasMultiplePrograms ? "{$r->nama_rombel} ({$r->kategori_program})" : $r->nama_rombel,
                    ];
                });

                return response()->json($result);
            } else {
                $uniqueNames = $query->distinct()
                    ->orderBy('ekstrakurikuler_rombel.nama_rombel')
                    ->pluck('ekstrakurikuler_rombel.nama_rombel')
                    ->unique()
                    ->values();

                $result = $uniqueNames->map(function ($name) {
                    return [
                        'id' => $name,
                        'nama_rombel' => $name,
                    ];
                });

                return response()->json($result);
            }
        }

        // Default: fetch sekolah
        $query = Sekolah::query()
            ->join('ekstrakurikuler', 'sekolah.kodlan', '=', 'ekstrakurikuler.sekolah_kodlan')
            ->join('ekstrakurikuler_rombel', 'ekstrakurikuler.id', '=', 'ekstrakurikuler_rombel.ekstrakurikuler_id')
            ->join('ekstrakurikuler_session', 'ekstrakurikuler_rombel.id', '=', 'ekstrakurikuler_session.ekstrakurikuler_rombel_id')
            ->where('ekstrakurikuler_session.status', 'selesai');

        if ($request->filled('kota')) {
            $query->where('sekolah.kota', $request->kota);
        }

        $sekolahList = $query->distinct()
            ->orderBy('sekolah.namasekolah')
            ->select('sekolah.kodlan', 'sekolah.namasekolah')
            ->get();

        return response()->json($sekolahList);
    }

    /**
     * AJAX: Ambil data tabel sesi dengan filter dan pagination.
     */
    public function data(Request $request): JsonResponse
    {
        $query = EkstrakurikulerSession::query()
            ->with([
                'rombel.ekstrakurikuler.sekolah',
                'instruktur',
                'laporanMengajar',
            ])
            ->where('status', 'selesai');

        if ($request->filled('kota')) {
            $query->whereHas('rombel.ekstrakurikuler.sekolah', function ($q) use ($request) {
                $q->where('kota', $request->kota);
            });
        }

        if ($request->filled('sekolah_kodlan')) {
            $query->whereHas('rombel.ekstrakurikuler.sekolah', function ($q) use ($request) {
                $q->where('kodlan', $request->sekolah_kodlan);
            });
        }

        if ($request->filled('rombel_id')) {
            $rVal = $request->rombel_id;
            if (is_numeric($rVal)) {
                $query->where('ekstrakurikuler_rombel_id', $rVal);
            } else {
                $query->whereHas('rombel', function ($q) use ($rVal) {
                    $q->where('nama_rombel', $rVal);
                });
            }
        }

        if ($request->filled('program')) {
            $query->whereHas('rombel.ekstrakurikuler', function ($q) use ($request) {
                $q->where('kategori_program', $request->program);
            });
        }

        if ($request->filled('instruktur_id')) {
            $query->where('user_id_instruktur', $request->instruktur_id);
        }

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $query->where(function ($q) use ($kw) {
                $q->whereHas('rombel.ekstrakurikuler.sekolah', fn($sq) => $sq->where('namasekolah', 'LIKE', "%{$kw}%"))
                  ->orWhereHas('laporanMengajar', fn($lq) => $lq->where('materi_pengajaran', 'LIKE', "%{$kw}%"))
                  ->orWhereHas('instruktur', fn($iq) => $iq->where('nama_lengkap', 'LIKE', "%{$kw}%"));
            });
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pelaksanaan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pelaksanaan', '<=', $request->tanggal_sampai);
        }

        $query->orderByDesc('tanggal_pelaksanaan')->orderBy('nomor_pertemuan');

        $perPage = $request->input('per_page', 25);
        if ($perPage === 'all' || $perPage == -1) {
            $totalCount = (clone $query)->count();
            $sessions = $query->paginate(max($totalCount, 1000));
        } else {
            $sessions = $query->paginate((int) $perPage);
        }

        $rows = $sessions->map(function (EkstrakurikulerSession $session) {
            $rombel     = $session->rombel;
            $ekskul     = $rombel?->ekstrakurikuler;
            $sekolah    = $ekskul?->sekolah;
            $laporan    = $session->laporanMengajar;
            $instruktur = $session->instruktur;

            $fotoKegiatanUrl = null;
            if ($laporan?->foto_kegiatan) {
                $fotoKegiatanUrl = rtrim(request()->getSchemeAndHttpHost(), '/') . '/storage/' . ltrim($laporan->foto_kegiatan, '/');
            }

            $fotoAbsensiUrl = null;
            if ($laporan?->foto_absensi_siswa) {
                $fotoAbsensiUrl = rtrim(request()->getSchemeAndHttpHost(), '/') . '/storage/' . ltrim($laporan->foto_absensi_siswa, '/');
            }

            $projectUrl = null;
            if ($laporan?->file_project) {
                $projectUrl = rtrim(request()->getSchemeAndHttpHost(), '/') . '/storage/' . ltrim($laporan->file_project, '/');
            }

            $tanggal = $laporan?->jadwal_mengajar ?? $session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal;

            return [
                'session_id'          => $session->id,
                'namsek'              => $sekolah?->namasekolah ?? '—',
                'kota'                => $sekolah?->kota ?? '—',
                'kategori_pengajaran' => $ekskul?->kategori_program ?? '—',
                'rombel'              => $rombel?->nama_rombel ?? '—',
                'instruktur_nama'     => $instruktur?->nama_lengkap ?? '—',
                'topik_materi'        => $laporan?->materi_pengajaran ?? '—',
                'tanggal_mengajar'    => $tanggal ? $tanggal->translatedFormat('d M Y') : '—',
                'tanggal_raw'         => $tanggal ? $tanggal->format('Y-m-d') : null,
                'pertemuan_ke'        => $session->nomor_pertemuan ?? '—',
                'jumlah_hadir'        => $laporan?->jumlah_siswa_hadir ?? 0,
                'foto_url'            => $fotoKegiatanUrl ?? $fotoAbsensiUrl,
                'foto_kegiatan_url'   => $fotoKegiatanUrl,
                'foto_absensi_url'    => $fotoAbsensiUrl,
                'project_url'         => $projectUrl,
                'print_url'           => route('ekstrakurikuler-session.print-session', ['session' => $session->id]),
            ];
        });

        return response()->json([
            'data'         => $rows,
            'current_page' => $sessions->currentPage(),
            'last_page'    => $sessions->lastPage(),
            'total'        => $sessions->total(),
            'per_page'     => 25,
        ]);
    }

    /**
     * Dispatch background job untuk generate export ZIP.
     */
    public function export(Request $request): JsonResponse
    {
        $filters = $request->only([
            'kota', 'sekolah_kodlan', 'rombel_id', 'tanggal_dari', 'tanggal_sampai',
        ]);
        $token = Str::uuid()->toString();

        Cache::put("agenda_export_{$token}", 'pending', 1800);
        GenerateAgendaExportJob::dispatch($filters, $token);

        return response()->json(['token' => $token, 'status' => 'pending']);
    }

    /**
     * Cek status export dan stream download file ZIP jika sudah selesai.
     */
    public function download(string $token): mixed
    {
        if (!preg_match('/^[0-9a-f\-]{36}$/', $token)) {
            abort(400, 'Token tidak valid.');
        }

        $status = Cache::get("agenda_export_{$token}");

        if ($status === null) {
            return response()->json(['status' => 'expired', 'message' => 'File sudah kedaluwarsa.'], 404);
        }

        if ($status === 'pending') {
            return response()->json(['status' => 'pending', 'message' => 'Sedang diproses...']);
        }

        if ($status === 'error') {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan. Silakan coba lagi.'], 500);
        }

        $filePath = "temp-exports/{$token}.zip";

        if (!Storage::disk('local')->exists($filePath)) {
            Cache::forget("agenda_export_{$token}");
            return response()->json(['status' => 'expired', 'message' => 'File tidak ditemukan.'], 404);
        }

        return Storage::disk('local')->download($filePath, 'Agenda_Kegiatan_Erlass.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * AJAX: Ambil data Invoice Tercetak / Berjalan khusus Salesman & Wilayah.
     */
    public function invoiceData(Request $request): JsonResponse
    {
        $query = InvoiceApproval::query()
            ->with([
                'sekolah',
                'ekstrakurikuler.sales',
                'rombel.ekstrakurikuler.sales',
                'items.rombel.ekstrakurikuler.sales',
            ]);

        // Status filter (Default: tercetak / approved)
        $statusFilter = $request->input('status', 'tercetak');
        if ($statusFilter === 'tercetak') {
            $query->where(function ($q) {
                $q->where('status', InvoiceApproval::STATUS_APPROVED)
                  ->orWhere('is_invoice_tercetak', true);
            });
        } elseif ($statusFilter === 'pending_akunting') {
            $query->where('status', InvoiceApproval::STATUS_PENDING_AKUNTING);
        } elseif ($statusFilter === 'pending_operasional') {
            $query->where('status', InvoiceApproval::STATUS_PENDING_OPERASIONAL);
        } elseif ($statusFilter === 'approved') {
            $query->where('status', InvoiceApproval::STATUS_APPROVED);
        }
        // if 'all', no status filter applied

        // Filter by Salesman
        if ($request->filled('salesman_id')) {
            $salesId = (int) $request->salesman_id;
            $query->where(function ($q) use ($salesId) {
                $q->whereHas('ekstrakurikuler', fn($eq) => $eq->where('user_id_sales', $salesId))
                  ->orWhereHas('rombel.ekstrakurikuler', fn($rq) => $rq->where('user_id_sales', $salesId))
                  ->orWhereHas('items.rombel.ekstrakurikuler', fn($iq) => $iq->where('user_id_sales', $salesId));
            });
        }

        // Filter by Wilayah / Kota
        if ($request->filled('kota')) {
            $kota = $request->kota;
            $query->whereHas('sekolah', fn($sq) => $sq->where('kota', $kota));
        }

        // Filter by Sekolah (Kodlan)
        if ($request->filled('sekolah_kodlan')) {
            $query->where('sekolah_kodlan', $request->sekolah_kodlan);
        }

        // Keyword Search (Sekolah, No Invoice, Program)
        if ($request->filled('keyword')) {
            $kw = trim($request->keyword);
            $query->where(function ($q) use ($kw) {
                $q->where('nomor_invoice', 'like', "%{$kw}%")
                  ->orWhereHas('sekolah', fn($sq) => $sq->where('namasekolah', 'like', "%{$kw}%"))
                  ->orWhere('kategori_program', 'like', "%{$kw}%")
                  ->orWhere('periode_label', 'like', "%{$kw}%");
            });
        }

        // Periode Filter
        if ($request->filled('periode_label')) {
            $query->where('periode_label', $request->periode_label);
        }

        // Sort: ID Desc (terbaru di atas)
        $query->orderByDesc('id');

        // Summary metrics sebelum pagination
        $totalCount = (clone $query)->count();
        $totalBillable = (clone $query)->sum('jumlah_siswa_billable');
        $uniqueSchools = (clone $query)->distinct()->count('sekolah_kodlan');

        $perPage = $request->input('per_page', 25);
        if ($perPage === 'all' || $perPage == -1) {
            $invoices = $query->paginate(max($totalCount, 1000));
        } else {
            $invoices = $query->paginate((int) $perPage);
        }

        $rows = $invoices->map(function (InvoiceApproval $inv) {
            $sales = $inv->sales;
            $sekolah = $inv->sekolah;

            $isTercetak = $inv->is_invoice_tercetak || $inv->status === InvoiceApproval::STATUS_APPROVED;

            return [
                'id'                   => $inv->id,
                'nomor_invoice'        => $inv->nomor_invoice,
                'is_draft'             => str_starts_with($inv->nomor_invoice ?? '', 'DRAFT'),
                'is_tercetak'          => (bool) $isTercetak,
                'status'               => $inv->status,
                'status_label'         => $inv->statusLabel(),
                'status_badge'         => $inv->statusBadgeClass(),
                'sekolah_nama'         => $sekolah?->namasekolah ?? '—',
                'sekolah_kodlan'       => $inv->sekolah_kodlan,
                'wilayah'              => $sekolah?->kota ?? '—',
                'pd'                   => $sekolah?->pd ?? null,
                'sales_nama'           => $sales?->nama_salesman ?? 'Belum Ditentukan',
                'group_leader'         => $sales?->group_leader ?? null,
                'sales_area'           => $sales?->area ?? null,
                'program_nama'         => $inv->program_nama,
                'skema_tagihan'        => $inv->skema_tagihan,
                'periode_label'        => $inv->periode_label ?? '—',
                'tahun_ajaran'         => $inv->tahun_ajaran ?? '2026/2027',
                'sesi_dari'            => $inv->sesi_dari,
                'sesi_sampai'          => $inv->sesi_sampai,
                'jumlah_sesi'          => $inv->jumlah_sesi ?? 4,
                'jumlah_siswa_billable'=> $inv->billable_efektif,
                'total_rombel'         => $inv->total_rombel ?? 1,
                'tanggal_tercetak'     => $inv->akunting_approved_at ? $inv->akunting_approved_at->translatedFormat('d M Y') : ($inv->serah_terima_akunting_at ? $inv->serah_terima_akunting_at->translatedFormat('d M Y') : $inv->created_at->translatedFormat('d M Y')),
                'has_pdf'              => $inv->hasPdf(),
            ];
        });

        return response()->json([
            'data'         => $rows,
            'current_page' => $invoices->currentPage(),
            'last_page'    => $invoices->lastPage(),
            'per_page'     => $invoices->perPage(),
            'total'        => $invoices->total(),
            'from'         => $invoices->firstItem() ?? 0,
            'to'           => $invoices->lastItem() ?? 0,
            'summary'      => [
                'total_invoices' => $totalCount,
                'total_siswa'    => $totalBillable,
                'total_sekolah'  => $uniqueSchools,
            ]
        ]);
    }
}

