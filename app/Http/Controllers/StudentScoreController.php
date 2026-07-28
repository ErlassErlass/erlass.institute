<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerRombel;
use App\Models\RombelInstructorHistory;
use App\Models\StudentScore;
use App\Services\ReportCardService;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentScoreController extends Controller
{
    protected $reportCardService;
    protected $certificateService;

    public function __construct(ReportCardService $reportCardService, CertificateService $certificateService)
    {
        $this->reportCardService = $reportCardService;
        $this->certificateService = $certificateService;
    }

    /**
     * Display a list of Rombels for grading.
     */
    public function rombelList()
    {
        $user = Auth::user();
        
        if ($user->role === 'instruktur') {
            $rombels = EkstrakurikulerRombel::where('user_id_instruktur', $user->id)
                ->orWhere('user_id_asisten', $user->id)
                ->with(['ekstrakurikuler.sekolah', 'instruktur'])
                ->get();
        } else {
            $rombels = EkstrakurikulerRombel::with(['ekstrakurikuler.sekolah', 'instruktur'])
                ->get();
        }

        return view('student_scores.rombel_list', compact('rombels'));
    }

    /**
     * Display student scores for a specific rombel.
     */
    public function index(EkstrakurikulerRombel $rombel)
    {
        $this->authorizeRombelAccess($rombel);

        $siswaList = $rombel->siswaAktif()->orderBy('nama_lengkap', 'asc')->get();
        
        // Find or create scores for active students, and recalculate attendance
        $scores = [];
        foreach ($siswaList as $siswa) {
            $score = StudentScore::firstOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'periode' => 'Semester 1 2026',
                ],
                [
                    'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
                    'nilai_kehadiran' => 0,
                ]
            );
            
            // Recalculate attendance rate dynamically
            $score->nilai_kehadiran = $score->hitungNilaiKehadiran();
            $score->save();
            
            $scores[$siswa->id] = $score;
        }

        return view('student_scores.index', compact('rombel', 'siswaList', 'scores'));
    }

    /**
     * Show the bulk input form for student scores.
     */
    public function bulkInputForm(EkstrakurikulerRombel $rombel)
    {
        $this->authorizeRombelWriteAccess($rombel); // Former instructors cannot input scores

        $siswaList = $rombel->siswaAktif()->orderBy('nama_lengkap', 'asc')->get();
        
        $scores = [];
        foreach ($siswaList as $siswa) {
            $score = StudentScore::firstOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'ekstrakurikuler_rombel_id' => $rombel->id,
                    'periode' => 'Semester 1 2026',
                ],
                [
                    'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
                    'nilai_kehadiran' => 0,
                ]
            );
            
            // Sync attendance
            $score->nilai_kehadiran = $score->hitungNilaiKehadiran();
            $score->save();
            
            $scores[$siswa->id] = $score;
        }

        return view('student_scores.bulk_input', compact('rombel', 'siswaList', 'scores'));
    }

    /**
     * Store bulk student scores.
     */
    public function storeBulk(Request $request, EkstrakurikulerRombel $rombel)
    {
        $this->authorizeRombelWriteAccess($rombel); // Former instructors cannot store scores

        $limit = min(8, $rombel->total_pertemuan ?? 4);

        $rules = [
            'scores' => 'required|array',
            'scores.*.projek_scratch' => 'nullable|string|max:255',
            'scores.*.catatan_guru' => 'nullable|string',
        ];

        for ($i = 1; $i <= $limit; $i++) {
            $rules["scores.*.nilai_tugas_{$i}"] = 'nullable|numeric|between:0,100';
            $rules["scores.*.nilai_sikap_{$i}"] = 'nullable|numeric|between:0,100';
            $rules["scores.*.nilai_proyek_{$i}"] = 'nullable|numeric|between:0,100';
        }

        $request->validate($rules);

        $inputScores = $request->input('scores');

        foreach ($inputScores as $siswaId => $data) {
            $score = StudentScore::where([
                'siswa_id' => $siswaId,
                'ekstrakurikuler_rombel_id' => $rombel->id,
                'periode' => 'Semester 1 2026',
            ])->first();

            if ($score) {
                if ($score->finalized_at) {
                    continue; // Skip if already finalized
                }

                $score->update(array_merge($data, [
                    'updated_by'            => Auth::id(),
                    'instruktur_pengisi_id' => Auth::id(), // Level 1: track who filled this score
                ]));
            }
        }

        return redirect()->route('student-scores.index', $rombel->id)
            ->with('success', 'Nilai siswa berhasil disimpan.');
    }

    /**
     * Finalize grading, generate report cards and certificates.
     */
    public function finalize(EkstrakurikulerRombel $rombel)
    {
        $this->authorizeRombelWriteAccess($rombel); // Former instructors cannot finalize

        $siswaList = $rombel->siswaAktif()->get();
        $scores = StudentScore::where('ekstrakurikuler_rombel_id', $rombel->id)
            ->whereIn('siswa_id', $siswaList->pluck('id'))
            ->get();

        // Check if all scores are complete (all 4 input fields for tugas, sikap, proyek are filled)
        foreach ($scores as $score) {
            if (!$score->isComplete()) {
                return redirect()->back()->with('error', 'Tidak dapat memfinalisasi nilai. Harap lengkapi semua 4 input nilai (Tugas, Sikap, Proyek) untuk siswa: ' . $score->siswa->nama_lengkap);
            }
        }

        // Finalize scores and trigger PDF generation
        foreach ($scores as $score) {
            $score->update([
                'finalized_at' => now(),
                'finalized_by' => Auth::id(),
            ]);

            // Generate report card PDF
            $this->reportCardService->generate($score);

            // Generate certificate PDF if eligible (attendance rate >= 75%)
            if ($this->certificateService->isEligible($score)) {
                $this->certificateService->generate($score);
            }
        }

        return redirect()->route('student-scores.index', $rombel->id)
            ->with('success', 'Nilai berhasil difinalisasi. Rapor dan Sertifikat telah digenerasi.');
    }

    /**
     * Helper to authorize Rombel access.
     *
     * Level 1: Active instructors → full access (read + write).
     * Level 2: Former instructors (found in rombel_instructor_history) → READ-ONLY access.
     *          They cannot use bulkInputForm() or storeBulk() or finalize().
     */
    private function authorizeRombelAccess(EkstrakurikulerRombel $rombel, bool $requireWriteAccess = false)
    {
        $user = Auth::user();

        if ($user->hasAdminAccess()) {
            return; // Admins always have full access
        }

        if ($user->role === 'instruktur') {
            $isActiveInstructor = $rombel->user_id_instruktur === $user->id
                               || $rombel->user_id_asisten === $user->id;

            if ($isActiveInstructor) {
                return; // Active instructor: full access
            }

            // Level 2: Check if user was EVER an instructor in this rombel
            $wasFormerInstructor = RombelInstructorHistory::wasEverInstructor($rombel->id, $user->id);

            if ($wasFormerInstructor) {
                if ($requireWriteAccess) {
                    abort(403, 'Akses ditolak: Anda hanya memiliki akses baca (read-only) sebagai instruktur lama di rombel ini.');
                }
                return; // Former instructor: read-only access granted
            }

            abort(403, 'Akses ditolak: Anda bukan instruktur atau asisten di rombel ini.');
        }

        abort(403, 'Akses ditolak: Anda tidak memiliki akses ke rombel ini.');
    }

    /**
     * Authorization specifically for write operations (bulk input, finalize).
     * Former instructors (read-only) will be denied.
     */
    private function authorizeRombelWriteAccess(EkstrakurikulerRombel $rombel): void
    {
        $this->authorizeRombelAccess($rombel, requireWriteAccess: true);
    }
}
