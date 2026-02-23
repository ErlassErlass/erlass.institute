<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class ScheduleDistributionExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $periodStart;
    protected $periodEnd;

    public function __construct($periodStart, $periodEnd)
    {
        $this->periodStart = $periodStart;
        $this->periodEnd = $periodEnd;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::teachingStaff()
            ->with(['instructorProfile:user_id,kota_domisili'])
            ->withCount(['ekstrakurikulerSessions' => function ($query) {
                // Count ALL sessions (except cancelled) in the period
                $query->where('status', '!=', 'dibatalkan')
                      ->whereBetween('tanggal_terjadwal', [$this->periodStart, $this->periodEnd]);
            }])
            ->orderBy('ekstrakurikuler_sessions_count', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Instruktur',
            'Nama Lengkap',
            'Kota Domisili',
            'Jumlah Sesi',
            'Status',
            'Kategori' // Recommended / OK
        ];
    }

    public function map($instructor): array
    {
        // Calculate average inside here if needed or pass it in constructor. 
        // For simplicity, just Raw Data first.
        
        return [
            $instructor->instructor_id ?? '-',
            $instructor->nama_lengkap,
            $instructor->instructorProfile->kota_domisili ?? '-',
            $instructor->ekstrakurikuler_sessions_count,
            ucfirst($instructor->status),
            $instructor->ekstrakurikuler_sessions_count == 0 ? 'Belum Ada Jadwal' : 'Aktif'
        ];
    }
}
