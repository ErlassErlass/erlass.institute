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

        // Send Notification
        Notification::send($instructors, new InstructorBroadcastNotification($request->subject, $request->message));

        return redirect()->route('admin.broadcast.create')
            ->with('success', 'Pesan broadcast telah dikirim ke ' . $instructors->count() . ' instruktur.');
    }
}
