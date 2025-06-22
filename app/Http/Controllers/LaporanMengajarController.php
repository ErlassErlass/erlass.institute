<?php

namespace App\Http\Controllers;

use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use App\Exports\LaporanMengajarExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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

    // Date range filter with improved validation
if ($request->filled('date_range')) {
    try {
        $dateRange = str_replace(' - ', ' to ', $request->date_range);
        $dates = array_map('trim', explode(' to ', $dateRange));
        
        $parseDate = function($dateString) {
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
        $laporanQuery->where(function($query) use ($searchTerm) {
            $query->whereHas('sekolah', function($q) use ($searchTerm) {
                $q->where('namasekolah', 'LIKE', $searchTerm);
            })->orWhere('rombel', 'LIKE', $searchTerm);
        });
    }

    // Get statistics
    $statsQuery = clone $laporanQuery;
    
    $totalLaporan = $statsQuery->count();
    $laporanMingguIni = $statsQuery->whereBetween('jadwal_mengajar', [
        now()->startOfWeek(), now()->endOfWeek()
    ])->count();
    $laporanBulanIni = $statsQuery->whereBetween('jadwal_mengajar', [
        now()->startOfMonth(), now()->endOfMonth()
    ])->count();
    
    $totalInstruktur = User::whereIn('role', ['instruktur', 'admin'])
        ->when(!in_array($user->role, ['admin', 'admin_erlass']), function($query) use ($user) {
            $query->where('id', $user->id);
        })
        ->count();

    // Get paginated results
    $laporan = $laporanQuery->latest()->paginate(10);
    $instructors = User::whereIn('role', ['instruktur', 'admin'])->orderBy('nama_lengkap')->get();

    return view('laporan-mengajar.index', compact(
        'laporan', 'instructors', 'totalLaporan', 
        'laporanMingguIni', 'laporanBulanIni', 'totalInstruktur'
    ));
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
            $parseDate = function($dateString) {
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

        $sekolahs = Sekolah::where('namasekolah', 'LIKE', '%'.$searchTerm.'%')
                          ->orWhere('kodlan', 'LIKE', '%'.$searchTerm.'%')
                          ->orderBy('namasekolah', 'asc')
                          ->limit(20)
                          ->get();

        return response()->json([
            'results' => $sekolahs->map(function ($sekolah) {
                return [
                    'id' => $sekolah->kodlan,
                    'text' => $sekolah->namasekolah.' ('.$sekolah->kodlan.') - '.$sekolah->kec.', '.$sekolah->kotkab
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
        
        return view('laporan-mengajar.create', compact('instructors', 'selectedSekolah'));
    }

public function store(Request $request)
{
    $validated = $request->validate($this->validationRules());

    // Format the date correctly for database storage
    $validated['jadwal_mengajar'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['jadwal_mengajar'])->format('Y-m-d');
    
    // Add the instructor ID
    $validated['user_id_instruktur'] = Auth::id();
    
    // Get complete school data
    $sekolah = Sekolah::findOrFail($validated['kodlan']);
    $validated['sekolah_nama'] = $sekolah->namasekolah;
    $validated['sekolah_kota'] = $sekolah->kotkab;
    $validated['sekolah_kecamatan'] = $sekolah->kec;
    $validated['sekolah_provinsi'] = $sekolah->provinsi;
    
    // Handle file uploads
    if ($request->hasFile('foto_kegiatan')) {
        $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
    }
    
    if ($request->hasFile('foto_absensi_siswa')) {
        $validated['foto_absensi_siswa'] = $request->file('foto_absensi_siswa')->store('laporan_mengajar_absensi', 'public');
    }
    
    // Create the report
    $laporan = LaporanMengajar::create($validated);
    
    return redirect()->route('laporan-mengajar.show', $laporan)
        ->with('success', 'Laporan berhasil disimpan! Silahkan input data absensi.');
}

public function show(LaporanMengajar $laporanMengajar)
{
    $absensi = $laporanMengajar->absensis()->get();
    
    $jumlah_hadir = $absensi->where('hadir', 1)->count();
    $jumlah_tidak_hadir = $absensi->where('hadir', 0)->count();
    $jumlah_keluar = $absensi->where('hadir', 2)->count(); // jika ada 'keluar' sebagai opsi (pastikan kolom ini ada)

    return view('laporan-mengajar.show', compact('laporanMengajar', 'jumlah_hadir', 'jumlah_tidak_hadir', 'jumlah_keluar'));
}


    public function edit(LaporanMengajar $laporanMengajar)
    {
        $instructors = User::where('id', '!=', $laporanMengajar->user_id_instruktur)->get();
        
        // Format tanggal untuk tampilan
        $laporanMengajar->jadwal_mengajar = \Carbon\Carbon::parse($laporanMengajar->jadwal_mengajar)->format('d/m/Y');
        
        return view('laporan-mengajar.edit', compact('laporanMengajar', 'instructors'));
    }

public function update(Request $request, LaporanMengajar $laporanMengajar)
{
    $validated = $request->validate($this->validationRules());

    // Format date
    $validated['jadwal_mengajar'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['jadwal_mengajar'])->format('Y-m-d');

    // Get complete school data if kodlan changed
    if ($validated['kodlan'] != $laporanMengajar->kodlan) {
        $sekolah = Sekolah::findOrFail($validated['kodlan']);
        $validated['sekolah_nama'] = $sekolah->namasekolah;
        $validated['sekolah_kota'] = $sekolah->kotkab;
        $validated['sekolah_kecamatan'] = $sekolah->kec;
        $validated['sekolah_provinsi'] = $sekolah->provinsi;
    }

    // Handle file uploads and deletions
    if ($request->hasFile('foto_kegiatan')) {
        // Delete old file if exists
        if ($laporanMengajar->foto_kegiatan) {
            Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
        }
        $validated['foto_kegiatan'] = $request->file('foto_kegiatan')->store('laporan_mengajar', 'public');
    } elseif ($request->has('hapus_foto_kegiatan')) {
        // Delete file if checkbox is checked
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

    $laporanMengajar->update($validated);
    return redirect()->route('laporan-mengajar.show', $laporanMengajar)
        ->with('success', 'Laporan berhasil diperbarui!');
}
    public function destroy(LaporanMengajar $laporanMengajar)
    {
        // Delete associated files
        if ($laporanMengajar->foto_kegiatan) {
            Storage::disk('public')->delete($laporanMengajar->foto_kegiatan);
        }
        if ($laporanMengajar->foto_absensi_siswa) {
            Storage::disk('public')->delete($laporanMengajar->foto_absensi_siswa);
        }
        
        $laporanMengajar->delete();
        return redirect()->route('laporan-mengajar.index')->with('success', 'Laporan berhasil dihapus!');
    }

    protected function validationRules(): array
    {
        return [
            'user_id_assisten' => 'nullable|exists:users,id',
            'kodlan' => 'required|exists:sekolah,kodlan',
            'pertemuan_ke' => 'required|integer|min:1',
            'rombel' => 'required|string|max:255',
            'kategori_pengajaran' => 'required|in:Reguler,Remedial,Pengayaan',
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