<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerSession;
use Illuminate\Support\Facades\Cache;

class WelcomeController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $liveSessions = Cache::remember('welcome_live_sessions_' . $today, 300, function () use ($today) {
            return EkstrakurikulerSession::with(['ekstrakurikuler.sekolah', 'rombel'])
                ->where('tanggal_terjadwal', $today)
                ->where('status', '!=', 'dibatalkan')
                ->limit(3)
                ->get();
        });

        return view('welcome', compact('liveSessions'));
    }
}
