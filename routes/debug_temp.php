
// Temporary Debug Route for Attendance Analytics
Route::get('/debug-attendance', function () {
    $attendanceStart = now()->subMonths(5)->startOfMonth();
    $attendanceEnd = now()->endOfMonth();

    $attendanceData = \App\Models\LaporanMengajar::withCount([
        'absensi as total_hadir' => function ($query) {
            $query->where('hadir', true);
        },
        'absensi as total_records'
    ])
    ->whereBetween('jadwal_mengajar', [$attendanceStart, $attendanceEnd])
    ->orderBy('jadwal_mengajar', 'asc')
    ->get();
    
    $grouped = $attendanceData->groupBy(function($date) {
        return \Carbon\Carbon::parse($date->jadwal_mengajar)->format('Y-m');
    });

    $results = [];
    $period = \Carbon\CarbonPeriod::create($attendanceStart, '1 month', $attendanceEnd);
    
    foreach ($period as $date) {
        $monthKey = $date->format('Y-m');
        $monthLabel = $date->translatedFormat('F');
        
        $reports = $grouped[$monthKey] ?? collect();
        
        $totalHadir = $reports->sum('total_hadir');
        $totalRecords = $reports->sum('total_records');
        
        $percentage = $totalRecords > 0 ? round(($totalHadir / $totalRecords) * 100, 1) : 0;
        
        $results[$monthKey] = [
            'label' => $monthLabel,
            'reports_count' => $reports->count(),
            'total_hadir' => $totalHadir,
            'total_records' => $totalRecords,
            'percentage' => $percentage
        ];
    }
    
    return [
        'start' => $attendanceStart->toDateTimeString(),
        'end' => $attendanceEnd->toDateTimeString(),
        'results' => $results,
        'raw_counts' => $attendanceData->map(function($r) {
            return [
                'id' => $r->id,
                'date' => $r->jadwal_mengajar, 
                'hadir' => $r->total_hadir, 
                'records' => $r->total_records
            ];
        })->take(10)
    ];
});
