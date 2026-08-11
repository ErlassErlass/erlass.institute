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
                $query->where('status', '!=', 'dibatalkan');
                if ($this->periodStart && $this->periodEnd) {
                    $query->whereBetween('tanggal_terjadwal', [$this->periodStart, $this->periodEnd]);
                }
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
            'No. Telepon / WA',
            'Kompetensi 1',
            'Kompetensi 2',
            'Kota Domisili',
            'Jumlah Sesi',
            'Status',
            'Kategori' // Recommended / OK
        ];
    }

    public function map($instructor): array
    {
        return [
            $instructor->instructor_id ?? '-',
            $instructor->nama_lengkap,
            $instructor->no_telephone ?? ($instructor->instructorProfile->no_hp_2 ?? '-'),
            $instructor->kompetensi_1 ?? '-',
            $instructor->kompetensi_2 ?? '-',
            $instructor->instructorProfile->kota_domisili ?? '-',
            $instructor->ekstrakurikuler_sessions_count,
            ucfirst($instructor->status),
            $instructor->ekstrakurikuler_sessions_count == 0 ? 'Belum Ada Jadwal' : 'Aktif'
        ];
    }
}
