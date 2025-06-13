<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;

class SekolahApiController extends Controller
{
    // 1. Ambil Tipe Kota/Kabupaten berdasarkan Provinsi
    public function getKotkabTipe(Request $request)
    {
        $request->validate(['provinsi' => 'required|string']);
        $tipe = Sekolah::where('provinsi', $request->query('provinsi'))
                        ->select('kotkab')->distinct()->pluck('kotkab');
        return response()->json($tipe);
    }

    // 2. Ambil Nama Kota berdasarkan Provinsi dan Tipe Kotkab
    public function getKota(Request $request)
    {
        $request->validate(['provinsi' => 'required|string', 'kotkab_tipe' => 'required|string']);
        $kota = Sekolah::where('provinsi', $request->query('provinsi'))
                        ->where('kotkab', $request->query('kotkab_tipe'))
                        ->select('kota')->distinct()->pluck('kota');
        return response()->json($kota);
    }

    // 3. Ambil Kecamatan berdasarkan Provinsi, Tipe Kotkab, dan Nama Kota
    public function getKecamatan(Request $request)
    {
        $request->validate(['provinsi' => 'required|string', 'kotkab_tipe' => 'required|string', 'kota' => 'required|string']);
        $kecamatan = Sekolah::where('provinsi', $request->query('provinsi'))
                            ->where('kotkab', $request->query('kotkab_tipe'))
                            ->where('kota', $request->query('kota'))
                            ->select('kec')->distinct()->pluck('kec');
        return response()->json($kecamatan);
    }

    // 4. Ambil Sekolah berdasarkan semua filter sebelumnya
    public function getSekolah(Request $request)
    {
        $request->validate(['provinsi' => 'required|string', 'kotkab_tipe' => 'required|string', 'kota' => 'required|string', 'kecamatan' => 'required|string']);
        $sekolah = Sekolah::where('provinsi', $request->query('provinsi'))
                            ->where('kotkab', $request->query('kotkab_tipe'))
                            ->where('kota', $request->query('kota'))
                            ->where('kec', $request->query('kecamatan'))
                            ->select('id', 'namasekolah', 'kodlan')->get();
        return response()->json($sekolah);
    }
}