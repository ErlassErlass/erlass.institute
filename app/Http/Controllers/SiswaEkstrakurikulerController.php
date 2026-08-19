<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkImportByRombelRequest;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\Siswa;
use App\Models\SiswaEkstrakurikuler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\WelcomeParentNotification;

class SiswaEkstrakurikulerController extends Controller
{
    /**
     * Menampilkan halaman enrollment siswa untuk ekstrakurikuler tertentu.
     */
    public function index(Ekstrakurikuler $ekstrakurikuler, Request $request)
    {
        $this->authorize('view', $ekstrakurikuler);

        $query = SiswaEkstrakurikuler::select('siswa_ekstrakurikuler.*')
            ->join('siswa', 'siswa_ekstrakurikuler.siswa_id', '=', 'siswa.id')
            ->with(['siswa', 'rombel'])
            ->where('siswa_ekstrakurikuler.ekstrakurikuler_id', $ekstrakurikuler->id);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('siswa_ekstrakurikuler.status', $request->status);
        }

        // Filter berdasarkan rombel
        if ($request->filled('rombel_id')) {
            $query->where('siswa_ekstrakurikuler.ekstrakurikuler_rombel_id', $request->rombel_id);
        }

        // Search siswa (Nama, NISN, Kelas)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('siswa.nama_lengkap', 'like', '%'.$search.'%')
                    ->orWhere('siswa.nisn', 'like', '%'.$search.'%')
                    ->orWhere('siswa.kelas', 'like', '%'.$search.'%');
            });
        }

        // Filter & Sorting: Default NISN Ascending as requested
        $sort = $request->get('sort', 'nisn_asc');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('siswa.nama_lengkap', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('siswa.nama_lengkap', 'desc');
                break;
            case 'nisn_desc':
                $query->orderBy('siswa.nisn', 'desc');
                break;
            case 'date_desc':
                $query->orderBy('siswa_ekstrakurikuler.tanggal_daftar', 'desc');
                break;
            case 'nisn_asc':
            default:
                $query->orderBy('siswa.nisn', 'asc');
                break;
        }

        // Per-page: support 'all' untuk tampilkan semua data
        $perPage = $request->get('per_page', 25);
        if ($perPage === 'all') {
            $enrollments = $query->get();
            // Wrap dalam LengthAwarePaginator kosong agar kompatibel dengan view
            $enrollments = new \Illuminate\Pagination\LengthAwarePaginator(
                $enrollments,
                $enrollments->count(),
                $enrollments->count() ?: 1,
                1,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            $enrollments = $query->paginate((int) $perPage)->withQueryString();
        }

        $rombels = $ekstrakurikuler->rombels;

        return view('ekstrakurikuler.enrollment.index', compact('ekstrakurikuler', 'enrollments', 'rombels'));
    }

    /**
     * Menampilkan form untuk menambah siswa ke ekstrakurikuler.
     */
    public function create(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        $rombels = $ekstrakurikuler->rombels;

        // Ambil siswa dari sekolah yang sama dan belum terdaftar di ekstrakurikuler ini
        $availableSiswa = Siswa::where('sekolah_kodlan', $ekstrakurikuler->sekolah_kodlan)
            ->whereDoesntHave('ekstrakurikulers', function ($query) use ($ekstrakurikuler) {
                $query->where('ekstrakurikuler.id', $ekstrakurikuler->id)
                    ->where('siswa_ekstrakurikuler.status', '!=', 'keluar');
            })
            ->orderBy('nama_lengkap')
            ->get();

        return view('ekstrakurikuler.enrollment.create', compact('ekstrakurikuler', 'rombels', 'availableSiswa'));
    }

    /**
     * Menyimpan enrollment siswa baru.
     */
    public function store(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:siswa,id',
            'ekstrakurikuler_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'tanggal_daftar' => 'required|date',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $rombel = EkstrakurikulerRombel::find($request->ekstrakurikuler_rombel_id);

            // Validasi rombel masih bisa menampung siswa - REMOVED due to logic error (current > current is always false, but equality check logic was flawed too as it compared against current count not max capacity)
            // No capacity limit defined strictly in DB yet.
            $currentEnrollments = $rombel->activeEnrollments()->count();
            $newEnrollments = count($request->siswa_ids);
            
            // Logic removed: if (($currentEnrollments + $newEnrollments) > $rombel->getJumlahSiswaAktual())

            $successCount = 0;
            $duplicateCount = 0;

            foreach ($request->siswa_ids as $siswaId) {
                // Cek apakah siswa sudah terdaftar di ekstrakurikuler ini
                $existing = SiswaEkstrakurikuler::where('siswa_id', $siswaId)
                    ->where('ekstrakurikuler_id', $ekstrakurikuler->id)
                    ->where('status', '!=', 'keluar')
                    ->first();

                if ($existing) {
                    $duplicateCount++;

                    continue;
                }

                // Validasi siswa dari sekolah yang sama
                $siswa = Siswa::find($siswaId);
                if ($siswa->sekolah_kodlan !== $ekstrakurikuler->sekolah_kodlan) {
                    continue;
                }

                SiswaEkstrakurikuler::create([
                    'siswa_id' => $siswaId,
                    'ekstrakurikuler_id' => $ekstrakurikuler->id,
                    'ekstrakurikuler_rombel_id' => $request->ekstrakurikuler_rombel_id,
                    'status' => 'aktif',
                    'tanggal_daftar' => $request->tanggal_daftar,
                    'catatan' => $request->catatan,
                ]);

                $successCount++;

                // Trigger Welcome Message if parent phone number is present
                if ($siswa->no_hp_orangtua) {
                    try {
                        $rombelModel = EkstrakurikulerRombel::find($request->ekstrakurikuler_rombel_id);
                        $siswa->notify(new WelcomeParentNotification($siswa, $rombelModel));
                    } catch (\Exception $e) {
                        \Log::error('Gagal mengirim WhatsApp Welcome Message ke siswa ID: ' . $siswa->id . '. Error: ' . $e->getMessage());
                    }
                }
            }

            DB::commit();

            $message = "Berhasil mendaftarkan {$successCount} siswa.";
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} siswa sudah terdaftar sebelumnya.";
            }

            return redirect()->route('ekstrakurikuler.enrollment.index', $ekstrakurikuler)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saat mendaftarkan siswa: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mendaftarkan siswa.');
        }
    }

    /**
     * Menampilkan detail enrollment siswa.
     */
    public function show(Ekstrakurikuler $ekstrakurikuler, SiswaEkstrakurikuler $enrollment)
    {
        $this->authorize('view', $ekstrakurikuler);

        $enrollment->load(['siswa', 'rombel', 'creator', 'updater']);

        return view('ekstrakurikuler.enrollment.show', compact('ekstrakurikuler', 'enrollment'));
    }

    /**
     * Menampilkan form edit enrollment siswa.
     */
    public function edit(Ekstrakurikuler $ekstrakurikuler, SiswaEkstrakurikuler $enrollment)
    {
        $this->authorize('update', $ekstrakurikuler);

        $rombels = $ekstrakurikuler->rombels;

        return view('ekstrakurikuler.enrollment.edit', compact('ekstrakurikuler', 'enrollment', 'rombels'));
    }

    /**
     * Update enrollment siswa.
     */
    public function update(Request $request, Ekstrakurikuler $ekstrakurikuler, SiswaEkstrakurikuler $enrollment)
    {
        $this->authorize('update', $ekstrakurikuler);

        $request->validate([
            'ekstrakurikuler_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'status' => 'required|in:aktif,lulus,keluar,pindah,nonaktif',
            'tanggal_keluar' => 'nullable|date|required_if:status,lulus,keluar',
            'alasan_keluar' => 'nullable|string|max:1000|required_if:status,keluar',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            $enrollment->update([
                'ekstrakurikuler_rombel_id' => $request->ekstrakurikuler_rombel_id,
                'status' => $request->status,
                'tanggal_keluar' => $request->tanggal_keluar,
                'alasan_keluar' => $request->alasan_keluar,
                'catatan' => $request->catatan,
            ]);

            return redirect()->route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment])
                ->with('success', 'Data enrollment berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error saat update enrollment: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    /**
     * Mengeluarkan siswa dari ekstrakurikuler.
     */
    public function withdraw(Request $request, Ekstrakurikuler $ekstrakurikuler, SiswaEkstrakurikuler $enrollment)
    {
        $this->authorize('update', $ekstrakurikuler);

        $request->validate([
            'alasan_keluar' => 'required|string|max:1000',
        ]);

        try {
            $enrollment->withdraw($request->alasan_keluar);

            return redirect()->route('ekstrakurikuler.enrollment.index', $ekstrakurikuler)
                ->with('success', 'Siswa berhasil dikeluarkan dari program ekstrakurikuler.');

        } catch (\Exception $e) {
            \Log::error('Error saat mengeluarkan siswa: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengeluarkan siswa.');
        }
    }

    /**
     * Memindahkan siswa ke rombel lain.
     */
    public function transfer(Request $request, Ekstrakurikuler $ekstrakurikuler, SiswaEkstrakurikuler $enrollment)
    {
        $this->authorize('update', $ekstrakurikuler);

        $request->validate([
            'new_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'alasan' => 'nullable|string|max:1000',
        ]);

        try {
            $newRombel = EkstrakurikulerRombel::find($request->new_rombel_id);

            // Validasi rombel baru masih bisa menampung - REMOVED
            // if ($newRombel->activeEnrollments()->count() >= $newRombel->getJumlahSiswaAktual()) {
            //    return redirect()->back()->with('error', 'Rombel tujuan sudah penuh.');
            // }

            $enrollment->transfer($request->new_rombel_id, $request->alasan);

            return redirect()->route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment])
                ->with('success', 'Siswa berhasil dipindahkan ke rombel baru.');

        } catch (\Exception $e) {
            \Log::error('Error saat memindahkan siswa: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memindahkan siswa.');
        }
    }

    /**
     * Aktifkan kembali enrollment siswa.
     */
    public function activate(Ekstrakurikuler $ekstrakurikuler, SiswaEkstrakurikuler $enrollment)
    {
        $this->authorize('update', $ekstrakurikuler);

        try {
            if ($enrollment->activate()) {
                return redirect()->back()->with('success', 'Siswa berhasil diaktifkan kembali.');
            } else {
                return redirect()->back()->with('error', 'Tidak dapat mengaktifkan siswa ini.');
            }
        } catch (\Exception $e) {
            \Log::error('Error saat mengaktifkan siswa: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengaktifkan siswa.');
        }
    }

    /**
     * Meluluskan siswa dari program.
     */
    public function graduate(Ekstrakurikuler $ekstrakurikuler, SiswaEkstrakurikuler $enrollment)
    {
        $this->authorize('update', $ekstrakurikuler);

        try {
            if ($enrollment->graduate()) {
                return redirect()->route('ekstrakurikuler.enrollment.index', $ekstrakurikuler)
                    ->with('success', 'Siswa berhasil diluluskan dari program.');
            } else {
                return redirect()->back()->with('error', 'Tidak dapat meluluskan siswa ini.');
            }
        } catch (\Exception $e) {
            \Log::error('Error saat meluluskan siswa: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat meluluskan siswa.');
        }
    }

    /**
     * Bulk actions untuk enrollment.
     */
    public function bulkAction(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        if ($request->has('enrollment_ids') && is_string($request->enrollment_ids)) {
            $request->merge([
                'enrollment_ids' => explode(',', $request->enrollment_ids)
            ]);
        }

        $request->validate([
            'enrollment_ids'     => 'required|array|min:1',
            'enrollment_ids.*'   => 'exists:siswa_ekstrakurikuler,id',
            'action'             => 'required|in:activate,deactivate,graduate,delete,withdraw,transfer',
            'bulk_alasan'        => 'nullable|string|max:1000',
            'bulk_rombel_tujuan' => 'nullable|exists:ekstrakurikuler_rombel,id',
        ]);

        // Validasi khusus per aksi
        if ($request->action === 'transfer' && ! $request->filled('bulk_rombel_tujuan')) {
            return redirect()->back()->with('error', 'Pilih rombel tujuan untuk aksi Pindah Rombel.');
        }

        try {
            DB::beginTransaction();

            $enrollments = SiswaEkstrakurikuler::whereIn('id', $request->enrollment_ids)
                ->where('ekstrakurikuler_id', $ekstrakurikuler->id)
                ->get();

            $successCount = 0;
            $rombelTujuan = $request->filled('bulk_rombel_tujuan')
                ? EkstrakurikulerRombel::find($request->bulk_rombel_tujuan)
                : null;

            foreach ($enrollments as $enrollment) {
                switch ($request->action) {
                    case 'activate':
                        if ($enrollment->activate()) {
                            $successCount++;
                        }
                        break;
                    case 'deactivate':
                        if ($enrollment->deactivate($request->bulk_alasan)) {
                            $successCount++;
                        }
                        break;
                    case 'graduate':
                        if ($enrollment->graduate()) {
                            $successCount++;
                        }
                        break;
                    case 'delete':
                        $enrollment->delete();
                        $successCount++;
                        break;
                    case 'withdraw':
                        // Keluarkan siswa dengan alasan
                        if ($enrollment->status === 'aktif' && $enrollment->withdraw($request->bulk_alasan)) {
                            $successCount++;
                        }
                        break;
                    case 'transfer':
                        // Pindah rombel: tandai lama sebagai pindah, buat enrollment baru
                        if ($enrollment->status === 'aktif' && $rombelTujuan && $rombelTujuan->id !== $enrollment->ekstrakurikuler_rombel_id) {
                            $enrollment->update([
                                'status'        => 'pindah',
                                'tanggal_keluar' => now()->toDateString(),
                                'alasan_keluar' => $request->bulk_alasan ?: 'Pindah rombel (bulk)',
                            ]);
                            SiswaEkstrakurikuler::create([
                                'siswa_id'                   => $enrollment->siswa_id,
                                'ekstrakurikuler_id'         => $ekstrakurikuler->id,
                                'ekstrakurikuler_rombel_id'  => $rombelTujuan->id,
                                'status'                     => 'aktif',
                                'tanggal_daftar'             => now()->toDateString(),
                                'catatan'                    => 'Pindah dari ' . ($enrollment->rombel->nama_rombel ?? '-') . ' (bulk transfer)',
                                'created_by'                 => auth()->id(),
                                'updated_by'                 => auth()->id(),
                            ]);
                            $successCount++;
                        }
                        break;
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Berhasil memproses {$successCount} enrollment siswa.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saat bulk action enrollment: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data.');
        }
    }

    /**
     * Mendaftarkan semua siswa dari rombel (kelas) tertentu secara bulk.
     */
    public function bulkImportByRombel(BulkImportByRombelRequest $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        try {
            DB::beginTransaction();

            $rombel = EkstrakurikulerRombel::find($request->ekstrakurikuler_rombel_id);

            // Ambil semua siswa dari rombel (kelas akademik, misal: 10-A) yang diminta dari sekolah yang sama
            $siswaFromRombel = Siswa::where('sekolah_kodlan', $ekstrakurikuler->sekolah_kodlan)
                ->where('rombel', $request->rombel) // Disini 'rombel' adalah Kelas Akademik siswa
                ->whereDoesntHave('ekstrakurikulers', function ($query) use ($ekstrakurikuler) {
                    $query->where('ekstrakurikuler.id', $ekstrakurikuler->id)
                        ->where('siswa_ekstrakurikuler.status', '!=', 'keluar');
                })
                ->get();

            if ($siswaFromRombel->isEmpty()) {
                return redirect()->back()->with('error', 'Tidak ada siswa yang dapat didaftarkan dari kelas '.$request->rombel.'. Siswa mungkin sudah terdaftar atau kelas tidak ditemukan.');
            }

            // Validasi kapasitas rombel ekstrakurikuler - REMOVED due to logic error
            // checks were comparing future count against current count
            $currentEnrollments = $rombel->activeEnrollments()->count();
            $newEnrollments = $siswaFromRombel->count();

            $successCount = 0;

            foreach ($siswaFromRombel as $siswa) {
                SiswaEkstrakurikuler::create([
                    'siswa_id' => $siswa->id,
                    'ekstrakurikuler_id' => $ekstrakurikuler->id,
                    'ekstrakurikuler_rombel_id' => $request->ekstrakurikuler_rombel_id, // Target Group ID
                    'status' => 'aktif',
                    'tanggal_daftar' => $request->tanggal_daftar,
                    'catatan' => $request->catatan,
                ]);

                $successCount++;

                // Trigger Welcome Message if parent phone number is present
                if ($siswa->no_hp_orangtua) {
                    try {
                        $rombelModel = EkstrakurikulerRombel::find($request->ekstrakurikuler_rombel_id);
                        $siswa->notify(new WelcomeParentNotification($siswa, $rombelModel));
                    } catch (\Exception $e) {
                        \Log::error('Gagal mengirim WhatsApp Welcome Message ke siswa ID: ' . $siswa->id . '. Error: ' . $e->getMessage());
                    }
                }
            }

            DB::commit();

            return redirect()->route('ekstrakurikuler.enrollment.index', $ekstrakurikuler)
                ->with('success', "Berhasil mendaftarkan {$successCount} siswa dari kelas {$request->rombel} ke program ekstrakurikuler.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saat bulk import by rombel: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat mendaftarkan siswa dari rombel.');
        }
    }

    /**
     * Mendapatkan daftar rombel yang tersedia dari sekolah.
     */
    public function getAvailableRombels(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('view', $ekstrakurikuler);

        $rombels = Siswa::where('sekolah_kodlan', $ekstrakurikuler->sekolah_kodlan)
            ->distinct()
            ->pluck('rombel')
            ->filter()
            ->sort()
            ->values();

        return response()->json($rombels);
    }

    /**
     * Import siswa ke program ekstrakurikuler (bulk import rombel-rombel)
     */
    public function importSiswaProgram(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $importer = new \App\Services\SiswaImporterService();
            $results = $importer->importToProgram(
                $file->getRealPath(),
                $file->getClientOriginalExtension(),
                $ekstrakurikuler
            );

            if ($results['failed'] > 0 && $results['success'] === 0) {
                return redirect()->back()
                    ->withErrors(['import_errors' => $results['errors']])
                    ->with('error', 'Gagal mengimpor data siswa.');
            }

            $message = "Berhasil mengimpor {$results['success']} siswa.";
            if ($results['failed'] > 0) {
                $message .= " Namun {$results['failed']} baris gagal diproses.";
                return redirect()->back()
                    ->with('success', $message)
                    ->withErrors(['import_errors' => $results['errors']]);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error saat import siswa program: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses berkas impor: ' . $e->getMessage());
        }
    }
}
