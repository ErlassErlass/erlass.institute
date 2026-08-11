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
use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Ekstrakurikuler;

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


    /**
     * Shared filter query builder for index, statistics, and exports.
     */
    private function getFilteredLaporanQuery(Request $request)
    {
        $user = Auth::user();
        $laporanQuery = LaporanMengajar::with('instruktur', 'sekolah', 'asisten', 'ekstrakurikulerSession.rombel.ekstrakurikuler');

        // Filter by user role
        if (! in_array($user->role, ['admin', 'admin_sistem', 'webmaster'])) {
            $laporanQuery->where('user_id_instruktur', $user->id);
        }

        // Filter by instructor
        if ($request->filled('instruktur_id')) {
            $laporanQuery->where('user_id_instruktur', $request->instruktur_id);
        }

        // Filter by category (support both exact kategori_pengajaran and ekskul category)
        if ($request->filled('kategori')) {
            $kat = $request->kategori;
            if ($kat === 'ekstrakurikuler') {
                $laporanQuery->ekstrakurikuler();
            } elseif ($kat === 'regular') {
                $laporanQuery->regular();
            } else {
                $laporanQuery->where(function ($query) use ($kat) {
                    $query->where('kategori_pengajaran', 'LIKE', "%{$kat}%")
                        ->orWhereHas('ekstrakurikulerSession.rombel.ekstrakurikuler', function ($q) use ($kat) {
                            $q->where('kategori_program', 'LIKE', "%{$kat}%");
                        });
                });
            }
        }

        // Date range filter with improved validation
        if ($request->filled('date_range')) {
            try {
                $dateRange = str_replace(' - ', ' to ', $request->date_range);
                $dates = array_map('trim', explode(' to ', $dateRange));

                $parseDate = function ($dateString) {
                    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateString)) {
                        return \Carbon\Carbon::createFromFormat('d/m/Y', $dateString);
                    }
                    return \Carbon\Carbon::parse($dateString);
                };

                if (count($dates) === 1) {
                    $date = $parseDate($dates[0]);
                    $laporanQuery->whereDate('jadwal_mengajar', $date);
                } elseif (count($dates) === 2) {
                    $startDate = $parseDate($dates[0])->startOfDay();
                    $endDate = $parseDate($dates[1])->endOfDay();
                    $laporanQuery->whereBetween('jadwal_mengajar', [$startDate, $endDate]);
                }
            } catch (\Exception $e) {
                // Silently ignore invalid dates
            }
        }

        // Search by school name, manual school, instructor name, topic, or rombel
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $laporanQuery->where(function ($query) use ($searchTerm) {
                $query->whereHas('sekolah', function ($q) use ($searchTerm) {
                    $q->where('namasekolah', 'LIKE', $searchTerm);
                })
                ->orWhere('sekolah_nama', 'LIKE', $searchTerm)
                ->orWhere('rombel', 'LIKE', $searchTerm)
                ->orWhere('materi_pengajaran', 'LIKE', $searchTerm)
                ->orWhere('refleksi_siswa', 'LIKE', $searchTerm)
                ->orWhere('refleksi_capaian', 'LIKE', $searchTerm)
                ->orWhereHas('instruktur', function ($q) use ($searchTerm) {
                    $q->where('nama_lengkap', 'LIKE', $searchTerm);
                });
            });
        }

        return $laporanQuery;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $laporanQuery = $this->getFilteredLaporanQuery($request);

        // Get statistics
        $totalLaporan = (clone $laporanQuery)->count();
        $laporanMingguIni = (clone $laporanQuery)->whereBetween('jadwal_mengajar', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();
        $laporanBulanIni = (clone $laporanQuery)->whereBetween('jadwal_mengajar', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->count();

        $totalInstruktur = User::where('role', 'instruktur')
            ->where('verification_status', 'approved')
            ->when(! in_array($user->role, ['admin', 'admin_sistem', 'webmaster']), function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->count();

        // Support per_page (25, 50, 100, all) and preserve query string
        $perPage = $request->input('per_page', 25);
        if ($perPage === 'all' || $perPage == -1) {
            $totalCount = (clone $laporanQuery)->count();
            $laporan = $laporanQuery->latest()->paginate(max($totalCount, 1000))->withQueryString();
        } else {
            $laporan = $laporanQuery->latest()->paginate((int) $perPage)->withQueryString();
        }
        
        // Cache dropdown list of instructors
        $instructors = \Illuminate\Support\Facades\Cache::remember('instructors_list_approved', 300, function () {
            return User::where('role', 'instruktur')
                ->where('verification_status', 'approved')
                ->orderBy('nama_lengkap')
                ->get();
        });

        // Cache category list (combine LaporanMengajar and Ekstrakurikuler categories)
        $kategoriList = \Illuminate\Support\Facades\Cache::remember('combined_kategori_list', 300, function () {
            $laporanCats = LaporanMengajar::distinct()->whereNotNull('kategori_pengajaran')->pluck('kategori_pengajaran')->toArray();
            $ekskulCats = \App\Models\Ekstrakurikuler::distinct()->whereNotNull('kategori_program')->pluck('kategori_program')->toArray();
            return collect(array_merge($laporanCats, $ekskulCats))->unique()->filter()->sort()->values()->toArray();
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
        $categories = [
            'Backup Pertemuan',
            'Free Trial Class',
            'Inkul LMS Coding Scratch',
            'Inkul LMS LKPD Informatika SD',
            'Inkul LMS LKPD Informatika SMA',
            'Inkul LMS LKPD Informatika SMP',
            'Inkul LMS Koding KA SD',
            'Pameran',
            'Pendampingan Lomba',
            'Sosialisasi bersama Sales',
        ];
        sort($categories);
        return $categories;
    }

    public function export(Request $request, $format)
    {
        // Validate the format
        if (! in_array($format, ['excel', 'pdf'])) {
            return back()->with('error', 'Format ekspor tidak valid');
        }

        $query = $this->getFilteredLaporanQuery($request);
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
        $instructors = User::where('role', 'instruktur')
            ->where('verification_status', 'approved')
            ->orderBy('nama_lengkap')
            ->get();
            
        $selectedSekolah = null;

        if (old('sekolah_kodlan')) {
            $selectedSekolah = Sekolah::find(old('sekolah_kodlan'));
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
                $inputDate = \Carbon\Carbon::parse($validated['jadwal_mengajar'])->startOfDay();
                
                // Jika input date adalah masa lalu lebih dari 1 hari dari sekarang
                if ($inputDate->copy()->addDay()->endOfDay()->isBefore(now())) {
                    $formattedDbDate = $inputDate->format('Y-m-d');
                    
                    // Cek apakah ada permohonan Ad-Hoc yang sudah disetujui untuk tanggal ini
                    $hasApprovedRequest = \App\Models\LateReportRequest::where('user_id', Auth::id())
                        ->whereNull('session_id')
                        ->where('adhoc_date', $formattedDbDate)
                        ->where('status', 'approved')
                        ->exists();

                    if (!$hasApprovedRequest) {
                         return redirect()->back()
                            ->withInput()
                            ->with('error', 'Tanggal kegiatan (' . $inputDate->format('d/m/Y') . ') telah melewati batas H+1. Silakan kirimkan permohonan buka akses Ad-Hoc.');
                    }
                }
             } catch (\Exception $e) {
                 // Date parsing validasi sudah di handle request validator
             }
        }

        if (! isset($validated['kategori_pengajaran'])) {
            $validated['kategori_pengajaran'] = $request->kategori_pengajaran;
        }

        // Format the date correctly for database storage
        $validated['jadwal_mengajar'] = \Carbon\Carbon::parse($validated['jadwal_mengajar'])->format('Y-m-d');

        // Add the instructor ID
        $validated['user_id_instruktur'] = Auth::id();

        // Get complete school data
        $validated['status'] = $request->has('draft') ? 'draft' : 'submitted';

        $validated['jumlah_siswa_hadir'] = (int) $request->input('jumlah_siswa_hadir', 0);
        $validated['jumlah_siswa_tidak_hadir'] = (int) $request->input('jumlah_siswa_tidak_hadir', 0);
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

        // For Ad-Hoc / Special event reports, skip individual student attendance and redirect to show.
        if ($laporan->isAdHoc()) {
            return redirect()->route('laporan-mengajar.show', $laporan)
                ->with('success', 'Laporan mengajar Ad-Hoc / Khusus berhasil dibuat!');
        }

        // Smart Redirect: If pre-registered students exist in DB for this school & rombel, redirect to absensi.create.
        // Otherwise (for standalone text rombel), redirect directly to show.
        $hasRegisteredStudents = \App\Models\Siswa::where('sekolah_kodlan', $validated['sekolah_kodlan'])
            ->where('rombel', $validated['rombel'])
            ->exists();

        if ($hasRegisteredStudents) {
            return redirect()->route('laporan-mengajar.absensi.create', $laporan)
                ->with('success', 'Laporan dasar berhasil dibuat! Silakan tandai absensi siswa.');
        }

        return redirect()->route('laporan-mengajar.show', $laporan)
            ->with('success', 'Laporan mengajar Ad-Hoc / Free Trial Class berhasil dibuat!');
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

        $availableSessions = collect();
        if ($ekstrakurikulerSession && $ekstrakurikulerSession->ekstrakurikuler_rombel_id) {
            $availableSessions = \App\Models\EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $ekstrakurikulerSession->ekstrakurikuler_rombel_id)
                ->orderBy('nomor_pertemuan')
                ->get();
        }

        return view('laporan-mengajar.show', compact('laporanMengajar', 'isEkstrakurikuler', 'ekstrakurikulerSession', 'ekstrakurikulerData', 'availableSessions'));
    }

    public function edit(LaporanMengajar $laporanMengajar)
    {
        $this->authorize('update', $laporanMengajar);
        
        $instructors = User::where('role', 'instruktur')
            ->where('verification_status', 'approved')
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

        if ($session->isAdHoc() || $laporan->isAdHoc()) {
            return redirect()->route('laporan-mengajar.show', $laporan)
                ->with('success', 'Laporan dari sesi Ad-Hoc / Khusus berhasil dibuat!');
        }

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

    protected function validationRules($request = null): array
    {
        $request = $request ?? request();
        $allowedKategori = array_unique(array_merge(
            [
                'Backup Pertemuan',
                'Free Trial Class',
                'Trial Class',
                'Inkul Coding Scratch',
                'Inkul LKPD Informatika SD',
                'Inkul LKPD Informatika SMA',
                'Inkul LKPD Informatika SMP',
                'Inkul LMS Koding KA SD',
                'Pameran',
                'Pendampingan Lomba',
                'Sosialisasi bersama Sales',
                'ekstrakurikuler',
                'Ekstrakurikuler',
                'Reguler',
            ],
            \App\Models\RefMateri::distinct()->pluck('kategori')->toArray()
        ));

        return [
            'total_pertemuan' => 'nullable|integer|min:1|max:200',
            'user_id_assisten' => [
                'nullable',
                'integer',
                \Illuminate\Validation\Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', 'instruktur');
                }),
            ],
            'sekolah_kodlan' => 'required|string|exists:sekolah,kodlan',
            'pertemuan_ke' => 'required|integer|min:1|max:100',
            'rombel' => 'required|string|max:50',
            'kategori_pengajaran' => [
                'sometimes',
                'required',
                'string',
                \Illuminate\Validation\Rule::in($allowedKategori),
            ],
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
            'jam_selesai' => [
                'required',
                'date_format:H:i',
                'after:jam_mulai',
                function ($attribute, $value, $fail) use ($request) {
                    // Validasi durasi mengajar per sesi (minimal 60 menit, maksimal 90 menit)
                    if ($request->jam_mulai && $value) {
                        try {
                            $start = \Carbon\Carbon::createFromFormat('H:i', $request->jam_mulai);
                            $end = \Carbon\Carbon::createFromFormat('H:i', $value);
                            if ($end < $start) $end->addDay();
                            $diff = $start->diffInMinutes($end);
                            if ($diff < 60) $fail('Durasi mengajar minimal 60 menit (1 jam).');
                            if ($diff > 180) $fail('Durasi mengajar maksimal 180 menit (3 jam).');
                        } catch (\Throwable $e) {}
                    }
                }
            ],
            'materi_pengajaran' => [
                'required',
                'string',
                'max:1000',
                function ($attribute, $value, $fail) use ($request) {
                    $kategori = $request->input('kategori_pengajaran');
                    if ($kategori && \App\Models\RefMateri::where('kategori', $kategori)->exists()) {
                        $exists = \App\Models\RefMateri::where('kategori', $kategori)
                            ->where('materi', $value)
                            ->exists();
                        if (! $exists) {
                            $fail('Materi pengajaran yang dipilih tidak valid.');
                        }
                    }
                },
            ],
            'foto_kegiatan' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ];
    }

    /**
     * Relocate a LaporanMengajar from one EkstrakurikulerSession to another target session in the same Rombel.
     */
    public function relocateReport(Request $request, LaporanMengajar $laporan)
    {
        if (!in_array(auth()->user()->role, ['webmaster', 'admin_sistem', 'admin'])) {
            abort(403, 'Akses ditolak. Fitur relokasi laporan hanya dapat dilakukan oleh Admin.');
        }

        $request->validate([
            'target_session_id' => 'required|exists:ekstrakurikuler_session,id',
            'alasan_relokasi' => 'nullable|string|max:500',
        ]);

        $targetSession = \App\Models\EkstrakurikulerSession::with('rombel')->findOrFail($request->input('target_session_id'));
        $currentSession = \App\Models\EkstrakurikulerSession::find($laporan->ekstrakurikuler_session_id);

        if ($currentSession && $currentSession->ekstrakurikuler_rombel_id !== $targetSession->ekstrakurikuler_rombel_id) {
            return redirect()->back()->with('error', 'Relokasi gagal: Sesi target harus berada pada Rombel yang sama.');
        }

        if ($targetSession->laporanMengajar()->exists() && $targetSession->laporanMengajar->id != $laporan->id) {
            return redirect()->back()->with('error', 'Relokasi gagal: Sesi target (Pertemuan ' . $targetSession->nomor_pertemuan . ') sudah memiliki laporan lain.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($laporan, $currentSession, $targetSession, $request) {
            $oldPertemuan = $laporan->pertemuan_ke;
            $newPertemuan = $targetSession->nomor_pertemuan;

            // 1. Reset current (old) session if exists
            if ($currentSession && $currentSession->id !== $targetSession->id) {
                $currentSession->update([
                    'status' => \App\Models\EkstrakurikulerSession::STATUS_TERJADWAL,
                ]);
            }

            // 2. Update status of new target session
            $targetSession->update([
                'status' => \App\Models\EkstrakurikulerSession::STATUS_SELESAI,
            ]);

            // 3. Update LaporanMengajar attributes
            $laporan->update([
                'ekstrakurikuler_session_id' => $targetSession->id,
                'pertemuan_ke' => $newPertemuan,
                'jadwal_mengajar' => $targetSession->tanggal_terjadwal ? $targetSession->tanggal_terjadwal->format('Y-m-d') : $laporan->jadwal_mengajar,
            ]);

            // 4. Log Activity
            if (class_exists('\App\Models\ActivityLog')) {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'UPDATE',
                    'module' => 'Laporan Mengajar',
                    'description' => "Memindahkan Laporan #{$laporan->id} dari Pertemuan {$oldPertemuan} ke Pertemuan {$newPertemuan} (Rombel ID #{$targetSession->ekstrakurikuler_rombel_id}). Alasan: " . ($request->input('alasan_relokasi') ?? '-'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        });

        return redirect()->back()->with('success', 'Laporan Mengajar berhasil dipindahkan ke Pertemuan ' . $targetSession->nomor_pertemuan . '!');
    }
}
