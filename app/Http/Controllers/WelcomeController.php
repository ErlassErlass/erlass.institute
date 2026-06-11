<?php

namespace App\Http\Controllers;

class WelcomeController extends Controller
{
    public function index()
    {
        $liveSessions = \App\Models\EkstrakurikulerSession::with(['ekstrakurikuler.sekolah'])
            ->where('tanggal_terjadwal', now()->toDateString())
            ->where('status', '!=', 'dibatalkan')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('welcome', compact('liveSessions'));
    }
}
