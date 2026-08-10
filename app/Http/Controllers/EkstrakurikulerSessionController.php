<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\RombelInstructorHistory;
use App\Models\User;
use App\Services\CalendarService;
use App\Services\SchedulingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Controller untuk mengelola sessions ekstrakurikuler dengan
 * fitur intelligent scheduling dan bulk management.
 */
class EkstrakurikulerSessionController extends Controller
{
    protected SchedulingService $schedulingService;
    protected CalendarService $calendarService;

    public function __construct(SchedulingService $schedulingService, CalendarService $calendarService)
    {
        $this->schedulingService = $schedulingService;
        $this->calendarService   = $calendarService;
    }

    /**
     * Tampilkan daftar sessions dengan filter dan pencarian.
     */
    public function index(Request $request): View|RedirectResponse
    {
        // Handling Reset Filter
        if ($request->has('reset_filter')) {
            session()->forget('ekstrakurikuler_sessions_filters');
            return redirect()->route('ekstrakurikuler.sessions.index');
        }

        $filterKeys = ['status', 'instruktur', 'tanggal_dari', 'tanggal_sampai', 'rombel', 'search', 'sort', 'filter_no_instructor', 'page'];

        // If request query is completely empty but we have saved filters in session, restore them automatically
        if (empty($request->query()) && session()->has('ekstrakurikuler_sessions_filters')) {
            $savedFilters = session('ekstrakurikuler_sessions_filters');
            if (!empty($savedFilters)) {
                return redirect()->route('ekstrakurikuler.sessions.index', $savedFilters);
            }
        }

        // Save current active filters to session if any filter query is set
        $currentFilters = array_filter($request->only($filterKeys), fn($val) => !is_null($val) && $val !== '');
        if (!empty($currentFilters)) {
            session(['ekstrakurikuler_sessions_filters' => $currentFilters]);
        }

        $query = EkstrakurikulerSession::with(['ekstrakurikuler.sekolah', 'rombel.ekstrakurikuler.sekolah', 'rombel.ekstrakurikuler.sales', 'instruktur', 'asisten', 'laporanMengajar']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Restrict to own sessions if not admin
        $user = auth()->user();
        if (! $user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal_terjadwal', [
                $request->tanggal_dari,
                $request->tanggal_sampai,
            ]);
        }

        // Filter berdasarkan instructor (hanya untuk admin/webmaster)
        if ($user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            if ($request->filled('instruktur') && $request->instruktur !== 'none') {
                $query->where('user_id_instruktur', $request->instruktur);
            }

            // Filter missing instructor (from Dashboard or dropdown option)
            if ($request->filled('filter_no_instructor') || $request->instruktur === 'none') {
                $query->whereNull('user_id_instruktur');
            }
        }

