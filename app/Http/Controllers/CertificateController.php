<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\ReportCard;
use App\Models\StudentScore;
use App\Models\EkstrakurikulerRombel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Display a listing of report cards and certificates.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = StudentScore::with([
            'siswa', 
            'ekstrakurikuler', 
            'ekstrakurikulerRombel'
        ])->whereNotNull('finalized_at');

        if ($user->role === 'instruktur') {
            $query->whereHas('ekstrakurikulerRombel', function($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        }

        // Apply filters
        if ($request->filled('rombel_id')) {
            $query->where('ekstrakurikuler_rombel_id', $request->rombel_id);
        }

        $scores = $query->latest()->paginate(15);

        // Fetch rombels for dropdown filter
        if ($user->role === 'instruktur') {
            $rombels = EkstrakurikulerRombel::where('user_id_instruktur', $user->id)
                ->orWhere('user_id_asisten', $user->id)->get();
        } else {
            $rombels = EkstrakurikulerRombel::all();
        }

        // Map report cards and certificates
        $reportCards = ReportCard::whereIn('student_score_id', $scores->pluck('id'))->get()->keyBy('student_score_id');
        $certificates = Certificate::whereIn('siswa_id', $scores->pluck('siswa_id'))->get()->groupBy('siswa_id');

        return view('certificates.index', compact('scores', 'rombels', 'reportCards', 'certificates'));
    }

    /**
     * Download the certificate PDF.
     */
    public function download(Certificate $certificate)
    {
        if ($certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            $fileName = basename($certificate->file_path);
            return Storage::disk('public')->download($certificate->file_path, $fileName);
        }

        return redirect()->back()->with('error', 'Berkas sertifikat tidak ditemukan di server.');
    }

    /**
     * Public route to verify certificate authenticity.
     */
    public function verify($certificate_code)
    {
        $certificate = Certificate::where('certificate_code', $certificate_code)
            ->with(['siswa', 'ekstrakurikuler'])
            ->firstOrFail();

        // Get student's final score for matching program
        $score = StudentScore::where('siswa_id', $certificate->siswa_id)
            ->where('ekstrakurikuler_id', $certificate->ekstrakurikuler_id)
            ->first();

        return view('certificates.verify', compact('certificate', 'score'));
    }
}
