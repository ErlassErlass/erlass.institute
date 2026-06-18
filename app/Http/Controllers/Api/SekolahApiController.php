<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahApiController extends Controller
{
    /**
     * Method #1: Mengambil Tipe Kota/Kabupaten berdasarkan Provinsi.
     */
    public function getKotkabTipe(Request $request)
    {
        $request->validate(['provinsi' => 'required|string']);

        $tipe = Sekolah::where('provinsi', $request->query('provinsi'))
            ->select('kotkab')->distinct()->pluck('kotkab');

        return response()->json($tipe);
    }

    /**
     * Method #2: Mengambil Nama Kota berdasarkan Provinsi dan Tipe Kotkab.
     */
    public function getKota(Request $request)
    {
        $request->validate([
            'provinsi' => 'required|string',
            'kotkab' => 'required|string', // Mengharapkan 'kotkab'
        ]);

        $kota = Sekolah::where('provinsi', $request->query('provinsi'))
            ->where('kotkab', $request->query('kotkab')) // Query berdasarkan 'kotkab'
            ->select('kota')->distinct()->pluck('kota');

        return response()->json($kota);
    }

    /**
     * Method #3: Mengambil Kecamatan berdasarkan Provinsi, Tipe Kotkab, dan Nama Kota.
     */
    public function getKecamatan(Request $request)
    {
        // ✅ DIPERBAIKI: Menggunakan 'kotkab' sebagai ganti 'kotkab_tipe'
        $request->validate([
            'provinsi' => 'required|string',
            'kotkab' => 'required|string',
            'kota' => 'required|string',
        ]);

        // ✅ DIPERBAIKI: Query berdasarkan 'kotkab'
        $kecamatan = Sekolah::where('provinsi', $request->query('provinsi'))
            ->where('kotkab', $request->query('kotkab'))
            ->where('kota', $request->query('kota'))
            ->select('kec')->distinct()->pluck('kec');

        return response()->json($kecamatan);
    }

    /**
     * Method #4: Mengambil Sekolah berdasarkan semua filter sebelumnya.
     */
    public function getSekolah(Request $request)
    {
        // ✅ DIPERBAIKI: Menggunakan 'kotkab' sebagai ganti 'kotkab_tipe'
        $request->validate([
            'provinsi' => 'required|string',
            'kotkab' => 'required|string',
            'kota' => 'required|string',
            'kecamatan' => 'required|string',
        ]);

        // ✅ DIPERBAIKI: Query berdasarkan 'kotkab' dan 'kec'
        $sekolah = Sekolah::where('provinsi', $request->query('provinsi'))
            ->where('kotkab', $request->query('kotkab'))
            ->where('kota', $request->query('kota'))
            ->where('kec', $request->query('kecamatan'))
                            // ✅ DIPERBAIKI: Memilih 'kodlan', bukan 'id'
            ->select('kodlan', 'namasekolah')->get();

        return response()->json($sekolah);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->query('q', ''); // 'q' adalah parameter default dari Select2
        $query = Sekolah::query();

        // Filter by City
        if ($request->filled('kota')) {
            $query->where('kota', $request->kota);
        } 
        // Filter by Region
        elseif ($request->filled('region')) {
            $regionService = app(\App\Services\Ekstrakurikuler\RegionMappingService::class);
            $cities = $regionService->getCitiesByRegion($request->region);
            $query->whereIn('kota', $cities->toArray());
        }

        // Group the search term conditions to maintain logical precedence (AND (a OR b))
        $query->where(function ($q) use ($searchTerm) {
            $q->where('namasekolah', 'LIKE', '%'.$searchTerm.'%')
              ->orWhere('kodlan', 'LIKE', '%'.$searchTerm.'%');
        });

        $sekolahs = $query->orderBy('namasekolah', 'asc')
            ->select('kodlan', 'namasekolah', 'kotkab', 'kec')
            ->limit(20)
            ->get();

        // Select2 AJAX membutuhkan format JSON dengan key 'results'
        $results = $sekolahs->map(function ($sekolah) {
            return [
                'id' => $sekolah->kodlan, // 'id' adalah key yang dibutuhkan oleh Select2
                'text' => $sekolah->namasekolah.' ('.$sekolah->kodlan.')', // 'text' untuk tampilan
                'kotkab' => $sekolah->kotkab,
                'kec' => $sekolah->kec,
            ];
        });

        return response()->json(['results' => $results]);
    }
}
