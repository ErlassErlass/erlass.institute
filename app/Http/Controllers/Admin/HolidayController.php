<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('tahun', now()->year);

        $holidays = Holiday::byYear($year)
            ->orderBy('tanggal')
            ->get();

        $availableYears = Holiday::selectRaw('DISTINCT tahun')
            ->orderBy('tahun')
            ->pluck('tahun');

        return view('admin.holidays.index', compact('holidays', 'year', 'availableYears'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal'          => 'required|date|unique:holidays,tanggal',
            'nama'             => 'required|string|max:255',
            'jenis'            => 'required|in:libur_nasional,cuti_bersama,libur_agama,hari_besar',
            'is_tanggal_merah' => 'boolean',
            'catatan'          => 'nullable|string|max:1000',
        ], [
            'tanggal.unique' => 'Sudah ada entri hari libur pada tanggal tersebut.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['tahun']            = date('Y', strtotime($data['tanggal']));
        $data['is_tanggal_merah'] = $request->boolean('is_tanggal_merah', true);

        Holiday::create($data);

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validator = Validator::make($request->all(), [
            'nama'             => 'required|string|max:255',
            'jenis'            => 'required|in:libur_nasional,cuti_bersama,libur_agama,hari_besar',
            'is_tanggal_merah' => 'boolean',
            'catatan'          => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['is_tanggal_merah'] = $request->boolean('is_tanggal_merah', true);

        $holiday->update($data);

        return back()->with('success', 'Data hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}
