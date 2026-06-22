<?php

namespace App\Http\Controllers;

use App\Models\StudentPortfolio;
use App\Models\EkstrakurikulerRombel;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class StudentPortfolioController extends Controller
{
    /**
     * Display portfolio listing or search.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'instruktur') {
            $rombels = EkstrakurikulerRombel::where('user_id_instruktur', $user->id)
                ->orWhere('user_id_asisten', $user->id)
                ->with(['ekstrakurikuler.sekolah'])
                ->get();
        } else {
            $rombels = EkstrakurikulerRombel::with(['ekstrakurikuler.sekolah'])->get();
        }

        return view('student_portfolios.index', compact('rombels'));
    }

    /**
     * Display portfolios of students in a specific Rombel.
     */
    public function rombelIndex(EkstrakurikulerRombel $rombel)
    {
        $user = Auth::user();
        if ($user->role === 'instruktur') {
            if ($rombel->user_id_instruktur !== $user->id && $rombel->user_id_asisten !== $user->id) {
                abort(403, 'Akses ditolak.');
            }
        } elseif (!$user->hasAdminAccess()) {
            abort(403, 'Akses ditolak.');
        }

        $siswaList = $rombel->siswaAktif()->orderBy('nama_lengkap', 'asc')->get();
        
        $portfolios = StudentPortfolio::where('ekstrakurikuler_rombel_id', $rombel->id)
            ->with('siswa')
            ->latest()
            ->get();

        return view('student_portfolios.rombel', compact('rombel', 'siswaList', 'portfolios'));
    }

    /**
     * Store student portfolio entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'ekstrakurikuler_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            'judul' => 'required|string|max:255',
            'tipe_file' => 'required|string|in:sb3,hex,py,png,jpg,jpeg,pdf,mp4,link',
            'deskripsi' => 'nullable|string',
            'pertemuan_ke' => 'nullable|integer|between:1,32',
            'file_upload' => 'nullable|file|max:10240|mimes:sb3,hex,py,png,jpg,jpeg,pdf,mp4,zip,rar', // Prevents dynamic script RCE
            'url_eksternal' => 'nullable|url|max:255',
        ]);

        $rombel = EkstrakurikulerRombel::findOrFail($request->ekstrakurikuler_rombel_id);

        $user = Auth::user();
        if ($user->role === 'instruktur') {
            if ($rombel->user_id_instruktur !== $user->id && $rombel->user_id_asisten !== $user->id) {
                abort(403, 'Akses ditolak.');
            }
        } elseif (!$user->hasAdminAccess()) {
            abort(403, 'Akses ditolak.');
        }

        $filePath = null;
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = 'portfolio_' . $request->siswa_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads/portfolios', $fileName, 'public');
        }

        StudentPortfolio::create([
            'siswa_id' => $request->siswa_id,
            'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
            'ekstrakurikuler_rombel_id' => $rombel->id,
            'tipe_file' => $request->tipe_file,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'file_path' => $filePath,
            'url_eksternal' => $request->url_eksternal,
            'pertemuan_ke' => $request->pertemuan_ke,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Portofolio siswa berhasil diunggah.');
    }

    /**
     * Delete student portfolio entry.
     */
    public function destroy(StudentPortfolio $portfolio)
    {
        $user = Auth::user();
        if ($user->role === 'instruktur') {
            if ($portfolio->ekstrakurikulerRombel->user_id_instruktur !== $user->id && 
                $portfolio->ekstrakurikulerRombel->user_id_asisten !== $user->id) {
                abort(403, 'Akses ditolak.');
            }
        } elseif (!$user->hasAdminAccess()) {
            abort(403, 'Akses ditolak.');
        }

        if ($portfolio->file_path && Storage::disk('public')->exists($portfolio->file_path)) {
            Storage::disk('public')->delete($portfolio->file_path);
        }

        $portfolio->delete();

        return redirect()->back()->with('success', 'Portofolio siswa berhasil dihapus.');
    }
}
