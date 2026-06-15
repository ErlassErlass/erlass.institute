<?php

namespace App\Http\Controllers;

use App\Models\ScheduleChange;
use App\Models\EkstrakurikulerSession;
use App\Models\SchoolPic;
use Illuminate\Http\Request;
use Exception;

class ScheduleChangeController extends Controller
{
    /**
     * Display a listing of schedule change requests.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = ScheduleChange::with([
            'session.rombel',
            'session.ekstrakurikuler.sekolah',
            'requester',
            'academicApprover',
            'schoolPicApprover',
        ])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        // Instruktur hanya melihat pengajuannya sendiri
        if (auth()->user()->role === 'instruktur') {
            $query->where('requested_by', auth()->id());
        }

        $scheduleChanges = $query->paginate(25);

        return view('schedule_changes.index', compact('scheduleChanges'));
    }

    /**
     * Show the form for creating a new schedule change request.
     */
    public function create(EkstrakurikulerSession $session)
    {
        // Pastikan session masih bisa di-reschedule
        if (!$session->canReschedule()) {
            return redirect()->back()
                ->with('error', 'Sesi ini tidak dapat diubah jadwalnya (status: ' . $session->status_label . ').');
        }

        $session->load('rombel', 'ekstrakurikuler.sekolah');

        return view('schedule_changes.create', compact('session'));
    }

    /**
     * Store a newly created schedule change request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ekstrakurikuler_session_id' => 'required|exists:ekstrakurikuler_session,id',
            'proposed_date' => 'required|date|after_or_equal:today',
            'proposed_start_time' => 'required|date_format:H:i',
            'proposed_end_time' => 'required|date_format:H:i|after:proposed_start_time',
            'reason' => 'required|string|max:1000',
        ]);

        $session = EkstrakurikulerSession::findOrFail($validated['ekstrakurikuler_session_id']);

        if (!$session->canReschedule()) {
            return redirect()->back()
                ->with('error', 'Sesi ini tidak dapat diubah jadwalnya.');
        }

        try {
            ScheduleChange::create([
                'ekstrakurikuler_session_id' => $session->id,
                'requested_by' => auth()->id(),
                'original_date' => $session->tanggal_terjadwal,
                'original_start_time' => $session->jam_mulai_terjadwal->format('H:i'),
                'original_end_time' => $session->jam_selesai_terjadwal->format('H:i'),
                'proposed_date' => $validated['proposed_date'],
                'proposed_start_time' => $validated['proposed_start_time'],
                'proposed_end_time' => $validated['proposed_end_time'],
                'reason' => $validated['reason'],
                'status' => 'pending',
            ]);

            return redirect()->route('schedule-changes.index')
                ->with('success', 'Pengajuan perubahan jadwal berhasil diajukan!');
        } catch (Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified schedule change.
     */
    public function show(ScheduleChange $scheduleChange)
    {
        $scheduleChange->load([
            'session.rombel',
            'session.ekstrakurikuler.sekolah',
            'requester',
            'academicApprover',
            'schoolPicApprover',
        ]);

        return view('schedule_changes.show', compact('scheduleChange'));
    }

    /**
     * Approve schedule change (academic approval from Erlass admin).
     */
    public function approveAcademic(ScheduleChange $scheduleChange)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Hanya admin Erlass yang bisa validasi akademik.');
        }

        if ($scheduleChange->status !== 'pending') {
            return redirect()->route('schedule-changes.show', $scheduleChange)
                ->with('error', 'Pengajuan ini bukan berstatus pending.');
        }

        $scheduleChange->update([
            'academic_approver_id' => auth()->id(),
            'academic_approved_at' => now(),
            'status' => 'approved_academic',
        ]);

        return redirect()->route('schedule-changes.show', $scheduleChange)
            ->with('success', 'Validasi akademik berhasil. Menunggu konfirmasi PIC sekolah.');
    }

    /**
     * Approve schedule change (school PIC confirmation).
     */
    public function approvePic(Request $request, ScheduleChange $scheduleChange)
    {
        if ($scheduleChange->status !== 'approved_academic') {
            return redirect()->route('schedule-changes.show', $scheduleChange)
                ->with('error', 'Pengajuan harus divalidasi akademik terlebih dahulu.');
        }

        $request->validate([
            'school_pic_id' => 'required|exists:school_pics,id',
        ]);

        $scheduleChange->update([
            'school_pic_approver_id' => $request->school_pic_id,
            'school_pic_approved_at' => now(),
            'status' => 'approved_pic',
        ]);

        return redirect()->route('schedule-changes.show', $scheduleChange)
            ->with('success', 'PIC Sekolah telah mengkonfirmasi perubahan jadwal.');
    }

    /**
     * Apply the approved schedule change to the actual session.
     */
    public function apply(ScheduleChange $scheduleChange)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Hanya admin yang bisa menerapkan perubahan jadwal.');
        }

        if ($scheduleChange->status !== 'approved_pic') {
            return redirect()->route('schedule-changes.show', $scheduleChange)
                ->with('error', 'Perubahan jadwal belum dikonfirmasi oleh PIC sekolah.');
        }

        try {
            $session = $scheduleChange->session;
            $session->update([
                'tanggal_terjadwal' => $scheduleChange->proposed_date,
                'jam_mulai_terjadwal' => $scheduleChange->proposed_start_time,
                'jam_selesai_terjadwal' => $scheduleChange->proposed_end_time,
            ]);

            $scheduleChange->update([
                'status' => 'applied',
            ]);

            return redirect()->route('schedule-changes.show', $scheduleChange)
                ->with('success', 'Perubahan jadwal berhasil diterapkan ke sesi!');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Reject a schedule change request.
     */
    public function reject(Request $request, ScheduleChange $scheduleChange)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Hanya admin yang bisa menolak pengajuan.');
        }

        if (in_array($scheduleChange->status, ['applied', 'rejected'])) {
            return redirect()->route('schedule-changes.show', $scheduleChange)
                ->with('error', 'Pengajuan ini sudah final (status: ' . $scheduleChange->status . ').');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $scheduleChange->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('schedule-changes.show', $scheduleChange)
            ->with('success', 'Pengajuan perubahan jadwal ditolak.');
    }
}
