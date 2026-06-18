<?php

namespace App\Http\Controllers;

use App\Models\SchoolCalendar;
use App\Models\Sekolah;
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SchoolCalendarController extends Controller
{
    public function __construct(protected CalendarService $calendarService) {}

    public function index(Request $request, string $kodlan)
    {
        $sekolah = Sekolah::where('kodlan', $kodlan)->firstOrFail();

        $year  = $request->input('tahun', now()->year);
        $start = "{$year}-01-01";
        $end   = "{$year}-12-31";

        $events = $this->calendarService->getSchoolCalendarInRange($kodlan, $start, $end);

        // Gabungan dengan libur nasional untuk tampilan kalender
        $nationalHolidays = $this->calendarService->getHolidaysInRange($start, $end);

        return view('sekolah.calendar', compact('sekolah', 'events', 'nationalHolidays', 'year'));
    }

    public function store(Request $request, string $kodlan)
    {
        $sekolah = Sekolah::where('kodlan', $kodlan)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'nama'            => 'required|string|max:255',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis'           => 'required|in:libur_sekolah,ujian,kegiatan_sekolah,lainnya',
            'is_blocking'     => 'boolean',
            'catatan'         => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['sekolah_kodlan'] = $kodlan;
        $data['is_blocking']    = $request->boolean('is_blocking', true);
        $data['created_by']     = Auth::id();

        SchoolCalendar::create($data);

        return back()->with('success', 'Event kalender sekolah berhasil ditambahkan.');
    }

    public function destroy(string $kodlan, SchoolCalendar $schoolCalendar)
    {
        // Pastikan event milik sekolah yang sama
        abort_if($schoolCalendar->sekolah_kodlan !== $kodlan, 403);

        $schoolCalendar->delete();

        return back()->with('success', 'Event kalender sekolah berhasil dihapus.');
    }
}
