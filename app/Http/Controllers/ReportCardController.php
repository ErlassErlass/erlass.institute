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

        if ($reportCard->file_path && Storage::disk('public')->exists($reportCard->file_path)) {
            $fileName = basename($reportCard->file_path);
            return Storage::disk('public')->download($reportCard->file_path, $fileName);
        }

        return redirect()->back()->with('error', 'Berkas rapor tidak ditemukan di server.');
    }
}
