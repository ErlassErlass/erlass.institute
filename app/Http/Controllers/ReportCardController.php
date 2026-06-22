<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ReportCardController extends Controller
{
    /**
     * Download the report card PDF.
     */
    public function download(ReportCard $reportCard)
    {
        if (!Auth::check()) {
            abort(403);
        }

        $user = Auth::user();
        if ($user->role === 'instruktur') {
            $rombel = $reportCard->ekstrakurikulerRombel;
            if (!$rombel || ($rombel->user_id_instruktur !== $user->id && $rombel->user_id_asisten !== $user->id)) {
                abort(403, 'Akses ditolak.');
            }
        } elseif (!$user->hasAdminAccess()) {
            abort(403, 'Akses ditolak.');
        }

        if ($reportCard->file_path && Storage::disk('public')->exists($reportCard->file_path)) {
            $fileName = basename($reportCard->file_path);
            return Storage::disk('public')->download($reportCard->file_path, $fileName);
        }

        return redirect()->back()->with('error', 'Berkas rapor tidak ditemukan di server.');
    }
}
