<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerSession;
use App\Models\LateReportRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LateReportRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new late report request from instructor.
     */
    public function store(Request $request, EkstrakurikulerSession $session)
    {
        $user = Auth::user();
        
        if ($user->role !== 'instruktur') {
            return back()->with('error', 'Hanya instruktur yang dapat mengajukan permohonan.');
        }

        // Cek kuota bulanan
        if ($user->monthly_late_report_quota <= 0) {
            return back()->with('error', 'Kuota permohonan bulanan Anda sudah habis (Max 3).');
        }

        // Cek apakah sudah ada request pending untuk sesi ini
        $existing = LateReportRequest::where('user_id', $user->id)
            ->where('session_id', $session->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('info', 'Permohonan untuk sesi ini sudah diajukan atau sudah disetujui.');
        }

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        LateReportRequest::create([
            'user_id' => $user->id,
            'session_id' => $session->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permohonan berhasil dikirim. Silakan tunggu persetujuan Admin.');
    }

    /**
     * Admin Index - List all requests.
     */
    public function index()
    {
        $this->authorizeAdmin();

        $requests = LateReportRequest::with(['user', 'session.rombel.ekstrakurikuler.sekolah'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(20);

        return view('admin.late-reports.index', compact('requests'));
    }

    /**
     * Admin Approve.
     */
    public function approve(LateReportRequest $lateReportRequest)
    {
        $this->authorizeAdmin();

        $lateReportRequest->update([
            'status' => 'approved',
            'admin_id' => Auth::id(),
            'admin_notes' => 'Disetujui oleh sistem.',
        ]);

        return back()->with('success', 'Permohonan disetujui. Instruktur sekarang bisa mengisi laporan.');
    }

    /**
     * Admin Reject.
     */
    public function reject(Request $request, LateReportRequest $lateReportRequest)
    {
        $this->authorizeAdmin();

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $lateReportRequest->update([
            'status' => 'rejected',
            'admin_id' => Auth::id(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Permohonan ditolak.');
    }

    private function authorizeAdmin()
    {
        if (!in_array(Auth::user()->role, ['admin', 'admin_sistem', 'webmaster'])) {
            abort(403);
        }
    }
}
