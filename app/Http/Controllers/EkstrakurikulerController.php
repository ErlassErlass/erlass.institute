<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEkstrakurikulerRequest;
use App\Http\Requests\Ekstrakurikuler\CreateEkstrakurikulerStep1Request;
use App\Http\Requests\Ekstrakurikuler\CreateEkstrakurikulerStep2Request;
use App\Http\Requests\Ekstrakurikuler\CreateEkstrakurikulerRombelRequest;
use App\Models\Ekstrakurikuler;
use App\Services\Ekstrakurikuler\EkstrakurikulerQueryService;
use App\Services\Ekstrakurikuler\EkstrakurikulerFormService;
use App\Services\Ekstrakurikuler\EkstrakurikulerWorkflowService;
use App\Services\Ekstrakurikuler\RegionMappingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * EkstrakurikulerController
 * Controller ini sudah dipecah menggunakan services yang terpisah untuk:
 * - Query & Filtering (EkstrakurikulerQueryService)
 * - Multi-step Form Logic (EkstrakurikulerFormService)  
 * - Business Logic & Workflow (EkstrakurikulerWorkflowService)
 * - Region Mapping (RegionMappingService)
 * 
 * API endpoints dipindahkan ke EkstrakurikulerApiController
 */
class EkstrakurikulerController extends Controller
{
    protected EkstrakurikulerQueryService $queryService;
    protected EkstrakurikulerFormService $formService;
    protected EkstrakurikulerWorkflowService $workflowService;
    protected RegionMappingService $regionService;

    public function __construct(
        EkstrakurikulerQueryService $queryService,
        EkstrakurikulerFormService $formService,
        EkstrakurikulerWorkflowService $workflowService,
        RegionMappingService $regionService
    ) {
        $this->queryService = $queryService;
        $this->formService = $formService;
        $this->workflowService = $workflowService;
        $this->regionService = $regionService;
        
        $this->authorizeResource(Ekstrakurikuler::class, 'ekstrakurikuler');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Normalisasi & Validasikan keselarasan filter silang (Region, Kota, Sekolah)
        if ($request->filled('region')) {
            $citiesInRegion = $this->regionService->getCitiesByRegion($request->region);
            if ($request->filled('kota') && !$citiesInRegion->contains($request->kota)) {
                $request->offsetUnset('kota');
                $request->query->remove('kota');
            }
        }

        if ($request->filled('sekolah_kodlan')) {
            $sekolah = \App\Models\Sekolah::find($request->sekolah_kodlan);
            if ($sekolah) {
                if ($request->filled('kota')) {
                    if ($sekolah->kota !== $request->kota) {
                        $request->offsetUnset('sekolah_kodlan');
                        $request->query->remove('sekolah_kodlan');
                    }
                } elseif ($request->filled('region')) {
                    $schoolRegion = $this->regionService->mapCityToRegion($sekolah->kota);
                    if ($schoolRegion !== $request->region) {
                        $request->offsetUnset('sekolah_kodlan');
                        $request->query->remove('sekolah_kodlan');
                    }
                }
            } else {
                $request->offsetUnset('sekolah_kodlan');
                $request->query->remove('sekolah_kodlan');
            }
        }

        // Build query dengan filtering & sorting menggunakan query service
        $ekstrakurikulerQuery = $this->queryService->buildFilteredQuery($request, auth()->user());
        $ekstrakurikulers = $ekstrakurikulerQuery->paginate(25)->withQueryString();

        // Get dropdown data
        $dropdownData = $this->queryService->getFormCreationData();
        
        // Get region data
        $regions = $this->regionService->getAvailableRegions();
        
        // Dapatkan opsi kota secara dinamis berdasarkan region terpilih
        if ($request->filled('region')) {
            $kotaOptions = $this->regionService->getCitiesByRegion($request->region)->toArray();
        } else {
            $kotaOptions = $this->regionService->getAvailableCities();
        }
        
        // Calculate statistics
        $stats = $this->queryService->getStatistics();

        return view('ekstrakurikuler.index', array_merge($dropdownData, [
            'ekstrakurikulers' => $ekstrakurikulers,
            'regions' => $regions,
            'kotaOptions' => $kotaOptions,
            'stats' => $stats,
        ]));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Clear dan initialize form data
        $this->formService->initializeForm();

        return $this->showStep(1);
    }

    /**
     * Show specific step of the multi-step form.
     */
    public function showStep($step = 1)
    {
        // Get form data from service
        $formData = $this->formService->getFormData();
        $finalStep = $this->formService->getFinalStep($formData);

        if ($step < 1 || $step > $finalStep) {
            $step = 1;
        }

        // Get dropdown data
        $dropdownData = $this->queryService->getFormCreationData();
        
        // Get region data
        $regions = $this->regionService->getAvailableRegions();
        $kotaOptions = $this->regionService->getAvailableCities();

        // Calculate next and previous steps
        $nextStep = $this->formService->calculateNextStep($step, $formData);
        $prevStep = $this->formService->calculatePreviousStep($step, $formData);

        return view('ekstrakurikuler.create', array_merge($dropdownData, [
            'step' => $step,
            'formData' => $formData,
            'regions' => $regions,
            'kotaOptions' => $kotaOptions,
            'nextStep' => $nextStep,
            'prevStep' => $prevStep,
            'finalStep' => $finalStep,
        ]));
    }

