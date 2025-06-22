<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Check if LaporanMengajar model exists
        if (class_exists('App\Models\LaporanMengajar')) {
            $today = Carbon::today();
            $laporan_hari_ini = \App\Models\LaporanMengajar::whereDate('created_at', $today)->count();
            $recent_laporan = \App\Models\LaporanMengajar::with(['sekolah', 'instruktur'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
            
            $total_laporan_instruktur = auth()->user()->role === 'instruktur' 
                ? \App\Models\LaporanMengajar::where('user_id_instruktur', auth()->id())->count()
                : null;
        } else {
            // Fallback values if LaporanMengajar doesn't exist
            $laporan_hari_ini = 0;
            $recent_laporan = collect();
            $total_laporan_instruktur = null;
        }

        $data = [
            'total_sekolah' => Sekolah::count(),
            'total_siswa' => Siswa::count(),
            'laporan_hari_ini' => $laporan_hari_ini,
            'total_pengguna' => User::count(),
            'total_laporan_instruktur' => $total_laporan_instruktur,
            'recent_laporan' => $recent_laporan ?? collect(),
            'sekolah_distribution' => Sekolah::withCount('siswa')
                ->orderBy('siswa_count', 'desc')
                ->take(5)
                ->get()
        ];

        return view('dashboard', $data);
    }
}