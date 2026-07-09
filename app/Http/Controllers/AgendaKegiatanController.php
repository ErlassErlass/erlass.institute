<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateAgendaExportJob;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\Sekolah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $totalSesi = EkstrakurikulerSession::where('status', 'selesai')->count();

        return view('agenda-kegiatan.index', compact('wilayahList', 'totalSesi'));
    }

    /**
     * AJAX: Cascading dropdown filter.
     * - Jika ada ?kota=X  => return list sekolah di kota X yang punya sesi selesai
     * - Jika ada ?sekolah_kodlan=X => return list rombel di sekolah X yang punya sesi selesai
     */
    public function filter(Request $request): JsonResponse
    {
        if ($request->filled('kota')) {
            $sekolahList = Sekolah::query()
                ->join('ekstrakurikuler', 'sekolah.kodlan', '=', 'ekstrakurikuler.sekolah_kodlan')
                ->join('ekstrakurikuler_rombel', 'ekstrakurikuler.id', '=', 'ekstrakurikuler_rombel.ekstrakurikuler_id')
                ->join('ekstrakurikuler_session', 'ekstrakurikuler_rombel.id', '=', 'ekstrakurikuler_session.ekstrakurikuler_rombel_id')
                ->where('ekstrakurikuler_session.status', 'selesai')
                ->where('sekolah.kota', $request->kota)
                ->distinct()
                ->orderBy('sekolah.namasekolah')
                ->select('sekolah.kodlan', 'sekolah.namasekolah')
                ->get();

            return response()->json($sekolahList);
        }

        if ($request->filled('sekolah_kodlan')) {
            $rombelList = EkstrakurikulerRombel::query()
                ->join('ekstrakurikuler', 'ekstrakurikuler_rombel.ekstrakurikuler_id', '=', 'ekstrakurikuler.id')
                ->join('ekstrakurikuler_session', 'ekstrakurikuler_rombel.id', '=', 'ekstrakurikuler_session.ekstrakurikuler_rombel_id')
                ->where('ekstrakurikuler_session.status', 'selesai')
                ->where('ekstrakurikuler.sekolah_kodlan', $request->sekolah_kodlan)
                ->distinct()
                ->orderBy('ekstrakurikuler_rombel.nama_rombel')
                ->select('ekstrakurikuler_rombel.id', 'ekstrakurikuler_rombel.nama_rombel')
                ->get();

            return response()->json($rombelList);
        }

        return response()->json([]);
    }

    /**
     * AJAX: Ambil data tabel sesi dengan filter dan pagination.
     */
    public function data(Request $request): JsonResponse
    {
        $query = EkstrakurikulerSession::query()
            ->with([
                'rombel.ekstrakurikuler.sekolah',
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
            $query->where('ekstrakurikuler_rombel_id', $request->rombel_id);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pelaksanaan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pelaksanaan', '<=', $request->tanggal_sampai);
        }

        $query->orderByDesc('tanggal_pelaksanaan')->orderBy('nomor_pertemuan');

        $sessions = $query->paginate(25);

        $rows = $sessions->map(function (EkstrakurikulerSession $session) {
            $rombel  = $session->rombel;
            $ekskul  = $rombel?->ekstrakurikuler;
            $sekolah = $ekskul?->sekolah;
            $laporan = $session->laporanMengajar;
            $fotoUrl = null;

            if ($laporan?->foto_kegiatan) {
                $fotoUrl = rtrim(request()->getSchemeAndHttpHost(), '/') . '/storage/' . ltrim($laporan->foto_kegiatan, '/');
            }

            $tanggal = $session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal;

            return [
                'session_id'          => $session->id,
                'namsek'              => $sekolah?->namasekolah ?? '—',
                'kota'                => $sekolah?->kota ?? '—',
                'kategori_pengajaran' => $ekskul?->kategori_program ?? '—',
                'rombel'              => $rombel?->nama_rombel ?? '—',
                'tanggal_mengajar'    => $tanggal ? $tanggal->translatedFormat('d M Y') : '—',
                'tanggal_raw'         => $tanggal ? $tanggal->format('Y-m-d') : null,
                'pertemuan_ke'        => $session->nomor_pertemuan ?? '—',
                'jumlah_hadir'        => $laporan?->jumlah_siswa_hadir ?? 0,
                'foto_url'            => $fotoUrl,
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

        if (!Storage::exists($filePath)) {
            Cache::forget("agenda_export_{$token}");
            return response()->json(['status' => 'expired', 'message' => 'File tidak ditemukan.'], 404);
        }

        return Storage::download($filePath, 'Agenda_Kegiatan_Erlass.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }
}
