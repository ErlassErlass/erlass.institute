<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerSession;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalHarianController extends Controller
{
    /**
     * Display daily schedule.
     */
    public function index(Request $request)
    {
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::today();
        
        $sessions = EkstrakurikulerSession::with([
                'ekstrakurikuler.sekolah', // Eager load nested relationship
                'rombel',
                'instruktur',
                'asisten'
            ])
            ->whereDate('tanggal_terjadwal', $date)
            ->orderBy('jam_mulai_terjadwal')
            ->get();

        return view('jadwal.harian', compact('sessions', 'date'));
    }
}
