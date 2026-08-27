<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LateReportRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store late report request (Backward compatible fallback).
     */
    public function store(Request $request, \App\Models\EkstrakurikulerSession $session)
    {
        $user = Auth::user();
        if ($user->role !== 'instruktur') {
            return back()->with('error', 'Hanya instruktur yang dapat mengajukan permohonan.');
        }

        $isAssignedOnSession = ($session->user_id_instruktur === $user->id || $session->user_id_asisten === $user->id);
        $isAssignedOnRombel = ($session->rombel && ($session->rombel->user_id_instruktur === $user->id || $session->rombel->user_id_asisten === $user->id));

        if (!$isAssignedOnSession && !$isAssignedOnRombel) {
            return back()->with('error', 'Akses ditolak. Anda bukan instruktur atau asisten untuk sesi ini.');
        }

        $request->validate(['reason' => 'required|string|min:10|max:500']);

        \App\Models\LateReportRequest::create([
            'user_id' => $user->id,
            'session_id' => $session->id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permohonan berhasil dikirim.');
    }

    /**
     * Store adhoc late report request (Backward compatible fallback).
     */
    public function storeAdhoc(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['instruktur', 'webmaster', 'admin_sistem', 'admin'])) {
            return back()->with('error', 'Hanya instruktur yang dapat mengajukan permohonan.');
        }

        $request->validate([
            'adhoc_date' => 'required|string',
            'reason' => 'required|string|min:5|max:500',
        ]);

        \App\Models\LateReportRequest::create([
            'user_id' => $user->id,
            'session_id' => null,
            'adhoc_date' => \Carbon\Carbon::parse($request->adhoc_date)->format('Y-m-d'),
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Permohonan akses Ad-Hoc berhasil dikirim.');
    }

    /**
     * Admin Index - List all severe late reports needing delay audit.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $statusTab = $request->query('status', 'pending');
        $search = $request->query('search');

        // Query base: Laporan yang mengalami keterlambatan berat (>3 hari / lewat cutoff) atau memiliki alasan kendala
        $query = LaporanMengajar::with([
            'instruktur:id,nama_lengkap,email,instructor_id,no_telephone',
            'ekstrakurikulerSession.rombel.ekstrakurikuler.sekolah',
            'sekolah:kodlan,namasekolah'
        ])
        ->where(function ($q) {
            $q->where('metadata_json->is_severe_late', true)
              ->orWhereNotNull('metadata_json->alasan_kendala_keterlambatan');
        });

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('instruktur', function ($sq) use ($search) {
                    $sq->where('nama_lengkap', 'like', "%{$search}%");
                })
                ->orWhere('rombel', 'like', "%{$search}%")
                ->orWhere('materi_pengajaran', 'like', "%{$search}%")
                ->orWhere('metadata_json->alasan_kendala_keterlambatan', 'like', "%{$search}%");
            });
        }

        // Tab Filter
        if ($statusTab === 'pending') {
            $query->where(function ($q) {
                $q->where('metadata_json->status_approval_kendala', 'pending_approval')
                  ->orWhereNull('metadata_json->status_approval_kendala');
            });
        } elseif ($statusTab === 'approved') {
            $query->where('metadata_json->status_approval_kendala', 'approved');
        } elseif ($statusTab === 'rejected') {
            $query->where('metadata_json->status_approval_kendala', 'rejected');
        }

        // Stats counts for header badges
        $baseCountQuery = LaporanMengajar::where(function ($q) {
            $q->where('metadata_json->is_severe_late', true)
              ->orWhereNotNull('metadata_json->alasan_kendala_keterlambatan');
        });

        $totalCount = (clone $baseCountQuery)->count();
        $pendingCount = (clone $baseCountQuery)->where(function ($q) {
            $q->where('metadata_json->status_approval_kendala', 'pending_approval')
              ->orWhereNull('metadata_json->status_approval_kendala');
        })->count();
        $approvedCount = (clone $baseCountQuery)->where('metadata_json->status_approval_kendala', 'approved')->count();
        $rejectedCount = (clone $baseCountQuery)->where('metadata_json->status_approval_kendala', 'rejected')->count();

        $reports = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.late-reports.index', compact(
            'reports',
            'statusTab',
            'search',
            'totalCount',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    /**
     * Admin Approve - Setujui Kendala Keterlambatan.
     */
    public function approve(Request $request, LaporanMengajar $lateReportRequest)
    {
        $this->authorizeAdmin();

        $admin = Auth::user();
        $meta = $lateReportRequest->metadata_json ?? [];
        if (!is_array($meta)) {
            $meta = [];
        }

        $meta['status_approval_kendala'] = 'approved';
        $meta['approved_by'] = $admin->id;
        $meta['approved_at'] = now()->toDateTimeString();
        $meta['admin_notes'] = $request->input('admin_notes', 'Disetujui oleh Admin ' . $admin->nama_lengkap . '.');

        $lateReportRequest->update(['metadata_json' => $meta]);

        $instructor = $lateReportRequest->instruktur;

        // Log Activity
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'approve_late_report_kendala',
            'description' => 'Admin ' . $admin->nama_lengkap . ' menyetujui (ACC) kendala keterlambatan Laporan ID #' . $lateReportRequest->id . ' (Pertemuan ' . $lateReportRequest->pertemuan_ke . ') untuk instruktur ' . optional($instructor)->nama_lengkap,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'Kendala keterlambatan sesi Pertemuan ' . $lateReportRequest->pertemuan_ke . ' (' . optional($instructor)->nama_lengkap . ') berhasil disetujui (ACC). Honor sesi akan dicairkan pada periode cutoff.');
    }

    /**
     * Admin Reject - Tolak Kendala Keterlambatan.
     */
    public function reject(Request $request, LaporanMengajar $lateReportRequest)
    {
        $this->authorizeAdmin();

        $request->validate([
            'admin_notes' => 'required|string|min:5|max:500',
        ], [
            'admin_notes.required' => 'Wajib memberikan catatan/alasan penolakan kendala.',
            'admin_notes.min' => 'Catatan penolakan minimal 5 karakter.',
        ]);

        $admin = Auth::user();
        $meta = $lateReportRequest->metadata_json ?? [];
        if (!is_array($meta)) {
            $meta = [];
        }

        $meta['status_approval_kendala'] = 'rejected';
        $meta['approved_by'] = $admin->id;
        $meta['approved_at'] = now()->toDateTimeString();
        $meta['admin_notes'] = $request->admin_notes;

        $lateReportRequest->update(['metadata_json' => $meta]);

        $instructor = $lateReportRequest->instruktur;

        // Log Activity
        ActivityLog::create([
            'user_id' => $admin->id,
            'action' => 'reject_late_report_kendala',
            'description' => 'Admin ' . $admin->nama_lengkap . ' menolak kendala keterlambatan Laporan ID #' . $lateReportRequest->id . ' (Pertemuan ' . $lateReportRequest->pertemuan_ke . ') untuk instruktur ' . optional($instructor)->nama_lengkap . ' (Alasan: ' . $request->admin_notes . ')',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('warning', 'Kendala keterlambatan sesi Pertemuan ' . $lateReportRequest->pertemuan_ke . ' ditolak. Sesi ditahan dari pencairan honor.');
    }

    private function authorizeAdmin()
    {
        if (!in_array(Auth::user()->role, ['admin', 'admin_sistem', 'webmaster'])) {
            abort(403, 'Akses ditolak.');
        }
    }
}
