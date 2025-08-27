<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\Sekolah;
use App\Models\User;
use App\Http\Requests\StoreEkstrakurikulerRequest;
use App\Http\Requests\UpdateEkstrakurikulerRequest;
use App\Services\SchedulingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EkstrakurikulerController extends Controller
{
    protected SchedulingService $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
        $this->authorizeResource(Ekstrakurikuler::class, 'ekstrakurikuler');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $ekstrakurikulerQuery = Ekstrakurikuler::with(['sekolah', 'sales', 'rombels']);

        // Filter by user role
        if (!in_array($user->role, ['admin', 'webmaster'])) {
            $ekstrakurikulerQuery->where('user_id_sales', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $ekstrakurikulerQuery->where('status', $request->status);
        }

        // Filter by region
        if ($request->filled('region')) {
            $ekstrakurikulerQuery->where('region', $request->region);
        }

        // Filter by kota through sekolah relationship
        if ($request->filled('kota')) {
            $ekstrakurikulerQuery->whereHas('sekolah', function($q) use ($request) {
                $q->where('kota', $request->kota);
            });
        }

        // Filter by school
        if ($request->filled('sekolah_kodlan')) {
            $ekstrakurikulerQuery->where('sekolah_kodlan', $request->sekolah_kodlan);
        }

        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $ekstrakurikulerQuery->where(function($query) use ($searchTerm) {
                $query->where('kategori_program', 'like', "%{$searchTerm}%")
                      ->orWhereHas('sekolah', function($q) use ($searchTerm) {
                          $q->where('namasekolah', 'like', "%{$searchTerm}%");
                      });
            });
        }

        // Date range filter
        if ($request->filled('date_range')) {
            try {
                $dateRange = str_replace(' - ', ' to ', $request->date_range);
                $dates = array_map('trim', explode(' to ', $dateRange));
                
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                    $ekstrakurikulerQuery->whereBetween('tanggal_mulai', [$startDate, $endDate]);
                }
            } catch (\Exception $e) {
                Log::warning('Invalid date range format', ['date_range' => $request->date_range]);
            }
        }

        $ekstrakurikulers = $ekstrakurikulerQuery->latest()->paginate(25);

        // Get filter options
        $sekolahs = Sekolah::select('kodlan', 'namasekolah', 'kotkab')->orderBy('namasekolah')->get();
        // Get regions from actual city data - map common cities to regions
        $cityToRegionMap = $this->getCityToRegionMapping();
        
        $allKota = Sekolah::select('kota')->distinct()->whereNotNull('kota')->orderBy('kota')->pluck('kota');
        $regions = collect($allKota)->map(function($kota) use ($cityToRegionMap) {
            return $cityToRegionMap[$kota] ?? strtoupper(explode(' ', $kota)[1] ?? $kota);
        })->unique()->sort()->values()->toArray();
        
        $kotaOptions = $allKota->toArray();
        $statuses = [
            Ekstrakurikuler::STATUS_DRAFT => 'Draft',
            Ekstrakurikuler::STATUS_DIAJUKAN => 'Diajukan',
            Ekstrakurikuler::STATUS_DISETUJUI => 'Disetujui',
            Ekstrakurikuler::STATUS_DITOLAK => 'Ditolak',
            Ekstrakurikuler::STATUS_AKTIF => 'Aktif',
            Ekstrakurikuler::STATUS_SELESAI => 'Selesai',
            Ekstrakurikuler::STATUS_DIBATALKAN => 'Dibatalkan',
        ];

        // Calculate statistics
        $stats = [
            'total' => Ekstrakurikuler::count(),
            'aktif' => Ekstrakurikuler::where('status', Ekstrakurikuler::STATUS_AKTIF)->count(),
            'diajukan' => Ekstrakurikuler::where('status', Ekstrakurikuler::STATUS_DIAJUKAN)->count(),
            'selesai' => Ekstrakurikuler::where('status', Ekstrakurikuler::STATUS_SELESAI)->count(),
        ];

        return view('ekstrakurikuler.index', compact(
            'ekstrakurikulers', 
            'sekolahs', 
            'regions', 
            'kotaOptions',
            'statuses', 
            'stats'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Clear any previous session data
        Session::forget('ekstrakurikuler_form_data');
        
        return $this->showStep(1);
    }

    /**
     * Show specific step of the multi-step form.
     */
    public function showStep($step = 1)
    {
        $step = (int) $step;
        if ($step < 1 || $step > 10) {
            $step = 1;
        }

        // Get form data from session with default values
        $formData = Session::get('ekstrakurikuler_form_data', [
            'total_rombel' => 2,
            'total_siswa' => 0,
            'total_ruangan' => 1
        ]);
        
        // Get necessary data for dropdowns
        $sekolahs = Sekolah::select('kodlan', 'namasekolah', 'kotkab', 'kec')
                          ->orderBy('namasekolah')
                          ->get();
        
        $salesUsers = User::where('role', 'instruktur')
                         ->orWhere('role', 'asisten')
                         ->orderBy('nama_lengkap')
                         ->get();

        // Get regions from city data for consistency  
        $cityToRegionMap = $this->getCityToRegionMapping();
        
        $allKota = Sekolah::select('kota')->distinct()->whereNotNull('kota')->orderBy('kota')->pluck('kota');
        $regions = collect($allKota)->map(function($kota) use ($cityToRegionMap) {
            return $cityToRegionMap[$kota] ?? strtoupper(explode(' ', $kota)[1] ?? $kota);
        })->unique()->sort()->values()->toArray();
        
        $kotaOptions = $allKota->toArray();
        
        $statuses = [
            Ekstrakurikuler::STATUS_DRAFT => 'Draft',
            Ekstrakurikuler::STATUS_DIAJUKAN => 'Diajukan',
        ];

        return view('ekstrakurikuler.create', compact(
            'step', 
            'formData', 
            'sekolahs', 
            'salesUsers', 
            'regions', 
            'kotaOptions',
            'statuses'
        ));
    }

    /**
     * Process step data and redirect to next step.
     */
    public function processStep(Request $request)
    {
        $step = (int) $request->input('current_step', 1);
        $nextStep = (int) $request->input('next_step', $step + 1);
        
        // Validate current step
        $this->validateStep($request, $step);
        
        // Get existing form data
        $formData = Session::get('ekstrakurikuler_form_data', []);
        
        // Merge new data
        $stepData = $this->getStepData($request, $step);
        $formData = array_merge($formData, $stepData);
        
        // Save to session
        Session::put('ekstrakurikuler_form_data', $formData);
        
        // If this is the final step, process the complete form
        if ($request->has('submit_final')) {
            return $this->store($request);
        }
        
        // Redirect to next step
        if ($nextStep > 10) {
            $nextStep = 10; // Final step
        }
        
        return redirect()->route('ekstrakurikuler.create.step', ['step' => $nextStep])
                        ->with('success', 'Data berhasil disimpan. Lanjutkan ke tahap berikutnya.');
    }

    /**
     * Validate specific step data.
     */
    private function validateStep(Request $request, int $step)
    {
        $rules = [];
        
        switch ($step) {
            case 1: // Basic Program Info
                $rules = [
                    'kategori_program' => 'required|string|in:Coding Scratch,English Course,Micro:bit Learning Kit,Pictoblox AI,Robotik Explorer,Robotik Jimu',
                    'user_id_sales' => 'required|exists:users,id',
                    'region' => 'nullable|string',
                    'city' => 'nullable|string',
                    'status' => 'required|string|in:draft,diajukan',
                ];
                break;
                
            case 2: // School Selection & Details
                $rules = [
                    'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
                    'alamat_lengkap' => 'required|string',
                    'google_maps_link' => 'nullable|url',
                    'jarak_km' => 'required|numeric|min:0',
                    'kepala_sekolah' => 'required|string|max:255',
                    'penanggung_jawab' => 'required|string|max:255',
                    'no_telepon' => 'required|string|max:20',
                ];
                break;
                
            case 3: // Technical Requirements
                $rules = [
                    'koneksi_internet' => 'required|in:ada,tidak_ada,tidak_diketahui',
                    'proyektor' => 'required|in:ada,tidak_ada,tidak_diketahui',
                    'keterangan_proyektor' => 'nullable|string',
                    'kabel_hdmi' => 'required|in:ada,tidak_ada,tidak_diketahui',
                    'kabel_vga' => 'required|in:ada,tidak_ada,tidak_diketahui',
                    'keterangan_kabel' => 'nullable|string',
                ];
                break;
                
            case 4: // Class Structure
                $rules = [
                    'total_siswa' => 'required|integer|min:1',
                    'total_ruangan' => 'required|integer|min:1',
                    'total_rombel' => 'required|integer|min:1|max:5',
                ];
                break;
                
            case 5:
            case 6:
            case 7:
            case 8:
            case 9: // Rombel Details (5-9)
                $rombelNumber = $step - 4;
                $rules = [
                    "rombel_{$rombelNumber}_total_pertemuan" => 'required|integer|min:1',
                    "rombel_{$rombelNumber}_tanggal_mulai" => 'required|date',
                    "rombel_{$rombelNumber}_tanggal_selesai" => 'required|date|after_or_equal:rombel_' . $rombelNumber . '_tanggal_mulai',
                    "rombel_{$rombelNumber}_hari" => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
                    "rombel_{$rombelNumber}_jam_mulai" => 'required|date_format:H:i',
                    "rombel_{$rombelNumber}_jumlah_siswa" => 'required|integer|min:1',
                ];
                break;
        }
        
        $request->validate($rules);
    }

    /**
     * Get data for specific step.
     */
    private function getStepData(Request $request, int $step): array
    {
        $data = [];
        
        switch ($step) {
            case 1:
                $data = $request->only([
                    'kategori_program', 'user_id_sales', 'region', 'city', 'status', 'deskripsi'
                ]);
                
                // Set nama_program based on kategori_program for compatibility
                if ($request->has('kategori_program')) {
                    $data['nama_program'] = $request->kategori_program;
                }
                break;
                
            case 2:
                $data = $request->only([
                    'sekolah_kodlan', 'alamat_lengkap', 'google_maps_link', 
                    'jarak_km', 'kepala_sekolah', 'penanggung_jawab', 'no_telepon'
                ]);
                break;
                
            case 3:
                $data = $request->only([
                    'koneksi_internet', 'proyektor', 
                    'keterangan_proyektor', 'kabel_hdmi', 'kabel_vga', 'keterangan_kabel'
                ]);
                break;
                
            case 4:
                $data = $request->only([
                    'total_siswa', 'total_ruangan', 'total_rombel'
                ]);
                break;
                
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
                $rombelNumber = $step - 4;
                $prefix = "rombel_{$rombelNumber}_";
                $data["rombels"][$rombelNumber] = [
                    'total_pertemuan' => $request->input($prefix . 'total_pertemuan'),
                    'tanggal_mulai' => $request->input($prefix . 'tanggal_mulai'),
                    'tanggal_selesai' => $request->input($prefix . 'tanggal_selesai'),
                    'hari' => $request->input($prefix . 'hari'),
                    'jam_mulai' => $request->input($prefix . 'jam_mulai'),
                    'jumlah_siswa' => $request->input($prefix . 'jumlah_siswa'),
                    'ruangan' => $request->input($prefix . 'ruangan', ''),
                    'keterangan_ruangan' => $request->input($prefix . 'keterangan_ruangan', ''),
                ];
                break;
        }
        
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Get complete form data from session
            $formData = Session::get('ekstrakurikuler_form_data', []);
            
            // Final validation
            $this->validateFinalForm($formData);
            
            // Calculate totals
            $totalSiswaRombel = 0;
            $tanggalMulaiEarliest = null;
            $tanggalSelesaiLatest = null;
            $totalPertemuanAll = 0;
            
            if (isset($formData['rombels'])) {
                foreach ($formData['rombels'] as $rombel) {
                    $totalSiswaRombel += $rombel['jumlah_siswa'];
                    $totalPertemuanAll += $rombel['total_pertemuan'];
                    
                    $mulai = Carbon::parse($rombel['tanggal_mulai']);
                    $selesai = Carbon::parse($rombel['tanggal_selesai']);
                    
                    if (!$tanggalMulaiEarliest || $mulai->lt($tanggalMulaiEarliest)) {
                        $tanggalMulaiEarliest = $mulai;
                    }
                    
                    if (!$tanggalSelesaiLatest || $selesai->gt($tanggalSelesaiLatest)) {
                        $tanggalSelesaiLatest = $selesai;
                    }
                }
            }
            
            // Create main Ekstrakurikuler record
            $ekstrakurikuler = Ekstrakurikuler::create([
                'kategori_program' => $formData['kategori_program'],
                'deskripsi' => $formData['deskripsi'] ?? null,
                'user_id_sales' => $formData['user_id_sales'],
                'region' => $formData['region'],
                'city' => $formData['city'] ?? $this->getCityFromSekolah($formData['sekolah_kodlan']),
                'sekolah_kodlan' => $formData['sekolah_kodlan'],
                'alamat_lengkap' => $formData['alamat_lengkap'],
                'google_maps_link' => $formData['google_maps_link'] ?? null,
                'jarak_km' => $formData['jarak_km'],
                'kepala_sekolah' => $formData['kepala_sekolah'],
                'penanggung_jawab' => $formData['penanggung_jawab'],
                'no_telepon' => $formData['no_telepon'],
                'koneksi_internet' => $formData['koneksi_internet'],
                'proyektor' => $formData['proyektor'],
                'keterangan_proyektor' => $formData['keterangan_proyektor'] ?? null,
                'kabel_hdmi' => $formData['kabel_hdmi'],
                'kabel_vga' => $formData['kabel_vga'],
                'keterangan_kabel' => $formData['keterangan_kabel'] ?? null,
                'total_siswa' => $totalSiswaRombel,
                'total_ruangan' => $formData['total_ruangan'],
                'total_rombel' => $formData['total_rombel'],
                'tanggal_mulai' => $tanggalMulaiEarliest,
                'tanggal_selesai' => $tanggalSelesaiLatest,
                'total_pertemuan' => $totalPertemuanAll,
                'frekuensi' => Ekstrakurikuler::FREKUENSI_MINGGUAN, // Default
                'status' => $formData['status'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            
            // Create Rombel records
            if (isset($formData['rombels'])) {
                foreach ($formData['rombels'] as $index => $rombelData) {
                    // Calculate jam_selesai (assume 2 hours duration)
                    $jamMulai = Carbon::createFromFormat('H:i', $rombelData['jam_mulai']);
                    $jamSelesai = $jamMulai->copy()->addHours(2);
                    
                    $rombel = EkstrakurikulerRombel::create([
                        'ekstrakurikuler_id' => $ekstrakurikuler->id,
                        'nama_rombel' => "Rombel {$index}",
                        'nomor_rombel' => $index,
                        'jumlah_siswa' => $rombelData['jumlah_siswa'],
                        'ruangan' => $rombelData['ruangan'] ?? "Ruang {$index}",
                        'keterangan_ruangan' => $rombelData['keterangan_ruangan'] ?? null,
                        'tanggal_mulai' => $rombelData['tanggal_mulai'],
                        'tanggal_selesai' => $rombelData['tanggal_selesai'],
                        'hari' => $rombelData['hari'],
                        'jam_mulai' => $rombelData['jam_mulai'],
                        'jam_selesai' => $jamSelesai->format('H:i'),
                        'total_pertemuan' => $rombelData['total_pertemuan'],
                        'frekuensi' => EkstrakurikulerRombel::FREKUENSI_MINGGUAN,
                        'pertemuan_selesai' => 0,
                        'status' => EkstrakurikulerRombel::STATUS_BELUM_MULAI,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);

                    // Auto-generate sessions untuk rombel ini
                    try {
                        $this->schedulingService->generateSessionsForRombel($rombel, [
                            'skip_holidays' => true
                        ]);
                        Log::info("Auto-generated sessions for rombel {$rombel->id}");
                    } catch (\Exception $e) {
                        Log::error("Failed to generate sessions for rombel {$rombel->id}: " . $e->getMessage());
                        // Don't fail the entire process, just log the error
                    }
                }
            }
            
            DB::commit();
            
            // Clear session data
            Session::forget('ekstrakurikuler_form_data');
            
            return redirect()->route('ekstrakurikuler.show', $ekstrakurikuler)
                           ->with('success', 'Program ekstrakurikuler berhasil dibuat!');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating ekstrakurikuler', [
                'error' => $e->getMessage(),
                'formData' => $formData ?? null
            ]);
            
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Validate final form data.
     */
    private function validateFinalForm(array $formData)
    {
        if (empty($formData['kategori_program'])) {
            throw new \Exception('Nama program harus diisi');
        }
        
        if (empty($formData['total_rombel']) || $formData['total_rombel'] < 1) {
            throw new \Exception('Jumlah rombel harus minimal 1');
        }
        
        if (empty($formData['rombels']) || count($formData['rombels']) < $formData['total_rombel']) {
            throw new \Exception('Data rombel tidak lengkap');
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
            'sessions.asisten'
        ]);
        
        return view('ekstrakurikuler.show', compact('ekstrakurikuler'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ekstrakurikuler $ekstrakurikuler)
    {
        $ekstrakurikuler->load(['rombels']);
        
        $sekolahs = Sekolah::select('kodlan', 'namasekolah', 'kotkab', 'kec')
                          ->orderBy('namasekolah')
                          ->get();
        
        $salesUsers = User::where('role', 'instruktur')
                         ->orWhere('role', 'asisten')
                         ->orderBy('nama_lengkap')
                         ->get();

        // Get regions from city data for consistency
        $cityToRegionMap = $this->getCityToRegionMapping();
        
        $allKota = Sekolah::select('kota')->distinct()->whereNotNull('kota')->orderBy('kota')->pluck('kota');
        $regions = collect($allKota)->map(function($kota) use ($cityToRegionMap) {
            return $cityToRegionMap[$kota] ?? strtoupper(explode(' ', $kota)[1] ?? $kota);
        })->unique()->sort()->values()->toArray();
        
        $kotaOptions = $allKota->toArray();
        
        $statuses = [
            Ekstrakurikuler::STATUS_DRAFT => 'Draft',
            Ekstrakurikuler::STATUS_DIAJUKAN => 'Diajukan',
            Ekstrakurikuler::STATUS_DISETUJUI => 'Disetujui',
            Ekstrakurikuler::STATUS_DITOLAK => 'Ditolak',
            Ekstrakurikuler::STATUS_AKTIF => 'Aktif',
            Ekstrakurikuler::STATUS_SELESAI => 'Selesai',
            Ekstrakurikuler::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
        
        return view('ekstrakurikuler.edit', compact(
            'ekstrakurikuler', 
            'sekolahs', 
            'salesUsers', 
            'regions', 
            'kotaOptions',
            'statuses'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEkstrakurikulerRequest $request, Ekstrakurikuler $ekstrakurikuler)
    {
        try {
            DB::beginTransaction();
            
            $ekstrakurikuler->update($request->validated());
            
            DB::commit();
            
            return redirect()->route('ekstrakurikuler.show', $ekstrakurikuler)
                           ->with('success', 'Program ekstrakurikuler berhasil diperbarui!');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating ekstrakurikuler', [
                'id' => $ekstrakurikuler->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data.'])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ekstrakurikuler $ekstrakurikuler)
    {
        try {
            DB::beginTransaction();
            
            // Check if program can be deleted
            if ($ekstrakurikuler->isActive()) {
                return back()->withErrors(['error' => 'Program yang sedang aktif tidak dapat dihapus.']);
            }
            
            $ekstrakurikuler->delete();
            
            DB::commit();
            
            return redirect()->route('ekstrakurikuler.index')
                           ->with('success', 'Program ekstrakurikuler berhasil dihapus!');
                           
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting ekstrakurikuler', [
                'id' => $ekstrakurikuler->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menghapus data.']);
        }
    }

    /**
     * Approve the specified ekstrakurikuler.
     */
    public function approve(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('approve', $ekstrakurikuler);
        
        if (!$ekstrakurikuler->canBeApproved()) {
            return back()->withErrors(['error' => 'Program tidak dapat disetujui.']);
        }
        
        $ekstrakurikuler->update([
            'status' => Ekstrakurikuler::STATUS_DISETUJUI,
            'tanggal_disetujui' => now(),
            'disetujui_oleh' => auth()->id(),
        ]);
        
        return back()->with('success', 'Program ekstrakurikuler berhasil disetujui!');
    }

    /**
     * Activate the specified ekstrakurikuler.
     */
    public function activate(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('activate', $ekstrakurikuler);
        
        if (!$ekstrakurikuler->canBeActivated()) {
            return back()->withErrors(['error' => 'Program tidak dapat diaktifkan.']);
        }
        
        $ekstrakurikuler->update([
            'status' => Ekstrakurikuler::STATUS_AKTIF,
        ]);
        
        return back()->with('success', 'Program ekstrakurikuler berhasil diaktifkan!');
    }

    /**
     * Get form data as JSON for AJAX requests.
     */
    public function getFormData()
    {
        $formData = Session::get('ekstrakurikuler_form_data', []);
        return response()->json($formData);
    }

    /**
     * Clear form session data.
     */
    public function clearFormData()
    {
        Session::forget('ekstrakurikuler_form_data');
        return response()->json(['success' => true]);
    }

    /**
     * Preview sessions yang akan di-generate berdasarkan data rombel.
     */
    public function previewSessions(Request $request)
    {
        $formData = Session::get('ekstrakurikuler_form_data', []);
        
        if (empty($formData['rombels'])) {
            return response()->json([
                'success' => false,
                'message' => 'Data rombel belum tersedia'
            ]);
        }

        $previews = [];
        
        try {
            foreach ($formData['rombels'] as $index => $rombelData) {
                // Create temporary rombel object untuk preview
                $tempRombel = new EkstrakurikulerRombel([
                    'nama_rombel' => "Rombel {$index}",
                    'nomor_rombel' => $index,
                    'tanggal_mulai' => $rombelData['tanggal_mulai'],
                    'tanggal_selesai' => $rombelData['tanggal_selesai'],
                    'hari' => $rombelData['hari'],
                    'jam_mulai' => $rombelData['jam_mulai'],
                    'jam_selesai' => Carbon::createFromFormat('H:i', $rombelData['jam_mulai'])->addHours(2)->format('H:i'),
                    'total_pertemuan' => $rombelData['total_pertemuan'],
                    'frekuensi' => EkstrakurikulerRombel::FREKUENSI_MINGGUAN,
                ]);

                // Calculate session dates
                $sessionDates = $this->schedulingService->calculateSessionDates($tempRombel, [
                    'skip_holidays' => true
                ]);

                $previews[] = [
                    'rombel_info' => [
                        'nama' => $tempRombel->nama_rombel,
                        'nomor' => $tempRombel->nomor_rombel,
                        'hari' => ucfirst($rombelData['hari']),
                        'waktu' => $rombelData['jam_mulai'] . ' - ' . $tempRombel->jam_selesai,
                        'periode' => Carbon::parse($rombelData['tanggal_mulai'])->format('d/m/Y') . ' - ' . 
                                   Carbon::parse($rombelData['tanggal_selesai'])->format('d/m/Y'),
                        'total_pertemuan_target' => $rombelData['total_pertemuan'],
                        'jumlah_siswa' => $rombelData['jumlah_siswa'],
                        'ruangan' => $rombelData['ruangan'] ?? "Ruang {$index}",
                    ],
                    'sessions_preview' => $sessionDates->take(5)->map(function($date, $sessionIndex) {
                        return [
                            'nomor_pertemuan' => $sessionIndex + 1,
                            'tanggal' => $date->format('d/m/Y'),
                            'hari' => $date->locale('id')->translatedFormat('l'),
                            'bulan_tahun' => $date->format('M Y'),
                        ];
                    })->values(),
                    'total_sessions_generated' => $sessionDates->count(),
                    'sessions_summary' => [
                        'first_session' => $sessionDates->first()?->format('d/m/Y'),
                        'last_session' => $sessionDates->last()?->format('d/m/Y'),
                        'total_weeks' => $sessionDates->count(),
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'previews' => $previews,
                'summary' => [
                    'total_rombels' => count($previews),
                    'total_sessions' => array_sum(array_column($previews, 'total_sessions_generated')),
                    'earliest_start' => collect($previews)->min('sessions_summary.first_session'),
                    'latest_end' => collect($previews)->max('sessions_summary.last_session'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error previewing sessions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate preview: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Regenerate sessions untuk ekstrakurikuler yang sudah ada.
     */
    public function regenerateSessions(Ekstrakurikuler $ekstrakurikuler)
    {
        $this->authorize('update', $ekstrakurikuler);

        try {
            DB::beginTransaction();

            $totalSessions = 0;
            $rombels = $ekstrakurikuler->rombels;

            foreach ($rombels as $rombel) {
                $sessions = $this->schedulingService->generateSessionsForRombel($rombel, [
                    'replace_existing' => true,
                    'skip_holidays' => true
                ]);
                
                $totalSessions += $sessions->count();
            }

            DB::commit();

            return redirect()->back()->with('success', 
                "Berhasil regenerate {$totalSessions} sessions untuk {$rombels->count()} rombel"
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to regenerate sessions for ekstrakurikuler {$ekstrakurikuler->id}: " . $e->getMessage());
            
            return redirect()->back()->withErrors([
                'error' => 'Gagal regenerate sessions: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API endpoint untuk mendapatkan sekolah berdasarkan kota
     */
    public function getSekolahByCity(Request $request)
    {
        $kota = $request->get('kota');
        
        if (!$kota) {
            return response()->json([
                'status' => 'error',
                'message' => 'Parameter kota diperlukan',
                'data' => []
            ]);
        }

        $sekolahList = Sekolah::where('kota', $kota)
                            ->select('kodlan', 'namasekolah', 'kotkab', 'kec')
                            ->orderBy('namasekolah')
                            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data sekolah berhasil diambil',
            'data' => $sekolahList
        ]);
    }

    /**
     * Helper method untuk mendapatkan kota dari sekolah_kodlan
     */
    private function getKotaFromSekolah($kodlan)
    {
        if (!$kodlan) return null;
        
        $sekolah = Sekolah::find($kodlan);
        return $sekolah ? $sekolah->kota : null;
    }

    /**
     * Helper method untuk mapping city ke region
     */
    private function getCityToRegionMapping()
    {
        return [
            'JAKARTA PUSAT' => 'JAKARTA',
            'JAKARTA UTARA' => 'JAKARTA', 
            'JAKARTA SELATAN' => 'JAKARTA',
            'JAKARTA TIMUR' => 'JAKARTA',
            'JAKARTA BARAT' => 'JAKARTA',
            'KOTA DEPOK' => 'DEPOK',
            'KABUPATEN BOGOR' => 'BOGOR',
            'KOTA BOGOR' => 'BOGOR',
            'KOTA TANGERANG' => 'TANGERANG',
            'KABUPATEN TANGERANG' => 'TANGERANG',
            'KOTA TANGERANG SELATAN' => 'TANGERANG',
            'KOTA BEKASI' => 'BEKASI',
            'KABUPATEN BEKASI' => 'BEKASI'
        ];
    }
}