    /**
     * Process step data and redirect to next step.
     */
    public function processStep(Request $request)
    {
        $step = (int) $request->input('current_step', 1);
        
        // Get current form data just in case we need it for validation or navigation
        $currentFormData = $this->formService->getFormData();

        // Validasi menggunakan form service
        $this->formService->validateStep($request, $step);

        // Get dan save step data
        $stepData = $this->formService->getStepData($request, $step);
        $this->formService->saveStepData($stepData);

        $updatedFormData = $this->formService->getFormData();
        $finalStep = $this->formService->getFinalStep($updatedFormData);
        $isFinal = $this->formService->isFinalStep($step, $updatedFormData);

        // Jika ini final step, proses complete form
        if ($isFinal && ($request->has('submit_final') || $request->has('final_confirmation'))) {
            return $this->store($request);
        }

        // Redirect ke next step
        $nextStep = $this->formService->calculateNextStep($step, $updatedFormData);

        if ($nextStep > $finalStep) {
            $nextStep = $finalStep;
        }

        return redirect()->route('ekstrakurikuler.create.step', ['step' => $nextStep])
            ->with('success', 'Data berhasil disimpan. Lanjutkan ke tahap berikutnya.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $ekstrakurikuler = $this->formService->storeEkstrakurikuler($request);

            return redirect()->route('ekstrakurikuler.index')
                ->with('success', 'Program ekstrakurikuler berhasil dibuat!');

        } catch (\Exception $e) {
            Log::error('Error in EkstrakurikulerController@store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ekstrakurikuler $ekstrakurikuler)
    {
        $ekstrakurikuler->load([
            'sekolah',
            'sales',
            'rombels.sessions',
            'sessions.instruktur',
            'sessions.asisten',
        ]);

        // Get available workflow transitions
        $availableTransitions = $this->workflowService->getAvailableTransitions($ekstrakurikuler);

        return view('ekstrakurikuler.show', compact('ekstrakurikuler', 'availableTransitions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ekstrakurikuler $ekstrakurikuler)
    {
        $ekstrakurikuler->load(['rombels']);

        // Get dropdown data
        $dropdownData = $this->queryService->getFormCreationData();
        
        // Get region data
        $regions = $this->regionService->getAvailableRegions();
        $kotaOptions = $this->regionService->getAvailableCities();

        $statuses = [
            Ekstrakurikuler::STATUS_DRAFT => 'Draft',
            Ekstrakurikuler::STATUS_DIAJUKAN => 'Diajukan',
            Ekstrakurikuler::STATUS_DISETUJUI => 'Disetujui',
            Ekstrakurikuler::STATUS_DITOLAK => 'Ditolak',
            Ekstrakurikuler::STATUS_AKTIF => 'Aktif',
            Ekstrakurikuler::STATUS_SELESAI => 'Selesai',
            Ekstrakurikuler::STATUS_DIBATALKAN => 'Dibatalkan',
        ];

        return view('ekstrakurikuler.edit', array_merge($dropdownData, [
            'ekstrakurikuler' => $ekstrakurikuler,
            'regions' => $regions,
            'kotaOptions' => $kotaOptions,
            'statuses' => $statuses,
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEkstrakurikulerRequest $request, Ekstrakurikuler $ekstrakurikuler)
    {
        try {
            $this->formService->updateEkstrakurikuler($ekstrakurikuler, $request->validated());

            return redirect()->route('ekstrakurikuler.show', $ekstrakurikuler)
                ->with('success', 'Program ekstrakurikuler berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Error updating ekstrakurikuler', [
                'id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        try {
            // Check if program can be deleted
            if ($ekstrakurikuler->status === Ekstrakurikuler::STATUS_AKTIF) {
                return back()->withErrors(['error' => 'Program yang sedang aktif tidak dapat dihapus.']);
            }

            $ekstrakurikuler->delete();

            return redirect()->route('ekstrakurikuler.index')
                ->with('success', 'Program ekstrakurikuler berhasil dihapus!');

        } catch (\Exception $e) {
            Log::error('Error deleting ekstrakurikuler', [
                'id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }

    /**
     * Approve the specified ekstrakurikuler.
     */
    public function approve(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('approve', $ekstrakurikuler);

        $notes = $request->input('notes');
        $result = $this->workflowService->approve($ekstrakurikuler, $notes);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']]);
        }
    }

    /**
     * Reject the specified ekstrakurikuler.
     */
    public function reject(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('reject', $ekstrakurikuler);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $result = $this->workflowService->reject($ekstrakurikuler, $request->reason);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']]);
        }
    }

    /**
     * Activate the specified ekstrakurikuler.
     */
    public function activate(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('activate', $ekstrakurikuler);

        $result = $this->workflowService->activate($ekstrakurikuler);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']]);
        }
    }

    /**
     * Complete the specified ekstrakurikuler.
     */
    public function complete(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('complete', $ekstrakurikuler);

        $result = $this->workflowService->complete($ekstrakurikuler);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']]);
        }
    }

    /**
     * Cancel the specified ekstrakurikuler.
     */
    public function cancel(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('cancel', $ekstrakurikuler);

        if ($ekstrakurikuler->status === Ekstrakurikuler::STATUS_DIBATALKAN) {
            return back()->with('error', 'Program ini sudah dibatalkan sebelumnya.');
        }

        $request->validate([
            'reason' => 'required|string|min:5'
        ]);

        try {
            \DB::beginTransaction();

            // 1. Update Program Status
            $ekstrakurikuler->update([
                'status' => Ekstrakurikuler::STATUS_DIBATALKAN,
                'alasan_pembatalan' => $request->reason,
                'tanggal_dibatalkan' => now(),
                'dibatalkan_oleh' => auth()->id()
            ]);

            // 2. Cancel Future Sessions (Terjadwal & >= Today)
            $ekstrakurikuler->sessions()
                ->where('status', \App\Models\EkstrakurikulerSession::STATUS_TERJADWAL)
                ->where('tanggal_terjadwal', '>=', now()->toDateString())
                ->update([
                    'status' => \App\Models\EkstrakurikulerSession::STATUS_DIBATALKAN,
                    'alasan_pembatalan' => 'Program dihentikan oleh pusat: ' . $request->reason
                ]);

            // 3. Unenroll Active Students
            $ekstrakurikuler->enrollments()
                ->where('status', \App\Models\SiswaEkstrakurikuler::STATUS_AKTIF)
                ->update([
                    'status' => \App\Models\SiswaEkstrakurikuler::STATUS_KELUAR,
                    'tanggal_keluar' => now(),
                    'alasan_keluar' => 'Program dibatalkan/dihentikan: ' . $request->reason
                ]);

            \DB::commit();

            return back()->with('success', 'Program berhasil dibatalkan dan semua jadwal mendatang telah dibersihkan.');

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error canceling program: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat membatalkan program.');
        }
    }

    /**
     * Regenerate sessions untuk ekstrakurikuler yang sudah ada.
     */
    public function regenerateSessions(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        $result = $this->workflowService->regenerateSessions($ekstrakurikuler);

        if ($result['success']) {
            return back()->with('success', $result['message']);
        } else {
            return back()->withErrors(['error' => $result['message']]);
        }
    }

    /**
     * Store a new rombel for an existing ekstrakurikuler program.
     */
    public function storeRombel(Request $request, Ekstrakurikuler $ekstrakurikuler)
    {
        // Authorization: only admin roles
        $user = auth()->user();
        if (!$user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            abort(403, 'Akses Ditolak.');
        }

        $validated = $request->validate([
            'hari' => 'required|string|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'total_pertemuan' => 'required|integer|min:1|max:52',
            'jumlah_siswa' => 'required|integer|min:1',
            'ruangan' => 'nullable|string|max:255',
            'keterangan_ruangan' => 'nullable|string|max:500',
        ]);

        try {
            \DB::beginTransaction();

            // Determine next rombel number
            $maxNomor = $ekstrakurikuler->rombels()->max('nomor_rombel') ?? 0;
            $nomorRombelBaru = $maxNomor + 1;

            $rombel = \App\Models\EkstrakurikulerRombel::create([
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'nama_rombel' => "Rombel {$nomorRombelBaru}",
                'nomor_rombel' => $nomorRombelBaru,
                'hari' => $validated['hari'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'tanggal_mulai' => $validated['tanggal_mulai'],
                'tanggal_selesai' => $validated['tanggal_selesai'],
                'total_pertemuan' => $validated['total_pertemuan'],
                'jumlah_siswa' => $validated['jumlah_siswa'],
                'ruangan' => $validated['ruangan'] ?? '',
                'keterangan_ruangan' => $validated['keterangan_ruangan'] ?? '',
                'status' => \App\Models\EkstrakurikulerRombel::STATUS_BERLANGSUNG,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Update total_rombel on the parent program
            $ekstrakurikuler->update([
                'total_rombel' => $ekstrakurikuler->rombels()->count(),
            ]);

            // Auto-generate sessions using SchedulingService
            try {
                $schedulingService = app(\App\Services\SchedulingService::class);
                $schedulingService->generateSessionsForRombel($rombel, ['replace_existing' => true]);
            } catch (\Exception $e) {
                Log::warning("Gagal generate session untuk rombel baru {$rombel->id}: " . $e->getMessage());
            }

            \DB::commit();

            return redirect()->route('ekstrakurikuler.show', $ekstrakurikuler)
                ->with('success', "Rombel {$nomorRombelBaru} berhasil ditambahkan dengan jadwal sesi otomatis!");

        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('Error creating new rombel', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menambahkan rombel: ' . $e->getMessage()])
                ->withInput();
        }
    }
}