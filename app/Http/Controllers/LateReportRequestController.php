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

        if ($session->user_id_instruktur !== $user->id && $session->user_id_asisten !== $user->id) {
            return back()->with('error', 'Akses ditolak. Anda bukan instruktur atau asisten untuk sesi ini.');
        }

        // Cek kuota bulanan
        if ($user->monthly_late_report_quota <= 0) {
            return back()->with('error', "Kuota permohonan bulanan Anda sudah habis (Max {$user->max_late_report_quota}).");
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
     * Store a new late report request for Ad-Hoc activity from instructor.
     */
    public function storeAdhoc(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['instruktur', 'webmaster', 'admin_sistem', 'admin'])) {
            return back()->with('error', 'Hanya instruktur yang dapat mengajukan permohonan.');
        }

        if ($user->monthly_late_report_quota <= 0) {
            return back()->with('error', "Kuota permohonan bulanan Anda sudah habis (Max {$user->max_late_report_quota}).");
        }

        $request->validate([
            'adhoc_date' => 'required|string',
            'reason' => 'required|string|min:5|max:500',
        ]);

        $rawDate = trim($request->adhoc_date);
        $adhocDate = null;

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) {
                $adhocDate = \Carbon\Carbon::createFromFormat('Y-m-d', $rawDate)->format('Y-m-d');
            } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $rawDate)) {
                $adhocDate = \Carbon\Carbon::createFromFormat('d/m/Y', $rawDate)->format('Y-m-d');
            } elseif (preg_match('/^\d{2}-\d{2}-\d{4}$/', $rawDate)) {
                $adhocDate = \Carbon\Carbon::createFromFormat('d-m-Y', $rawDate)->format('Y-m-d');
            } else {
                $adhocDate = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Format tanggal tidak valid. Silakan tentukan tanggal kegiatan.');
        }

        $existing = LateReportRequest::where('user_id', $user->id)
            ->whereNull('session_id')
            ->where('adhoc_date', $adhocDate)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return back()->with('info', 'Permohonan Ad-Hoc untuk tanggal ini sudah diajukan atau disetujui.');
        }

        LateReportRequest::create([
            'user_id' => $user->id,
            'session_id' => null,
            'adhoc_date' => $adhocDate,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permohonan akses Ad-Hoc tanggal ' . \Carbon\Carbon::parse($adhocDate)->format('d/m/Y') . ' berhasil dikirim. Silakan tunggu persetujuan Admin.');
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

        $admin = Auth::user();
        $lateReportRequest->update([
            'status' => 'approved',
            'admin_id' => $admin->id,
            'admin_notes' => 'Disetujui oleh Admin ' . $admin->nama_lengkap . '.',
        ]);

        $instructor = $lateReportRequest->user;

        // Log Activity
        \App\Models\ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'approve_adhoc_request',
            'description' => 'Admin ' . $admin->nama_lengkap . ' menyetujui (ACC) permohonan akses laporan Ad-Hoc/Susulan ID #' . $lateReportRequest->id . ' untuk instruktur ' . optional($instructor)->nama_lengkap,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Permohonan disetujui. Notifikasi permohonan Ad-Hoc kini tampil di Dashboard Instruktur ' . optional($instructor)->nama_lengkap . '.');
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

        $admin = Auth::user();
        $lateReportRequest->update([
            'status' => 'rejected',
            'admin_id' => $admin->id,
            'admin_notes' => $request->admin_notes,
        ]);

        $instructor = $lateReportRequest->user;

        // Log Activity
        \App\Models\ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'reject_adhoc_request',
            'description' => 'Admin ' . $admin->nama_lengkap . ' menolak permohonan akses laporan Ad-Hoc/Susulan ID #' . $lateReportRequest->id . ' untuk instruktur ' . optional($instructor)->nama_lengkap . ' (Alasan: ' . $request->admin_notes . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Permohonan akses Ad-Hoc berhasil ditolak.');
    }

    private function authorizeAdmin()
    {
        if (!in_array(Auth::user()->role, ['admin', 'admin_sistem', 'webmaster'])) {
            abort(403);
        }
    }
}
