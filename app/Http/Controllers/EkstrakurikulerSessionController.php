<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use App\Models\EkstrakurikulerSession;
use App\Models\EkstrakurikulerRombel;
use App\Models\User;
use App\Services\SchedulingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Controller untuk mengelola sessions ekstrakurikuler dengan
 * fitur intelligent scheduling dan bulk management.
 */
class EkstrakurikulerSessionController extends Controller
{
    protected SchedulingService $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    /**
     * Tampilkan daftar sessions dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $query = EkstrakurikulerSession::with(['rombel.ekstrakurikuler', 'instruktur', 'asisten']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_terjadwal', [
                $request->tanggal_dari,
                $request->tanggal_sampai
            ]);
        }

        // Filter berdasarkan instructor
        if ($request->filled('instruktur')) {
            $query->where('user_id_instruktur', $request->instruktur);
        }

        // Filter berdasarkan rombel
        if ($request->filled('rombel')) {
            $query->where('ekstrakurikuler_rombel_id', $request->rombel);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('topik_materi', 'like', "%{$search}%")
                  ->orWhere('deskripsi_kegiatan', 'like', "%{$search}%")
                  ->orWhereHas('rombel.ekstrakurikuler', function($subQ) use ($search) {
                      $subQ->where('nama_program', 'like', "%{$search}%");
                  });
            });
        }

        $sessions = $query->orderBy('tanggal_terjadwal', 'desc')
                         ->orderBy('jam_mulai_terjadwal')
                         ->paginate(20);

        // Data untuk filter dropdown
        $instructors = User::where('is_active', true)
                          ->whereIn('role', ['admin', 'instruktur'])
                          ->select('id', 'name')
                          ->get();

        $rombels = EkstrakurikulerRombel::with('ekstrakurikuler')
                                      ->where('status', '!=', 'dibatalkan')
                                      ->get();

        return view('ekstrakurikuler.sessions.index', compact(
            'sessions', 'instructors', 'rombels'
        ));
    }

    /**
     * Tampilkan kalender view sessions.
     */
    public function calendar(Request $request): View
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $startDate = Carbon::create($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $sessions = EkstrakurikulerSession::with(['rombel.ekstrakurikuler', 'instruktur'])
            ->whereBetween('tanggal_terjadwal', [$startDate, $endDate])
            ->get()
            ->groupBy(function($session) {
                return $session->tanggal_terjadwal->format('Y-m-d');
            });

        return view('ekstrakurikuler.sessions.calendar', compact(
            'sessions', 'month', 'year', 'startDate', 'endDate'
        ));
    }

    /**
     * Tampilkan detail session.
     */
    public function show(EkstrakurikulerSession $session): View
    {
        $session->load(['rombel.ekstrakurikuler.sekolah', 'instruktur', 'asisten', 'laporanMengajar']);

        return view('ekstrakurikuler.sessions.show', compact('session'));
    }

    /**
     * Tampilkan form edit session.
     */
    public function edit(EkstrakurikulerSession $session): View
    {
        $session->load(['rombel.ekstrakurikuler']);

        $instructors = User::where('is_active', true)
                          ->whereIn('role', ['admin', 'instruktur'])
                          ->select('id', 'name')
                          ->get();

        return view('ekstrakurikuler.sessions.edit', compact('session', 'instructors'));
    }

    /**
     * Update session data.
     */
    public function update(Request $request, EkstrakurikulerSession $session): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'tanggal_terjadwal' => 'required|date',
            'jam_mulai_terjadwal' => 'required|date_format:H:i',
            'jam_selesai_terjadwal' => 'required|date_format:H:i|after:jam_mulai_terjadwal',
            'user_id_instruktur' => 'nullable|exists:users,id',
            'user_id_asisten' => 'nullable|exists:users,id',
            'topik_materi' => 'nullable|string|max:255',
            'deskripsi_kegiatan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Cek apakah session dapat diupdate
        if (!in_array($session->status, [
            EkstrakurikulerSession::STATUS_TERJADWAL,
            EkstrakurikulerSession::STATUS_DITUNDA
        ])) {
            return redirect()->back()
                           ->withErrors(['status' => 'Session ini tidak dapat diupdate karena sudah ' . $session->status_label]);
        }

