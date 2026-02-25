<?php

namespace App\Http\Controllers;

use App\Exports\LaporanMengajarExport;
use App\Http\Requests\StoreLaporanMengajarRequest;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class LaporanMengajarController extends Controller
{
    protected $fileUploadService;

    public function __construct(\App\Services\FileUploadService $fileUploadService)
    {
        $this->authorizeResource(LaporanMengajar::class, 'laporan_mengajar');
        $this->fileUploadService = $fileUploadService;
    }
    
    // ... (lines 24-295 are mostly identical unless we need to touch store/update)
    
    // skipping to store method implementation detail reuse isn't easy with replace tool if too long gap
    // I will target the store method specific block first.
    
    // Actually, I can't easily replace the constructor AND index AND store in one go if they are far apart.
    // I will do Constructor first.


    public function index(Request $request)
    {
        $user = Auth::user();
        $laporanQuery = LaporanMengajar::with('instruktur', 'sekolah', 'asisten');

        // Filter by user role
        if (! in_array($user->role, ['admin', 'admin_sistem', 'webmaster'])) {
            $laporanQuery->where('user_id_instruktur', $user->id);
        }

        // Filter by instructor
        if ($request->filled('instruktur_id')) {
            $laporanQuery->where('user_id_instruktur', $request->instruktur_id);
        }

        // Filter by category (ekstrakurikuler vs regular)
        if ($request->filled('kategori')) {
            if ($request->kategori === 'ekstrakurikuler') {
                $laporanQuery->ekstrakurikuler();
            } elseif ($request->kategori === 'regular') {
                $laporanQuery->regular();
            }
        }

        // Date range filter with improved validation
        if ($request->filled('date_range')) {
            try {
                $dateRange = str_replace(' - ', ' to ', $request->date_range);
                $dates = array_map('trim', explode(' to ', $dateRange));

                $parseDate = function ($dateString) {
                    // Try to parse from d/m/Y format first
                    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateString)) {
                        return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
                    }

                    // Fallback to other formats if needed
                    return \Carbon\Carbon::parse($dateString);
                };

                if (count($dates) === 1) {
                    // Single date filter
                    $date = $parseDate($dates[0]);
                    $laporanQuery->whereDate('jadwal_mengajar', $date);
                } elseif (count($dates) === 2) {
                    // Date range filter
                    $startDate = $parseDate($dates[0])->startOfDay();
                    $endDate = $parseDate($dates[1])->endOfDay();
                    $laporanQuery->whereBetween('jadwal_mengajar', [$startDate, $endDate]);
                }
            } catch (\Exception $e) {
                return redirect()->route('laporan-mengajar.index')
                    ->with('error', 'Format tanggal tidak valid. Gunakan format dd/mm/yyyy');
            }
        }

        // Category filter
        if ($request->filled('kategori')) {
            $laporanQuery->where('kategori_pengajaran', $request->kategori);
        }

        // Search by school name or rombel
        if ($request->filled('search')) {
            $searchTerm = '%'.$request->search.'%';
            $laporanQuery->where(function ($query) use ($searchTerm) {
                $query->whereHas('sekolah', function ($q) use ($searchTerm) {
                    $q->where('namasekolah', 'LIKE', $searchTerm);
                })->orWhere('rombel', 'LIKE', $searchTerm);
            });
        }

        // Get statistics
        $statsQuery = clone $laporanQuery;

        $totalLaporan = $statsQuery->count();
        $laporanMingguIni = $statsQuery->whereBetween('jadwal_mengajar', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();
        $laporanBulanIni = $statsQuery->whereBetween('jadwal_mengajar', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();

        $totalInstruktur = User::whereIn('role', ['instruktur', 'admin'])
            ->when(! in_array($user->role, ['admin', 'admin_sistem', 'webmaster']), function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->count();

        // Get paginated results
        $laporan = $laporanQuery->latest()->paginate(10);
        
        // Optimize: Cache expensive dropdown data
        $instructors = \Illuminate\Support\Facades\Cache::remember('instructors_list', 60, function () {
            return User::whereIn('role', ['instruktur', 'admin'])->orderBy('nama_lengkap')->get();
        });

        // Optimize: Cache categories
        $kategoriList = \Illuminate\Support\Facades\Cache::remember('ekskul_categories', 60, function () {
            return \App\Models\Ekstrakurikuler::distinct()
                ->whereNotNull('kategori_program')
                ->pluck('kategori_program')
                ->sort()
                ->values()
                ->toArray();
        });
        
        return view('laporan-mengajar.index', compact(
            'laporan',
            'instructors',
            'kategoriList',
            'totalLaporan',
            'laporanMingguIni',
            'laporanBulanIni',
            'totalInstruktur'
        ));
    }

    private function getKategoriList(): array
    {
        return [
            'Pameran', 'Pendampingan Lomba', 'Sosialisasi bersama Sales', 'Trial Class',
        ];
    }

    public function export(Request $request, $format)
    {
        // Validate the format
        if (! in_array($format, ['excel', 'pdf'])) {
            return back()->with('error', 'Format ekspor tidak valid');
        }

        $user = Auth::user();
        $query = LaporanMengajar::with('instruktur', 'sekolah', 'asisten');

        if (! in_array($user->role, ['admin', 'admin_sistem', 'webmaster'])) {
            $query->where('user_id_instruktur', $user->id);
        }

        if ($request->filled('instruktur_id')) {
            $query->where('user_id_instruktur', $request->instruktur_id);
        }

        if ($request->filled('date_range')) {
            try {
                $dateRange = str_replace(' - ', ' to ', $request->date_range);
                $dates = array_map('trim', explode(' to ', $dateRange));
                $parseDate = function ($dateString) {
                    try {
                        return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
                    } catch (\Exception $e) {
                        return \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);
                    }
                };

                if (count($dates) === 1) {
                    $date = $parseDate($dates[0]);
                    $query->whereDate('jadwal_mengajar', $date);
                } elseif (count($dates) === 2) {
                    $startDate = $parseDate($dates[0])->startOfDay();
                    $endDate = $parseDate($dates[1])->endOfDay();
                    $query->whereBetween('jadwal_mengajar', [$startDate, $endDate]);
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Format tanggal tidak valid');
            }
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_pengajaran', $request->kategori);
        }

        $laporan = $query->latest()->get();

        if ($format === 'excel') {
            return Excel::download(new LaporanMengajarExport($laporan), 'laporan-mengajar.xlsx');
        }

        if ($format === 'pdf') {
            $pdf = PDF::loadView('exports.laporan-mengajar-pdf', compact('laporan'));

            return $pdf->download('laporan-mengajar.pdf');
        }

        return back()->with('error', 'Format ekspor tidak valid');
    }

    public function search(Request $request)
    {
        $searchTerm = $request->query('q', '');

        $sekolahQuery = Sekolah::query()
            ->where(function ($query) use ($searchTerm) {
                $query->where('namasekolah', 'LIKE', '%'.$searchTerm.'%')
                      ->orWhere('kodlan', 'LIKE', '%'.$searchTerm.'%');
            })
            ->orderBy('namasekolah', 'asc')
            ->limit(20);

        $sekolahs = $sekolahQuery->get();

        return response()->json([
            'results' => $sekolahs->map(function ($sekolah) {
                return [
                    'id' => $sekolah->kodlan,
                    'text' => $sekolah->namasekolah.' ('.$sekolah->kodlan.') - '.$sekolah->kec.', '.$sekolah->kotkab,
                ];
            }),
            'pagination' => ['more' => false],
        ]);
    }

    public function getPendingSessions()
    {
        $sessions = EkstrakurikulerSession::with(['rombel.ekstrakurikuler.sekolah', 'rombel'])
            ->where('user_id_instruktur', Auth::id())
            ->whereIn('status', ['terjadwal', 'berlangsung'])
            ->orderBy('tanggal_terjadwal', 'asc')
            ->orderBy('jam_mulai_terjadwal', 'asc')
            ->get();

        return response()->json([
            'sessions' => $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'sekolah' => $session->rombel->ekstrakurikuler->sekolah->namasekolah,
                    'program' => $session->rombel->ekstrakurikuler->kategori_program,
                    'rombel' => $session->rombel->nama_rombel,
                    'pertemuan_ke' => $session->nomor_pertemuan,
                    'tanggal' => $session->tanggal_terjadwal->format('d/m/Y'),
                    'jam' => $session->jadwal_waktu,
                    'topik' => $session->topik_materi ?? '-',
                    'url' => route('ekstrakurikuler.sessions.report.create', $session)
                ];
            })
        ]);
    }

    /**
     * Get materi list based on category for Dynamic Dropdown
     */
    public function getMateri(Request $request)
    {
        $kategori = $request->query('kategori');
        
        if (!$kategori) {
            return response()->json([]);
        }

        $materi = \App\Models\RefMateri::where('kategori', $kategori)
            ->orderBy('materi', 'asc')
            ->pluck('materi');

        return response()->json($materi);
    }

    public function create()
    {
        // Fix: Only show instructors in dropdown
        $instructors = User::where('role', 'instruktur')
            ->where('id', '!=', auth()->id())
            ->orderBy('nama_lengkap')
            ->get();
            
        $selectedSekolah = null;

        if (old('kodlan')) {
            $selectedSekolah = Sekolah::find(old('kodlan'));
        }

        $kategori = $this->getKategoriList();

        return view('laporan-mengajar.create', compact('instructors', 'selectedSekolah', 'kategori'));
    }

    public function store(StoreLaporanMengajarRequest $request)
    {
        $validated = $request->validated();

        // Enforce H+1 Restriction for Instructors (Manual Input)
        if (Auth::user()->role === 'instruktur') {
             try {
                $inputDate = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['jadwal_mengajar'])->startOfDay();
                
                // Jika input date adalah masa lalu lebih dari 1 hari dari sekarang
                if ($inputDate->addDay()->endOfDay()->isBefore(now())) {
                     return redirect()->back()
                        ->withInput()
                        ->with('error', 'Anda tidak dapat membuat laporan untuk tanggal yang sudah lewat H+1. Hubungi Admin.');
                }
             } catch (\Exception $e) {
                 // Date parsing validasi sudah di handle request validator
             }
        }

        if (! isset($validated['kategori_pengajaran'])) {
            $validated['kategori_pengajaran'] = $request->kategori_pengajaran;
        }

        // Format the date correctly for database storage
        $validated['jadwal_mengajar'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['jadwal_mengajar'])->format('Y-m-d');

        // Add the instructor ID
        $validated['user_id_instruktur'] = Auth::id();

        // Get complete school data
        $validated['status'] = $request->has('draft') ? 'draft' : 'submitted';

        $validated['jumlah_siswa_hadir'] = 0;
        $validated['jumlah_siswa_tidak_hadir'] = 0;
        $validated['jumlah_siswa_keluar'] = 0;
        
        // Default values for removed evaluation fields
        $validated['keaktifan'] = 'aktif'; 
        $validated['pemahaman_materi'] = 'paham';
        $validated['refleksi_siswa'] = '-';
        $validated['refleksi_capaian'] = '-';

        // Handle file uploads
        if ($request->hasFile('foto_kegiatan')) {
            $validated['foto_kegiatan'] = $this->fileUploadService->upload(
                $request->file('foto_kegiatan'), 
                'reports', 
                'activities'
            );
        }

        // Removed: foto_absensi_siswa logic as requested

        // Create the report
        $laporan = LaporanMengajar::create($validated);

        // ACTIVITY LOGGING (Create Report)
        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'buat_laporan',
            'description' => 'Membuat laporan mengajar baru (Manual). Sekolah: ' . ($request->sekolah_nama ?? $validated['sekolah_kodlan']),
            'subject_type' => LaporanMengajar::class,
            'subject_id' => $laporan->id,
            'properties' => $validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect to attendance input
        return redirect()->route('laporan-mengajar.absensi.create', $laporan)
            ->with('success', 'Laporan dasar berhasil dibuat! Sekarang, silakan isi absensi.');
    }

    public function show(LaporanMengajar $laporanMengajar)
    {
        $this->authorize('view', $laporanMengajar);

        $isEkstrakurikuler = $laporanMengajar->isFromEkstrakurikuler();
        $ekstrakurikulerSession = null;
        $ekstrakurikulerData = null;

        if ($isEkstrakurikuler) {
            $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;
            if ($ekstrakurikulerSession) {
                $ekstrakurikulerData = [
                    'kategori_program' => $ekstrakurikulerSession->rombel->ekstrakurikuler->kategori_program ?? 'Ekstrakurikuler',
                ];
            }
        }

        return view('laporan-mengajar.show', compact('laporanMengajar', 'isEkstrakurikuler', 'ekstrakurikulerSession', 'ekstrakurikulerData'));
    }

    public function edit(LaporanMengajar $laporanMengajar)
    {
        $this->authorize('update', $laporanMengajar);
        
        // Fix: Only show instructors in dropdown
        $instructors = User::where('role', 'instruktur')
            ->where('id', '!=', $laporanMengajar->user_id_instruktur)
            ->orderBy('nama_lengkap')
            ->get();
            
        $kategori = $this->getKategoriList();

        return view('laporan-mengajar.edit', compact('laporanMengajar', 'instructors', 'kategori'));
    }

    public function update(Request $request, LaporanMengajar $laporanMengajar)
    {
        $this->authorize('update', $laporanMengajar);

        $validated = $request->validate($this->validationRules());

        // Handle File Uploads (Foto Kegiatan)
        if ($request->hasFile('foto_kegiatan')) {
            // Delete old file
            if ($laporanMengajar->foto_kegiatan) {
                Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
            }
            $validated['foto_kegiatan'] = $this->fileUploadService->upload(
                $request->file('foto_kegiatan'), 
                'reports', 
                'activities'
            );
        } elseif ($request->has('hapus_foto_kegiatan') && $request->hapus_foto_kegiatan == 1) {
             if ($laporanMengajar->foto_kegiatan) {
                Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
            }
            $validated['foto_kegiatan'] = null;
        }

        // Handle removed fields defaults if they are somehow missing in request but required in DB?
        // Assuming DB columns are nullable or have defaults. If not, we might need to set them.
        // Based on migration, they might be nullable. If not, we set them to existing values or defaults.
        
        $laporanMengajar->update($validated);

        // Activity Log
        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'update_laporan',
            'description' => 'Memperbarui laporan mengajar. ID: ' . $laporanMengajar->id,
            'subject_type' => LaporanMengajar::class,
            'subject_id' => $laporanMengajar->id,
            'properties' => $laporanMengajar->getChanges(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('laporan-mengajar.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    /**
     * Ekstrakurikuler dashboard — redirects to index with ekskul filter.
     */
    public function ekstrakurikulerDashboard()
    {
        return redirect()->route('laporan-mengajar.index', ['kategori' => 'ekstrakurikuler']);
    }

    /**
     * Create a laporan mengajar from an ekstrakurikuler session.
     */
    public function createFromEkstrakurikuler(EkstrakurikulerSession $session)
    {
        $this->authorize('create', LaporanMengajar::class);

        // Check if a report already exists for this session
        $existing = LaporanMengajar::where('ekstrakurikuler_session_id', $session->id)->first();
        if ($existing) {
            return redirect()->route('laporan-mengajar.show', $existing)
                ->with('info', 'Laporan untuk sesi ini sudah ada.');
        }

        $rombel = $session->rombel;
        $ekskul = $rombel->ekstrakurikuler;
        $sekolah = $ekskul->sekolah;

        $validated = [
            'user_id_instruktur' => Auth::id(),
            'sekolah_kodlan' => $sekolah->kodlan,
            'rombel' => $rombel->nama_rombel,
            'kategori_pengajaran' => $ekskul->kategori_program ?? 'Ekstrakurikuler',
            'pertemuan_ke' => $session->nomor_pertemuan ?? 1,
            'jadwal_mengajar' => $session->tanggal_terjadwal->format('Y-m-d'),
            'jam_mulai' => $session->jam_mulai_terjadwal ?? '08:00',
            'jam_selesai' => $session->jam_selesai_terjadwal ?? '09:30',
            'materi_pengajaran' => $session->topik_materi ?? '-',
            'status' => 'draft',
            'ekstrakurikuler_session_id' => $session->id,
            'jumlah_siswa_hadir' => 0,
            'jumlah_siswa_tidak_hadir' => 0,
            'jumlah_siswa_keluar' => 0,
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
        ];

        $laporan = LaporanMengajar::create($validated);

        // Update session status
        $session->update(['status' => 'berlangsung']);

        return redirect()->route('laporan-mengajar.absensi.create', $laporan)
            ->with('success', 'Laporan dari sesi ekstrakurikuler berhasil dibuat! Silakan isi absensi.');
    }

    public function destroy(LaporanMengajar $laporanMengajar)
    {
        $this->authorize('delete', $laporanMengajar);

        if ($laporanMengajar->foto_kegiatan) {
            Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
        }
        
        if ($laporanMengajar->foto_absensi_siswa) {
            Storage::disk('public')->delete($laporanMengajar->foto_absensi_siswa);
        }

        $laporanMengajar->delete();

        return redirect()->route('laporan-mengajar.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }    // Ideally I should update validationRules method as well.

    protected function validationRules(): array
    {
        return [
            'user_id_assisten' => 'nullable|exists:users,id',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'pertemuan_ke' => 'required|integer|min:1',
            'rombel' => 'required|string|max:255',
            'jadwal_mengajar' => [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        \Carbon\Carbon::createFromFormat('d/m/Y', $value);
                    } catch (\Exception $e) {
                        try {
                            \Carbon\Carbon::createFromFormat('Y-m-d', $value);
                        } catch (\Exception $e) {
                            $fail('Format tanggal harus dd/mm/yyyy atau yyyy-mm-dd');
                        }
                    }
                },
            ],
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'materi_pengajaran' => 'required|string',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Increased to 5MB and added GIF
            // Removed: foto_absensi_siswa, refleksi_siswa, refleksi_capaian, keaktifan, pemahaman_materi
        ];
    }
}
