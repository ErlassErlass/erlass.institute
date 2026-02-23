<?php

namespace App\Http\Controllers;

use App\Models\EkstrakurikulerRombel as Rombel;
use App\Services\SiswaImporterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RombelSiswaController extends Controller
{
    protected $importerService;

    public function __construct(SiswaImporterService $importerService)
    {
        $this->importerService = $importerService;
    }

    /**
     * Handle the import of students into a Rombel.
     */
    public function importToRombel(Request $request, Rombel $rombel)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            
            // Delegate to service
            $result = $this->importerService->importToRombel($file, $rombel);

            return back()->with('success', "Import berhasil! {$result['imported']} siswa ditambahkan, {$result['updated']} diperbarui.");
            
        } catch (\Exception $e) {
            Log::error("Error importing siswa for Rombel {$rombel->id}: " . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengimport data: ' . $e->getMessage()]);
        }
    }
}
