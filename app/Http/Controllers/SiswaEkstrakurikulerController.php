<?php

namespace App\Http\Controllers;

use App\Models\SiswaEkstrakurikuler;
use App\Models\Siswa;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SiswaEkstrakurikulerController extends Controller
{
    /**
     * Menampilkan halaman enrollment siswa untuk ekstrakurikuler tertentu.
     */
    public function index(Ekstrakurikuler $ekstrakurikuler, Request $request)
    {
        $this->authorize('view', $ekstrakurikuler);

        $query = SiswaEkstrakurikuler::with(['siswa', 'rombel'])
            ->where('ekstrakurikuler_id', $ekstrakurikuler->id);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan rombel
        if ($request->filled('rombel_id')) {
            $query->where('ekstrakurikuler_rombel_id', $request->rombel_id);
        }

        // Search siswa
        if ($request->filled('search')) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_lengkap', 'like', '%' . $request->search . '%')
                  ->orWhere('nisn', 'like', '%' . $request->search . '%');
            });
        }

        $enrollments = $query->orderBy('tanggal_daftar', 'desc')->paginate(20);
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
            'catatan' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $rombel = EkstrakurikulerRombel::find($request->ekstrakurikuler_rombel_id);
            
            // Validasi rombel masih bisa menampung siswa
            $currentEnrollments = $rombel->activeEnrollments()->count();
            $newEnrollments = count($request->siswa_ids);
            
            if (($currentEnrollments + $newEnrollments) > $rombel->getJumlahSiswaAktual()) {
                return redirect()->back()->with('error', 'Rombel sudah penuh atau melebihi kapasitas.');
            }

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
                    'catatan' => $request->catatan
                ]);

                $successCount++;
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
            \Log::error('Error saat mendaftarkan siswa: ' . $e->getMessage());
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
            'catatan' => 'nullable|string|max:1000'
        ]);

        try {
            $enrollment->update([
                'ekstrakurikuler_rombel_id' => $request->ekstrakurikuler_rombel_id,
                'status' => $request->status,
                'tanggal_keluar' => $request->tanggal_keluar,
                'alasan_keluar' => $request->alasan_keluar,
                'catatan' => $request->catatan
            ]);

            return redirect()->route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment])
                ->with('success', 'Data enrollment berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error saat update enrollment: ' . $e->getMessage());
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
            'alasan_keluar' => 'required|string|max:1000'
        ]);

        try {
            $enrollment->withdraw($request->alasan_keluar);

            return redirect()->route('ekstrakurikuler.enrollment.index', $ekstrakurikuler)
                ->with('success', 'Siswa berhasil dikeluarkan dari program ekstrakurikuler.');

        } catch (\Exception $e) {
            \Log::error('Error saat mengeluarkan siswa: ' . $e->getMessage());
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
            'alasan' => 'nullable|string|max:1000'
        ]);

        try {
            $newRombel = EkstrakurikulerRombel::find($request->new_rombel_id);
            
            // Validasi rombel baru masih bisa menampung
            if ($newRombel->activeEnrollments()->count() >= $newRombel->getJumlahSiswaAktual()) {
                return redirect()->back()->with('error', 'Rombel tujuan sudah penuh.');
            }

            $enrollment->transfer($request->new_rombel_id, $request->alasan);

            return redirect()->route('ekstrakurikuler.enrollment.show', [$ekstrakurikuler, $enrollment])
                ->with('success', 'Siswa berhasil dipindahkan ke rombel baru.');

        } catch (\Exception $e) {
            \Log::error('Error saat memindahkan siswa: ' . $e->getMessage());
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
            \Log::error('Error saat mengaktifkan siswa: ' . $e->getMessage());
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
            \Log::error('Error saat meluluskan siswa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat meluluskan siswa.');
        }
    }

    /**
     * Bulk actions untuk enrollment.
     */
    public function bulkAction(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        $request->validate([
            'enrollment_ids' => 'required|array|min:1',
            'enrollment_ids.*' => 'exists:siswa_ekstrakurikuler,id',
            'action' => 'required|in:activate,deactivate,graduate,delete',
            'bulk_alasan' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $enrollments = SiswaEkstrakurikuler::whereIn('id', $request->enrollment_ids)
                ->where('ekstrakurikuler_id', $ekstrakurikuler->id)
                ->get();

            $successCount = 0;

            foreach ($enrollments as $enrollment) {
                switch ($request->action) {
                    case 'activate':
                        if ($enrollment->activate()) $successCount++;
                        break;
                    case 'deactivate':
                        if ($enrollment->deactivate($request->bulk_alasan)) $successCount++;
                        break;
                    case 'graduate':
                        if ($enrollment->graduate()) $successCount++;
                        break;
                    case 'delete':
                        $enrollment->delete();
                        $successCount++;
                        break;
                }
            }

            DB::commit();

            return redirect()->back()->with('success', "Berhasil memproses {$successCount} enrollment siswa.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saat bulk action enrollment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses data.');
        }
    }
}