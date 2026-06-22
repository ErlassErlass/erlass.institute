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
        $user = auth()->user();

        $query = EkstrakurikulerSession::with([
                'ekstrakurikuler.sekolah',
                'rombel',
                'instruktur',
                'asisten'
            ])
            ->whereDate('tanggal_terjadwal', $date)
            ->orderBy('jam_mulai_terjadwal');

        // Restrict to own sessions if not admin
        if (! $user->hasRole(['admin', 'admin_sistem', 'webmaster'])) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        }

        $sessions = $query->get();

        return view('jadwal.harian', compact('sessions', 'date'));
    }
}