        // Cek conflict jika ada perubahan instructor atau waktu
        if ($request->filled('user_id_instruktur')) {
            $instructor = User::find($request->user_id_instruktur);
            $assistant = $request->filled('user_id_asisten') ? User::find($request->user_id_asisten) : null;
            
            // Temporary update session untuk check conflict
            $tempSession = clone $session;
            $tempSession->fill($request->only([
                'tanggal_terjadwal',
                'jam_mulai_terjadwal', 
                'jam_selesai_terjadwal'
            ]));

            $conflicts = $this->schedulingService->checkInstructorConflicts($instructor, $tempSession, $assistant);
            
            if (!empty($conflicts)) {
                return redirect()->back()
                               ->withErrors(['conflict' => 'Conflict detected: ' . implode(', ', $conflicts)]);
            }
        }

        $session->update($request->only([
            'tanggal_terjadwal',
            'jam_mulai_terjadwal',
            'jam_selesai_terjadwal',
            'user_id_instruktur',
            'user_id_asisten',
            'topik_materi',
            'deskripsi_kegiatan',
            'catatan'
        ]));

        return redirect()->route('ekstrakurikuler.sessions.show', $session)
                        ->with('success', 'Session berhasil diupdate');
    }

    /**
     * Mulai session (change status to berlangsung).
     */
    public function start(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        if (!$session->canStart()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat dimulai saat ini'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'topik_materi' => 'nullable|string|max:255',
            'deskripsi_kegiatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $started = $session->start($request->only(['topik_materi', 'deskripsi_kegiatan']));

        return response()->json([
            'success' => $started,
            'message' => $started ? 'Session berhasil dimulai' : 'Gagal memulai session',
            'session' => $started ? $session->fresh() : null
        ]);
    }

    /**
     * Selesaikan session (change status to selesai).
     */
    public function complete(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        if (!$session->canComplete()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat diselesaikan saat ini'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'catatan' => 'nullable|string',
            'deskripsi_kegiatan' => 'nullable|string',
            'auto_create_laporan' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $completed = $session->complete($request->all());

        return response()->json([
            'success' => $completed,
            'message' => $completed ? 'Session berhasil diselesaikan' : 'Gagal menyelesaikan session',
            'session' => $completed ? $session->fresh() : null
        ]);
    }

    /**
     * Batalkan session.
     */
    public function cancel(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        if (!$session->canCancel()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat dibatalkan saat ini'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'alasan_pembatalan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Alasan pembatalan harus diisi',
                'errors' => $validator->errors()
            ], 422);
        }

        $cancelled = $session->cancel($request->alasan_pembatalan);

        return response()->json([
            'success' => $cancelled,
            'message' => $cancelled ? 'Session berhasil dibatalkan' : 'Gagal membatalkan session',
            'session' => $cancelled ? $session->fresh() : null
        ]);
    }

    /**
     * Reschedule session ke tanggal lain.
     */
    public function reschedule(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        if (!$session->canReschedule()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat direschedule saat ini'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'tanggal_pengganti' => 'required|date|after:today',
            'alasan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $newDate = Carbon::parse($request->tanggal_pengganti);
        $rescheduled = $session->reschedule($newDate, $request->alasan);

        return response()->json([
            'success' => $rescheduled,
            'message' => $rescheduled ? 'Session berhasil direschedule' : 'Gagal reschedule session',
            'session' => $rescheduled ? $session->fresh() : null
        ]);
    }

    /**
     * Bulk operations untuk multiple sessions.
     */
    public function bulk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:assign_instructor,reschedule,cancel,update_time',
            'session_ids' => 'required|array|min:1',
            'session_ids.*' => 'exists:ekstrakurikuler_session,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        $sessions = EkstrakurikulerSession::whereIn('id', $request->session_ids)->get();
        
        try {
            DB::beginTransaction();
            
            $result = match($request->action) {
                'assign_instructor' => $this->bulkAssignInstructor($request, $sessions),
                'reschedule' => $this->bulkReschedule($request, $sessions),
                'cancel' => $this->bulkCancel($request, $sessions),
                'update_time' => $this->bulkUpdateTime($request, $sessions),
                default => ['success' => false, 'message' => 'Action tidak valid']
            };
            
            if ($result['success']) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate ulang sessions untuk rombel.
     */
    public function regenerateRombelSessions(Request $request, EkstrakurikulerRombel $rombel): JsonResponse
    {
        try {
            DB::beginTransaction();

            $options = [
                'replace_existing' => $request->boolean('replace_existing', false),
                'skip_holidays' => $request->boolean('skip_holidays', true),
            ];

            $sessions = $this->schedulingService->generateSessionsForRombel($rombel, $options);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Berhasil generate {$sessions->count()} sessions untuk rombel {$rombel->nama_rombel}",
                'sessions_count' => $sessions->count()
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate sessions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Dapatkan available slots untuk instructor.
     */
    public function availableSlots(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'instructor_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'duration' => 'integer|min:30|max:480', // durasi dalam menit
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $instructor = User::find($request->instructor_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $duration = $request->get('duration', 120); // default 2 jam

        $slots = $this->schedulingService->findAvailableSlots($instructor, $startDate, $endDate, $duration);

        return response()->json([
            'success' => true,
            'slots' => $slots
        ]);
    }

    /**
     * Generate laporan scheduling untuk rombel.
     */
    public function schedulingReport(EkstrakurikulerRombel $rombel): JsonResponse
    {
        $report = $this->schedulingService->generateSchedulingReport($rombel);

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    /**
     * Helper method untuk bulk assign instructor.
     */
    protected function bulkAssignInstructor(Request $request, $sessions): array
    {
        $validator = Validator::make($request->all(), [
            'user_id_instruktur' => 'required|exists:users,id',
            'user_id_asisten' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => 'Data instructor tidak valid'];
        }

        $instructor = User::find($request->user_id_instruktur);
        $assistant = $request->filled('user_id_asisten') ? User::find($request->user_id_asisten) : null;

        $result = $this->schedulingService->assignInstructorToSessions($sessions, $instructor, $assistant);

        return [
            'success' => $result['failed'] === 0,
            'message' => "Berhasil assign {$result['success']} sessions, gagal {$result['failed']} sessions",
            'details' => $result
        ];
    }

    /**
     * Helper method untuk bulk reschedule.
     */
    protected function bulkReschedule(Request $request, $sessions): array
    {
        $validator = Validator::make($request->all(), [
            'new_schedule.tanggal_terjadwal' => 'required|date',
            'new_schedule.jam_mulai' => 'nullable|date_format:H:i',
            'new_schedule.jam_selesai' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => 'Data jadwal baru tidak valid'];
        }

        $result = $this->schedulingService->rescheduleSessions($sessions, $request->new_schedule);

        return [
            'success' => $result['failed'] === 0,
            'message' => "Berhasil reschedule {$result['success']} sessions, gagal {$result['failed']} sessions",
            'details' => $result
        ];
    }

    /**
     * Helper method untuk bulk cancel.
     */
    protected function bulkCancel(Request $request, $sessions): array
    {
        $validator = Validator::make($request->all(), [
            'alasan_pembatalan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => 'Alasan pembatalan harus diisi'];
        }

        $success = 0;
        $failed = 0;

        foreach ($sessions as $session) {
            if ($session->cancel($request->alasan_pembatalan)) {
                $success++;
            } else {
                $failed++;
            }
        }

        return [
            'success' => $failed === 0,
            'message' => "Berhasil cancel {$success} sessions, gagal {$failed} sessions"
        ];
    }

    /**
     * Helper method untuk bulk update time.
     */
    protected function bulkUpdateTime(Request $request, $sessions): array
    {
        $validator = Validator::make($request->all(), [
            'jam_mulai_terjadwal' => 'nullable|date_format:H:i',
            'jam_selesai_terjadwal' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return ['success' => false, 'message' => 'Format waktu tidak valid'];
        }

        $updateData = array_filter($request->only(['jam_mulai_terjadwal', 'jam_selesai_terjadwal']));
        
        if (empty($updateData)) {
            return ['success' => false, 'message' => 'Tidak ada data waktu yang akan diupdate'];
        }

        $result = $this->schedulingService->bulkUpdateSessions($sessions, $updateData);

        return [
            'success' => $result,
            'message' => $result ? 'Berhasil update waktu semua sessions' : 'Gagal update beberapa sessions'
        ];
    }
}