        // Filter berdasarkan rombel
        if ($request->filled('rombel')) {
            $query->where('ekstrakurikuler_rombel_id', $request->rombel);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topik_materi', 'like', "%{$search}%")
                    ->orWhere('deskripsi_kegiatan', 'like', "%{$search}%")
                    ->orWhereHas('rombel.ekstrakurikuler', function ($subQ) use ($search) {
                        $subQ->where('kategori_program', 'like', "%{$search}%")
                             ->orWhereHas('sekolah', function ($schoolQ) use ($search) {
                                 $schoolQ->where('namasekolah', 'like', "%{$search}%");
                             });
                    });
            });
        }

        // Sorting: Sesi berstatus 'selesai' ditaruh di paling belakang, dan urutan tanggal dimulai dari hari ini
        $sort = $request->get('sort', 'date_asc'); // Default to date_asc (Jadwal Terdekat)
        $today = now()->format('Y-m-d');
        
        // Sesi berstatus selesai/completed selalu ditaruh di paling belakang
        $query->orderByRaw("CASE WHEN status IN ('selesai', 'completed') THEN 1 ELSE 0 END ASC");

        switch ($sort) {
            case 'date_asc':
                $query->orderByRaw("CASE WHEN tanggal_terjadwal >= '{$today}' THEN 0 ELSE 1 END ASC")
                      ->orderByRaw("CASE WHEN tanggal_terjadwal >= '{$today}' THEN tanggal_terjadwal END ASC")
                      ->orderByRaw("CASE WHEN tanggal_terjadwal < '{$today}' THEN tanggal_terjadwal END DESC")
                      ->orderBy('jam_mulai_terjadwal', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('tanggal_terjadwal', 'desc')
                      ->orderBy('jam_mulai_terjadwal', 'desc');
                break;
            case 'meeting_asc':
                $query->orderBy('nomor_pertemuan', 'asc');
                break;
            case 'meeting_desc':
                $query->orderBy('nomor_pertemuan', 'desc');
                break;
            default:
                $query->orderByRaw("CASE WHEN tanggal_terjadwal >= '{$today}' THEN 0 ELSE 1 END ASC")
                      ->orderByRaw("CASE WHEN tanggal_terjadwal >= '{$today}' THEN tanggal_terjadwal END ASC")
                      ->orderByRaw("CASE WHEN tanggal_terjadwal < '{$today}' THEN tanggal_terjadwal END DESC")
                      ->orderBy('jam_mulai_terjadwal', 'asc');
                break;
        }

        $sessions = $query->paginate(20)->withQueryString();

        // Data untuk filter dropdown (hanya untuk admin/webmaster)
        $instructors = collect();
        if ($user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            $instructors = User::teachingStaff()
                ->orderBy('nama_lengkap', 'asc')
                ->select('id', 'nama_lengkap')
                ->get();
        }

        $rombels = collect();

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
        $year  = $request->get('year', now()->year);

        $startDate = Carbon::create($year, $month, 1);
        $endDate   = $startDate->copy()->endOfMonth();

        $user = auth()->user();
        $query = EkstrakurikulerSession::with(['rombel.ekstrakurikuler', 'instruktur'])
            ->whereBetween('tanggal_terjadwal', [$startDate, $endDate]);

        // Restrict to own sessions if not admin
        if (! $user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        }

        $sessions = $query->get()
            ->groupBy(function ($session) {
                return $session->tanggal_terjadwal->format('Y-m-d');
            });

        // Data hari libur nasional untuk bulan ini
        $holidays = $this->calendarService
            ->getHolidaysInRange($startDate->toDateString(), $endDate->toDateString())
            ->keyBy(fn ($h) => \Carbon\Carbon::parse($h->tanggal)->toDateString());

        return view('ekstrakurikuler.sessions.calendar', compact(
            'sessions', 'month', 'year', 'startDate', 'endDate', 'holidays'
        ));
    }

    /**
     * Tampilkan detail session.
     */
    public function show(EkstrakurikulerSession $session): View
    {
        // Authorization: Admin can view all, instructor can only view assigned sessions
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            if ($session->user_id_instruktur !== $user->id && $session->user_id_asisten !== $user->id) {
                abort(403, 'Akses Ditolak: Anda bukan instruktur atau asisten untuk sesi ini.');
            }
        }

        $session->load(['rombel.ekstrakurikuler.sekolah', 'instruktur', 'asisten', 'laporanMengajar.absensi.siswa']);

        $previousReport = null;
        if ($session->ekstrakurikuler_rombel_id) {
            $previousReport = \App\Models\LaporanMengajar::whereIn('ekstrakurikuler_session_id', function ($query) use ($session) {
                $query->select('id')
                    ->from('ekstrakurikuler_session')
                    ->where('ekstrakurikuler_rombel_id', $session->ekstrakurikuler_rombel_id)
                    ->where('id', '!=', $session->id);
            })
            ->with(['instruktur:id,nama_lengkap'])
            ->latest('jadwal_mengajar')
            ->latest('id')
            ->first();
        }

        return view('ekstrakurikuler.sessions.show', compact('session', 'previousReport'));
    }

    /**
     * Tampilkan form edit session.
     */
    public function edit(EkstrakurikulerSession $session): View
    {
        $this->authorize('update', $session);
        $session->load(['rombel.ekstrakurikuler']);

        $instructors = User::teachingStaff()
            ->orderBy('nama_lengkap', 'asc')
            ->select('id', 'nama_lengkap')
            ->get();

        // Ambil daftar materi berdasarkan kategori program
        $kategori = $session->rombel->ekstrakurikuler->kategori_program;
        $materiList = \App\Models\RefMateri::where('kategori', $kategori)
            ->orderByRaw("CASE WHEN materi = 'Lain - Lain' THEN 1 ELSE 0 END")
            ->orderBy('materi', 'asc')
            ->pluck('materi');

        return view('ekstrakurikuler.sessions.edit', compact('session', 'instructors', 'materiList'));
    }

    /**
     * Update session data.
     */
    public function update(Request $request, EkstrakurikulerSession $session): RedirectResponse
    {
        $this->authorize('update', $session);
        
        $input = $request->all();
        if (!empty($input['jam_mulai_terjadwal'])) {
            $input['jam_mulai_terjadwal'] = substr($input['jam_mulai_terjadwal'], 0, 5);
        }
        if (!empty($input['jam_selesai_terjadwal'])) {
            $input['jam_selesai_terjadwal'] = substr($input['jam_selesai_terjadwal'], 0, 5);
        }

        $validator = Validator::make($input, [
            'tanggal_terjadwal' => 'required|date',
            'jam_mulai_terjadwal' => 'required|date_format:H:i',
            'jam_selesai_terjadwal' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai_terjadwal',
                function ($attribute, $value, $fail) use ($input) {
                    // Validasi durasi mengajar per sesi (minimal 30 menit, maksimal 180 menit)
                    if (!empty($input['jam_mulai_terjadwal']) && $value) {
                        try {
                            $start = \Carbon\Carbon::createFromFormat('H:i', $input['jam_mulai_terjadwal']);
                            $end = \Carbon\Carbon::createFromFormat('H:i', $value);
                            if ($end < $start) $end->addDay();
                            $diff = $start->diffInMinutes($end);
                            if ($diff < 30) $fail('Durasi mengajar minimal 30 menit.');
                            if ($diff > 180) $fail('Durasi mengajar maksimal 180 menit (3 jam).');
                        } catch (\Throwable $e) {}
                    }
                }
            ],
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
        if (! in_array($session->status, [
            EkstrakurikulerSession::STATUS_TERJADWAL,
            EkstrakurikulerSession::STATUS_DITUNDA,
        ])) {
            return redirect()->back()
                ->withErrors(['status' => 'Session ini tidak dapat diupdate karena sudah '.$session->status_label]);
        }

        // Prepare data for update
        $data = $request->only([
            'tanggal_terjadwal',
            'jam_mulai_terjadwal',
            'jam_selesai_terjadwal',
            'topik_materi',
            'deskripsi_kegiatan',
            'catatan',
        ]);

        // Handle nullable foreign keys explicitly
        $data['user_id_instruktur'] = $request->input('user_id_instruktur') ?: null;
        $data['user_id_asisten'] = $request->input('user_id_asisten') ?: null;

        // Cek conflict jika ada perubahan instructor atau waktu
        if ($data['user_id_instruktur']) {
            $instructor = User::find($data['user_id_instruktur']);
            $assistant = $data['user_id_asisten'] ? User::find($data['user_id_asisten']) : null;

            // Temporary update session untuk check conflict
            $tempSession = clone $session;
            $tempSession->fill($data);

            $conflicts = $this->schedulingService->checkInstructorConflicts($instructor, $tempSession, $assistant);

            // Filter out conflict with the session itself (if needed, though scheduling service usually handles this)
            // Assuming checkInstructorConflicts handles excluding current session if logic allows, 
            // but strict check might flag it. 
            // For now we assume strict check is desired.

            if (! empty($conflicts)) {
                return redirect()->back()
                    ->withErrors(['conflict' => 'Conflict detected: '.implode(', ', $conflicts)])
                    ->withInput();
            }
        }

            // Check for SOFT conflicts (availability preference)
        $softWarnings = [];
        if ($data['user_id_instruktur']) {
            // Re-instantiate objects if not already done in hard check block
             if (!isset($instructor)) $instructor = User::find($data['user_id_instruktur']);
             if (!isset($tempSession)) {
                 $tempSession = clone $session;
                 $tempSession->fill($data);
             }
             
             $softWarnings = $this->schedulingService->checkInstructorSoftConflicts($instructor, $tempSession);
        }

        // Detect instructor change and record history (Level 2)
        $oldInstruktorId = $session->user_id_instruktur;
        $newInstruktorId = $data['user_id_instruktur'] ?? null;

        $session->update($data);

        // If instructor has changed, record into rombel_instructor_history
        if ($newInstruktorId && $oldInstruktorId !== $newInstruktorId) {
            $rombel = $session->rombel;
            if ($rombel) {
                RombelInstructorHistory::recordChange(
                    rombelId:         $rombel->id,
                    newInstruktorId:  $newInstruktorId,
                    newAsitenId:      $data['user_id_asisten'] ?? null,
                    fromSesi:         $session->nomor_pertemuan,
                    previousEndSesi:  max(1, $session->nomor_pertemuan - 1),
                    alasan:           $request->input('alasan_pergantian'),
                    digantiOleh:      Auth::id(),
                );
            }
        }

        $targetRoute = 'ekstrakurikuler.sessions.show';
        $routeParams = ['session' => $session->id];

        if ($request->input('redirect_to') === 'index' || $request->has('_return_query')) {
            $targetRoute = 'ekstrakurikuler.sessions.index';
            $routeParams = session('ekstrakurikuler_sessions_filters', []);
            if ($request->has('_return_query')) {
                $decoded = json_decode($request->input('_return_query'), true);
                if (is_array($decoded) && !empty($decoded)) {
                    $routeParams = array_merge($routeParams, $decoded);
                }
            }
        }

        $redirect = redirect()->route($targetRoute, $routeParams)
            ->with('success', 'Session berhasil diupdate');
            
        if (!empty($softWarnings)) {
            $redirect->with('warning', 'Session diupdate dengan Peringatan Ketersediaan: ' . implode(', ', $softWarnings));
        }

        return $redirect;
    }

    /**
     * AJAX endpoint untuk mengecek konflik jadwal session (Instruktur & Asisten) secara real-time.
     */
    public function checkConflict(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id_instruktur' => 'nullable|exists:users,id',
            'user_id_asisten' => 'nullable|exists:users,id',
            'tanggal_terjadwal' => 'required|date',
            'jam_mulai_terjadwal' => 'required',
            'jam_selesai_terjadwal' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $instrukturId = $request->input('user_id_instruktur') ?: null;
        $asistenId = $request->input('user_id_asisten') ?: null;

        $instructor = $instrukturId ? User::find($instrukturId) : null;
        $assistant = $asistenId ? User::find($asistenId) : null;

        if (!$instructor && !$assistant) {
            return response()->json([
                'success' => true,
                'has_conflict' => false,
                'messages' => [],
            ]);
        }

        $tempSession = clone $session;
        $tempSession->tanggal_terjadwal = $request->input('tanggal_terjadwal');
        $tempSession->jam_mulai_terjadwal = $request->input('jam_mulai_terjadwal');
        $tempSession->jam_selesai_terjadwal = $request->input('jam_selesai_terjadwal');
        if ($instrukturId) $tempSession->user_id_instruktur = $instrukturId;
        if ($asistenId) $tempSession->user_id_asisten = $asistenId;

        $conflicts = [];
        if ($instructor) {
            $conflicts = array_merge($conflicts, $this->schedulingService->checkInstructorConflicts($instructor, $tempSession, $assistant));
        }

        $details = [];
        if ($instrukturId) {
            $conflictingSessions = EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah'])
                ->where('user_id_instruktur', $instrukturId)
                ->where('id', '!=', $session->id)
                ->where('tanggal_terjadwal', $tempSession->tanggal_terjadwal)
                ->where('status', '!=', EkstrakurikulerSession::STATUS_DIBATALKAN)
                ->where(function ($q) use ($tempSession) {
                    $q->whereBetween('jam_mulai_terjadwal', [$tempSession->jam_mulai_terjadwal, $tempSession->jam_selesai_terjadwal])
                      ->orWhereBetween('jam_selesai_terjadwal', [$tempSession->jam_mulai_terjadwal, $tempSession->jam_selesai_terjadwal]);
                })
                ->get();

            foreach ($conflictingSessions as $cs) {
                $sekolahNama = $cs->rombel->ekstrakurikuler->sekolah->namasekolah ?? 'Sekolah';
                $rombelNama = $cs->rombel->nama_rombel ?? 'Rombel';
                $jamStart = \Carbon\Carbon::parse($cs->jam_mulai_terjadwal)->format('H:i');
                $jamEnd = \Carbon\Carbon::parse($cs->jam_selesai_terjadwal)->format('H:i');
                $details[] = "Instruktur bertabrakan dengan {$sekolahNama} ({$rombelNama}) jam {$jamStart} - {$jamEnd}";
            }
        }

        return response()->json([
            'success' => true,
            'has_conflict' => !empty($conflicts) || !empty($details),
            'messages' => !empty($details) ? $details : $conflicts,
        ]);
    }

    /**
     * Start session.
     */
    public function start(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        $this->authorize('start', $session);

        if (! $session->canStart()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat dimulai saat ini (Hanya bisa dimulai hari ini)',
            ], 400);
        }

        $started = $session->start($request->only(['topik_materi', 'deskripsi_kegiatan']));

        return response()->json([
            'success' => $started,
            'message' => $started ? 'Session berhasil dimulai' : 'Gagal memulai session',
            'session' => $started ? $session->fresh() : null,
        ]);
    }

    /**
     * Complete session.
     */
    public function complete(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        $this->authorize('complete', $session);

        if (! $session->canComplete()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat diselesaikan saat ini',
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
                'errors' => $validator->errors(),
            ], 422);
        }

        $completed = $session->complete($request->all());

        return response()->json([
            'success' => $completed,
            'message' => $completed ? 'Session berhasil diselesaikan' : 'Gagal menyelesaikan session',
            'session' => $completed ? $session->fresh() : null,
        ]);
    }

    /**
     * Batalkan session.
     */
    public function cancel(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Fitur pembatalan sesi dinonaktifkan. Silakan gunakan fitur Reschedule untuk menggeser jadwal.',
        ], 400);
    }

    /**
     * Reschedule session ke tanggal lain.
     */
    public function reschedule(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        $this->authorize('reschedule', $session);

        if (! $session->canReschedule()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat direschedule saat ini',
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
                'errors' => $validator->errors(),
            ], 422);
        }

        $newDate = Carbon::parse($request->tanggal_pengganti);
        $rescheduled = $session->reschedule($newDate, $request->alasan);

        return response()->json([
            'success' => $rescheduled,
            'message' => $rescheduled ? 'Session berhasil direschedule' : 'Gagal reschedule session',
            'session' => $rescheduled ? $session->fresh() : null,
        ]);
    }

    /**
     * Tunda session (menggantung).
     */
    public function postpone(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        $this->authorize('postpone', $session);

        if (! $session->canPostpone()) {
            return response()->json([
                'success' => false,
                'message' => 'Session tidak dapat ditunda saat ini',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'alasan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Alasan penundaan harus diisi',
                'errors' => $validator->errors(),
            ], 422);
        }

        $postponed = $session->postpone($request->alasan);

        return response()->json([
            'success' => $postponed,
            'message' => $postponed ? 'Session berhasil ditunda' : 'Gagal menunda session',
            'session' => $postponed ? $session->fresh() : null,
        ]);
    }

    /**
     * Bulk operations untuk multiple sessions.
     */
    public function bulk(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:assign_instructor,reschedule,update_time',
            'session_ids' => 'required|array|min:1',
            'session_ids.*' => 'exists:ekstrakurikuler_session,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $sessions = EkstrakurikulerSession::whereIn('id', $request->session_ids)->get();

        try {
            DB::beginTransaction();

            $result = match ($request->action) {
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
                'message' => 'Terjadi error: '.$e->getMessage(),
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
                'sessions_count' => $sessions->count(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate sessions: '.$e->getMessage(),
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
                'errors' => $validator->errors(),
            ], 422);
        }

        $instructor = User::find($request->instructor_id);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $duration = $request->get('duration', 120); // default 2 jam

        $slots = $this->schedulingService->findAvailableSlots($instructor, $startDate, $endDate, $duration);

        return response()->json([
            'success' => true,
            'slots' => $slots,
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
            'report' => $report,
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
            'details' => $result,
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
            'details' => $result,
        ];
    }

    /**
     * Helper method untuk bulk cancel.
     */
    protected function bulkCancel(Request $request, $sessions): array
    {
        return [
            'success' => false,
            'message' => 'Pembatalan massal dinonaktifkan. Silakan gunakan reschedule massal.',
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
            'message' => $result ? 'Berhasil update waktu semua sessions' : 'Gagal update beberapa sessions',
        ];
    }
    /**
     * Kirim reminder manual untuk session.
     */
    public function sendReminder(Request $request, EkstrakurikulerSession $session): JsonResponse
    {
        // Hanya admin/admin_sistem/webmaster yang boleh kirim reminder manual
        $this->authorize('update', $session); 

        // Validasi input
        $validator = Validator::make($request->all(), [
            'custom_message' => 'nullable|string|max:500',
            'target' => 'nullable|string|in:instructor,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesan tidak valid',
                'errors' => $validator->errors(),
            ], 422);
        }

        $target = $request->input('target', 'instructor');

        // Fitur Pengujian Khusus Admin ke Nomor +62 821-1830-2927
        if ($target === 'admin' || $request->boolean('test_admin')) {
            try {
                $adminPhone = '6282118302927';
                $prefixNote = "🧪 *[PESAN UJI COBA GATEWAY WA ADMIN (+62 821-1830-2927)]*\n";
                $customMsg = $prefixNote . ($request->custom_message ?? 'Tes koneksi Fonnte WhatsApp Gateway Erlass Institute.');

                $testNotification = new \App\Notifications\ScheduleReminderNotification($session, $customMsg);
                
                $notifiable = new class($adminPhone) {
                    use \Illuminate\Notifications\Notifiable;
                    public $phone;
                    public $id = 'admin_test';
                    public $nama_lengkap = 'Admin Testing (+6282118302927)';
                    public function __construct($phone) { $this->phone = $phone; }
                    public function routeNotificationForWhatsapp() { return $this->phone; }
                    public function routeNotificationFor($driver, $notification = null) { return $this->phone; }
                };

                $channel = new \App\Notifications\Channels\WhatsAppChannel();
                $channel->send($notifiable, $testNotification);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesan uji coba WhatsApp berhasil dikirim ke Nomor Admin (+62 821-1830-2927)',
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Manual Reminder Admin Test Error: " . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim pesan uji coba ke Admin: ' . $e->getMessage(),
                ], 500);
            }
        }

        $instructor = $session->instruktur;

        if (! $instructor) {
            return response()->json([
                'success' => false,
                'message' => 'Session ini belum memiliki instruktur Assigned',
            ], 400);
        }

        try {
            $instructor->notify(new \App\Notifications\ScheduleReminderNotification($session, $request->custom_message));
            
            return response()->json([
                'success' => true,
                'message' => 'Reminder berhasil dikirim ke ' . $instructor->nama_lengkap,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Manual Reminder Error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim reminder: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kirim reminder manual untuk progress report siswa.
     */
    public function sendProgressReminder(Request $request, EkstrakurikulerSession $session): RedirectResponse
    {
        // Hanya admin/admin_sistem/webmaster/instruktur yang ditugaskan yang boleh kirim
        $this->authorize('update', $session);

        $laporan = $session->laporanMengajar;
        $rombel = $session->rombel;

        if ($session->status !== EkstrakurikulerSession::STATUS_SELESAI || !$laporan) {
            return redirect()->back()->with('error', 'Reminder progress hanya bisa dikirim untuk sesi yang sudah selesai dan memiliki laporan mengajar.');
        }

        try {
            // Find students who were PRESENT in this specific session
            $presentStudentIds = \App\Models\Absensi::where('laporan_mengajar_id', $laporan->id)
                ->where('status', 'hadir')
                ->pluck('siswa_id');

            $studentsToNotify = \App\Models\Siswa::whereIn('id', $presentStudentIds)
                                     ->whereNotNull('no_hp_orangtua')
                                     ->get();

            $messagesSent = 0;

            foreach ($studentsToNotify as $student) {
                try {
                    $rombelReports = $rombel->sessions()
                        ->has('laporanMengajar')
                        ->with('laporanMengajar') 
                        ->get()
                        ->pluck('laporanMengajar')
                        ->filter();

                    $attendanceRecords = \App\Models\Absensi::whereIn('laporan_mengajar_id', $rombelReports->pluck('id'))
                        ->where('siswa_id', $student->id)
                        ->where('status', 'hadir')
                        ->get();

                    $totalPresent = $attendanceRecords->count();

                    // If they have attended at least 2 times for manual trigger
                    if ($totalPresent >= 2) {
                        // Get the last 4 reports of the rombel
                        $last4Reports = $rombelReports->sortByDesc('jadwal_mengajar')->take(4)->sortBy('jadwal_mengajar')->values();

                        $student->notify(new \App\Notifications\ProgressReminderNotification($student, $rombel, $last4Reports));
                        $messagesSent++;
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Manual ProgressReminder Error for student {$student->id}: " . $e->getMessage());
                }
            }
            
            if ($messagesSent > 0) {
                return redirect()->back()->with('success', "Berhasil mengirim {$messagesSent} pesan Progress Reminder ke WhatsApp orang tua siswa.");
            } else {
                return redirect()->back()->with('info', "Tidak ada pesan yang dikirim. Pastikan siswa tujuan memiliki No. HP dan sudah hadir minimal 4 kali.");
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Manual Batch Progress Reminder Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat mengirim pesan reminder.');
        }
    }

    /**
     * Override session fee (Admin Only).
     */
    public function overrideFee(Request $request, EkstrakurikulerSession $session): \Illuminate\Http\RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'override_fee' => 'nullable|numeric|min:0',
        ]);

        $session->update([
            'override_fee' => $request->input('override_fee') !== null ? (float) $request->input('override_fee') : null,
        ]);

        return redirect()->back()->with('success', 'Fee sesi berhasil dikoreksi (override)!');
    }

    /**
     * Tambah satu session secara manual ke rombel (Admin Only).
     */
    public function addManualSession(Request $request, EkstrakurikulerRombel $rombel): \Illuminate\Http\RedirectResponse
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'tanggal_terjadwal' => 'required|date',
            'jam_mulai_terjadwal' => 'required|string',
            'jam_selesai_terjadwal' => 'required|string',
            'topik_materi' => 'nullable|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Hitung nomor pertemuan berikutnya secara otomatis (max + 1)
            $nextMeetingNumber = $rombel->sessions()->max('nomor_pertemuan') + 1;

            $sessionData = [
                'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
                'ekstrakurikuler_rombel_id' => $rombel->id,
                'nomor_pertemuan' => $nextMeetingNumber,
                'tanggal_terjadwal' => $request->input('tanggal_terjadwal'),
                'jam_mulai_terjadwal' => $request->input('jam_mulai_terjadwal'),
                'jam_selesai_terjadwal' => $request->input('jam_selesai_terjadwal'),
                'user_id_instruktur' => $rombel->user_id_instruktur,
                'user_id_asisten' => $rombel->user_id_asisten,
                'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
                'topik_materi' => $request->input('topik_materi'),
                'catatan' => $request->input('catatan'),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];

            EkstrakurikulerSession::create($sessionData);

            DB::commit();

            return redirect()->back()->with('success', "Berhasil menambahkan Pertemuan {$nextMeetingNumber} secara manual.");

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Error adding manual session: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan sesi baru: ' . $e->getMessage());
        }
    }
}
