<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\EkstrakurikulerSession;
use App\Exports\LaporanMengajarExport;
use App\Http\Requests\StoreLaporanMengajarRequest;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporanMengajarController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(LaporanMengajar::class, 'laporan_mengajar');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $laporanQuery = LaporanMengajar::with('instruktur', 'sekolah', 'asisten');

        // Filter by user role
        if (!in_array($user->role, ['admin', 'admin_erlass'])) {
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
            $searchTerm = '%' . $request->search . '%';
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
            now()->endOfWeek()
        ])->count();
        $laporanBulanIni = $statsQuery->whereBetween('jadwal_mengajar', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->count();

        $totalInstruktur = User::whereIn('role', ['instruktur', 'admin'])
            ->when(!in_array($user->role, ['admin', 'admin_erlass']), function ($query) use ($user) {
                $query->where('id', $user->id);
            })
            ->count();

        // Get paginated results
        $laporan = $laporanQuery->latest()->paginate(10);
        $instructors = User::whereIn('role', ['instruktur', 'admin'])->orderBy('nama_lengkap')->get();

        return view('laporan-mengajar.index', compact(
            'laporan',
            'instructors',
            'totalLaporan',
            'laporanMingguIni',
            'laporanBulanIni',
            'totalInstruktur'
        ));
    }

    private function getKategoriList(): array
{
    return [
        'Coding Scratch', 'Coding Pictoblox', 'English Course',
        'Microbit:Learning Kit', 'Robotic Explorer', 'Robotik Jimu'
    ];
}


    public function export(Request $request, $format)
    {
        // Validate the format
        if (!in_array($format, ['excel', 'pdf'])) {
            return back()->with('error', 'Format ekspor tidak valid');
        }

        $user = Auth::user();
        $query = LaporanMengajar::with('instruktur', 'sekolah', 'asisten');

        if (!in_array($user->role, ['admin', 'admin_erlass'])) {
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

        $sekolahs = Sekolah::where('namasekolah', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('kodlan', 'LIKE', '%' . $searchTerm . '%')
            ->orderBy('namasekolah', 'asc')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $sekolahs->map(function ($sekolah) {
                return [
                    'id' => $sekolah->kodlan,
                    'text' => $sekolah->namasekolah . ' (' . $sekolah->kodlan . ') - ' . $sekolah->kec . ', ' . $sekolah->kotkab
                ];
            }),
            'pagination' => ['more' => false]
        ]);
    }

    public function create()
    {
        $instructors = User::where('id', '!=', auth()->id())->get();
        $selectedSekolah = null;

        if (old('kodlan')) {
            $selectedSekolah = Sekolah::find(old('kodlan'));
        }

        $kategori = $this->getKategoriList();


        return view('laporan-mengajar.create', compact('instructors', 'selectedSekolah','kategori'));
    }

    public function store(StoreLaporanMengajarRequest $request)
    {
        $validated = $request->validated();

        if (!isset($validated['kategori_pengajaran'])) {
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

        // Handle file uploads
        if ($request->hasFile('foto_kegiatan')) {
            $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
        }

        if ($request->hasFile('foto_absensi_siswa')) {
            $validated['foto_absensi_siswa'] = $request->file('foto_absensi_siswa')->store('laporan_mengajar_absensi', 'public');
        }

        // Create the report
        $laporan = LaporanMengajar::create($validated);

        // ✅ PENTING: Redirect ke halaman input absensi untuk laporan yang baru dibuat
        return redirect()->route('laporan-mengajar.absensi.create', $laporan)
            ->with('success', 'Laporan dasar berhasil dibuat! Sekarang, silakan isi absensi.');
    }

    public function show(LaporanMengajar $laporanMengajar)
    {
        $absensi = $laporanMengajar->absensis()->get();

        $jumlah_hadir = $absensi->where('hadir', 1)->count();
        $jumlah_tidak_hadir = $absensi->where('hadir', 0)->count();
        $jumlah_keluar = $absensi->where('hadir', 2)->count(); // jika ada 'keluar' sebagai opsi (pastikan kolom ini ada)

        // Cek apakah ini laporan dari ekstrakurikuler
        $isEkstrakurikuler = $laporanMengajar->isFromEkstrakurikuler();
        $ekstrakurikulerSession = null;
        $ekstrakurikulerData = null;

        if ($isEkstrakurikuler) {
            $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;
            $ekstrakurikulerData = $laporanMengajar->getEkstrakurikulerData();
        }

        return view('laporan-mengajar.show', compact(
            'laporanMengajar', 
            'jumlah_hadir', 
            'jumlah_tidak_hadir', 
            'jumlah_keluar',
            'isEkstrakurikuler',
            'ekstrakurikulerSession',
            'ekstrakurikulerData'
        ));
    }


    public function edit(LaporanMengajar $laporanMengajar)
    {
        
    $instructors = User::where('id', '!=', $laporanMengajar->user_id_instruktur)->get();
    
    // Load relasi sekolah agar bisa digunakan di view untuk menampilkan nama sekolah
    $laporanMengajar->load('sekolah');

    // ✅ SIAPKAN FORMAT TANGGAL DI SINI
    // Ambil tanggal dari database (format Y-m-d) dan ubah ke format tampilan (d/m/Y)
    try {
        // Kita buat properti baru agar tidak mengubah data asli di model
        $laporanMengajar->jadwal_mengajar_formatted = \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->format('d/m/Y');
    } catch (\Exception $e) {
        // Jika karena suatu hal tanggalnya tidak valid, kosongkan saja
        $laporanMengajar->jadwal_mengajar_formatted = '';
    }
    $kategori = $this->getKategoriList();


    return view('laporan-mengajar.edit', compact('laporanMengajar', 'instructors','kategori'));
    }

    public function update(Request $request, LaporanMengajar $laporanMengajar)
    {
        // ✅ Melewatkan $laporanMengajar ke validationRules
        $validated = $request->validate($this->validationRules($laporanMengajar));

        // Format tanggal dari dd/mm/yyyy ke format database Y-m-d
        $validated['jadwal_mengajar'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['jadwal_mengajar'])->format('Y-m-d');

        // Set status jika ada input 'draft' (untuk fitur "Simpan Draft")
        $validated['status'] = $request->has('draft') ? 'draft' : 'submitted';

        // Logika upload dan hapus file Anda sudah sangat bagus!
        if ($request->hasFile('foto_kegiatan')) {
            if ($laporanMengajar->foto_kegiatan) {
                Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
            }
            $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
        } elseif ($request->has('hapus_foto_kegiatan')) {
            if ($laporanMengajar->foto_kegiatan) {
                Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
            }
            $validated['foto_kegiatan'] = null;
        }

        if ($request->hasFile('foto_absensi_siswa')) {
            if ($laporanMengajar->foto_absensi_siswa) {
                Storage::disk('public')->delete($laporanMengajar->foto_absensi_siswa);
            }
            $validated['foto_absensi_siswa'] = $request->file('foto_absensi_siswa')->store('laporan_mengajar_absensi', 'public');
        } elseif ($request->has('hapus_foto_absensi')) {
            if ($laporanMengajar->foto_absensi_siswa) {
                Storage::disk('public')->delete($laporanMengajar->foto_absensi_siswa);
            }
            $validated['foto_absensi_siswa'] = null;
        }

        // Update data laporan
        $laporanMengajar->update($validated);

        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil diperbarui!');
    }



    public function destroy(LaporanMengajar $laporanMengajar)
    {
        // Cek apakah ini laporan dari ekstrakurikuler
        $isEkstrakurikuler = $laporanMengajar->isFromEkstrakurikuler();
        $ekstrakurikulerSession = null;

        if ($isEkstrakurikuler) {
            $ekstrakurikulerSession = $laporanMengajar->ekstrakurikulerSession;
        }

        // Delete associated files
        if ($laporanMengajar->foto_kegiatan) {
            Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
        }
        if ($laporanMengajar->foto_absensi_siswa) {
            Storage::disk('public')->delete($laporanMengajar->foto_absensi_siswa);
        }

        $laporanMengajar->delete();

        // Update status ekstrakurikuler session jika perlu
        if ($ekstrakurikulerSession) {
            $ekstrakurikulerSession->laporan_mengajar_id = null;
            $ekstrakurikulerSession->save();
        }

        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil dihapus!');
    }

    /**
     * Membuat laporan mengajar dari ekstrakurikuler session.
     */
    public function createFromEkstrakurikuler(EkstrakurikulerSession $session)
    {
        $this->authorize('create', LaporanMengajar::class);

        // Validasi session
        if ($session->status !== 'selesai') {
            return redirect()->back()->with('error', 'Session ekstrakurikuler harus selesai terlebih dahulu.');
        }

        if ($session->laporan_mengajar_id) {
            return redirect()->route('laporan-mengajar.show', $session->laporan_mengajar_id)
                ->with('info', 'Laporan mengajar untuk session ini sudah ada.');
        }

        // Auto-create laporan mengajar
        $laporan = $session->autoCreateLaporanMengajar();

        if (!$laporan) {
            return redirect()->back()->with('error', 'Gagal membuat laporan mengajar.');
        }

        return redirect()->route('laporan-mengajar.show', $laporan)
            ->with('success', 'Laporan mengajar berhasil dibuat dari session ekstrakurikuler.');
    }

    /**
     * Menampilkan dashboard khusus untuk ekstrakurikuler.
     */
    public function ekstrakurikulerDashboard(Request $request)
    {
        $user = Auth::user();
        
        // Query laporan ekstrakurikuler
        $query = LaporanMengajar::with(['instruktur', 'sekolah', 'ekstrakurikulerSession'])
            ->ekstrakurikuler();

        // Filter by user role
        if (!in_array($user->role, ['admin', 'admin_erlass'])) {
            $query->where('user_id_instruktur', $user->id);
        }

        // Filter by program
        if ($request->filled('program')) {
            $query->whereHas('ekstrakurikulerSession', function ($q) use ($request) {
                $q->whereHas('ekstrakurikuler', function ($q2) use ($request) {
                    $q2->where('nama_program', 'like', '%' . $request->program . '%');
                });
            });
        }

        // Date filter
        if ($request->filled('bulan')) {
            $query->whereMonth('jadwal_mengajar', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('jadwal_mengajar', $request->tahun);
        }

        $laporanEkstrakurikuler = $query->orderBy('jadwal_mengajar', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total_laporan' => $query->count(),
            'bulan_ini' => $query->whereMonth('jadwal_mengajar', now()->month)->count(),
            'minggu_ini' => $query->whereBetween('jadwal_mengajar', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'hari_ini' => $query->whereDate('jadwal_mengajar', today())->count(),
        ];

        return view('laporan-mengajar.ekstrakurikuler-dashboard', compact('laporanEkstrakurikuler', 'stats'));
    }

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
                }
            ],
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'materi_pengajaran' => 'required|string',
            'foto_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_absensi_siswa' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'refleksi_siswa' => 'required|string',
            'refleksi_capaian' => 'required|string',
            'keaktifan' => 'required|in:sangat_pasif,pasif,aktif,sangat_aktif',
            'pemahaman_materi' => 'required|in:belum_paham,sedikit_paham,paham,sangat_paham',
        ];
    }
}
