<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\InstructorBroadcastNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class BroadcastController extends Controller
{
    public function create()
    {
        $instructorCount = User::where('role', 'instruktur')->whereNotNull('no_telephone')->count();
        return view('admin.broadcast.create', compact('instructorCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $instructors = User::where('role', 'instruktur')
            ->whereNotNull('no_telephone')
            ->get();

        if ($instructors->isEmpty()) {
            return back()->with('error', 'Tidak ada instruktur dengan nomor WhatsApp yang valid.');
        }

        /**
         * ANTI-BAN: Kirim satu per satu dengan jeda 5 detik antar pesan.
         * Menghindari burst sending yang dapat memicu rate-limit atau ban Fonnte/WA.
         *
         * Jika QUEUE_CONNECTION=redis/database aktif, jeda ini berjalan di background
         * sehingga request HTTP tidak tertahan.
         */
        $delaySeconds = 0;

        foreach ($instructors as $instructor) {
            $notification = (new InstructorBroadcastNotification($request->subject, $request->message))
                ->delay(now()->addSeconds($delaySeconds));

            $instructor->notify($notification);

            // Jeda 5 detik per penerima
            $delaySeconds += 5;
        }

        return redirect()->route('admin.broadcast.create')
            ->with('success', 'Pesan broadcast telah dijadwalkan ke ' . $instructors->count() . ' instruktur (dikirim bertahap).');
    }
}
