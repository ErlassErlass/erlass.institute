<?php

namespace App\Exports;

use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AvailabilityExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected string $weekStr;
    protected ?string $kotaFilter;
    protected ?string $dayFilter;

    protected int $rowIndex = 2;

    protected array $dayColumns = [
        'Senin'  => 'E',
        'Selasa' => 'F',
        'Rabu'   => 'G',
        'Kamis'  => 'H',
        'Jumat'  => 'I',
        'Sabtu'  => 'J',
    ];

    protected array $cellStyles = [];
    protected $collectionData = null;

    public function __construct(string $weekStr, ?string $kotaFilter = null, ?string $dayFilter = null)
    {
        $this->weekStr    = $weekStr;
        $this->kotaFilter = $kotaFilter ?: null;
        $this->dayFilter  = $dayFilter ?: null;
    }

    public function title(): string
    {
        return 'Ketersediaan_' . str_replace('-', '_', $this->weekStr);
    }

    public function collection()
    {
        if ($this->collectionData) {
            return $this->collectionData;
        }

        $monday   = null;
        $saturday = null;
        if (preg_match('/^(\d{4})-W(\d{2})$/', $this->weekStr, $m)) {
            $year    = (int) $m[1];
            $weekNum = (int) $m[2];
            $monday   = Carbon::now()->setISODate($year, $weekNum, 1)->startOfDay();
            $saturday = $monday->copy()->addDays(5)->endOfDay();
        }

        $bulanIni    = Carbon::now()->startOfMonth();
        $bulanIniEnd = Carbon::now()->endOfMonth();

        $query = User::teachingStaff()
            ->with([
                'instructorProfile',
                'ekstrakurikulerSessions' => function ($q) use ($monday, $saturday) {
                    $q->with(['ekstrakurikuler.sekolah', 'rombel.ekstrakurikuler.sekolah'])
                      ->whereNotIn('status', ['dibatalkan']);
                    if ($monday && $saturday) {
                        $q->whereBetween('tanggal_terjadwal', [$monday, $saturday]);
                    }
                }
            ])
            ->withCount([
                'ekstrakurikulerSessions as sesi_aktif_bulan_ini' => function ($q) use ($bulanIni, $bulanIniEnd) {
                    $q->whereNotIn('status', ['dibatalkan'])
                      ->whereBetween('tanggal_terjadwal', [$bulanIni, $bulanIniEnd]);
                }
            ]);

        if ($this->kotaFilter) {
            $query->whereHas('instructorProfile', function ($q) {
                $q->where('kota_domisili', 'LIKE', $this->kotaFilter);
            });
        }

        $instructors = $query->orderBy('nama_lengkap')->get();

        $hariMapping = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];

        $rows = $instructors->map(function ($instr) use ($monday, $hariMapping) {
            $waktuMengajar = $instr->instructorProfile?->waktu_mengajar ?? [];
            $hasWaktu      = !empty($waktuMengajar);
            $kotaDomisili  = $instr->instructorProfile?->kota_domisili ?? '';

            $dayData = [];
            for ($dow = 1; $dow <= 6; $dow++) {
                $dayName    = $hariMapping[$dow];
                $dayDate    = $monday ? $monday->copy()->addDays($dow - 1) : null;
                $dayDateStr = $dayDate ? $dayDate->toDateString() : null;

                $slots = $hasWaktu ? ($waktuMengajar[$dayName] ?? []) : null;

                if ($slots === null) {
                    $dayData[$dayName] = ['status' => 'no_data', 'label' => 'Belum Isi Jadwal'];
                    continue;
                }

                if (empty($slots)) {
                    $dayData[$dayName] = ['status' => 'unavailable', 'label' => 'Libur'];
                    continue;
                }

                sort($slots);
                $availRange = $this->parseRanges([$dayName => $slots])[$dayName] ?? '';

                $todaySessions = ($dayDateStr && $monday)
                    ? $instr->ekstrakurikulerSessions->filter(
                        fn($s) => Carbon::parse($s->tanggal_terjadwal)->toDateString() === $dayDateStr
                    )
                    : collect();

                $busyHours = $todaySessions->count();

                if ($busyHours === 0) {
                    $status = 'free';
                    $label  = 'Free (' . $availRange . ')';
                } elseif ($busyHours >= count($slots)) {
                    $status = 'busy';
                    $label  = 'Penuh (' . $busyHours . ' sesi)';
                } else {
                    $status = 'partial';
                    $label  = 'Ada Sesi (' . $busyHours . '/' . count($slots) . ')';
                }

                $dayData[$dayName] = ['status' => $status, 'label' => $label];
            }

            $instr->_export_day_data = $dayData;
            $instr->_export_kota     = $kotaDomisili;
            return $instr;
        });

        if ($this->dayFilter) {
            $dayFilter = $this->dayFilter;
            $rows = $rows->filter(function ($instr) use ($dayFilter) {
                $status = $instr->_export_day_data[$dayFilter]['status'] ?? 'no_data';
                return in_array($status, ['free', 'partial', 'busy']);
            })->values();
        }

        $this->collectionData = $rows;
        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'Nama Instruktur', 'Email', 'Domisili', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Sesi Bulan Ini'];
    }

    public function map($instr): array
    {
        $days    = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $dayData = $instr->_export_day_data ?? [];
        $no      = $this->rowIndex - 1;

        foreach ($days as $day) {
            $col    = $this->dayColumns[$day];
            $status = $dayData[$day]['status'] ?? 'no_data';
            $this->cellStyles[] = ['col' => $col, 'row' => $this->rowIndex, 'status' => $status];
        }

        $this->rowIndex++;

        $row = [$no, $instr->nama_lengkap, $instr->email, $instr->_export_kota ?? ''];
        foreach ($days as $day) {
            $row[] = $dayData[$day]['label'] ?? '—';
        }
        $row[] = $instr->sesi_aktif_bulan_ini ?? 0;

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1e40af']],
        ]);

        foreach ($this->cellStyles as $cs) {
            $cellRef = $cs['col'] . $cs['row'];
            $color   = $this->statusColor($cs['status']);
            if ($color) {
                $sheet->getStyle($cellRef)->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $color]],
                ]);
            }
        }

        $sheet->getColumnDimension('A')->setWidth(6);

        return [];
    }

    protected function statusColor(string $status): ?string
    {
        return match ($status) {
            'free'        => 'FFdcfce7',
            'partial'     => 'FFfef3c7',
            'busy'        => 'FFfee2e2',
            'unavailable' => 'FFf1f5f9',
            'no_data'     => 'FFfffbeb',
            default       => null,
        };
    }

    protected function parseRanges(array $waktuMengajar): array
    {
        $result = [];
        foreach ($waktuMengajar as $day => $slots) {
            if (empty($slots)) continue;
            sort($slots);
            $ranges      = [];
            $rangeStart  = null;
            $prevSlotEnd = null;

            foreach ($slots as $slot) {
                [$h, $mStr] = explode(':', $slot);
                $slotStart  = (int)$h * 60 + (int)$mStr;
                $slotEnd    = $slotStart + 60;

                if ($rangeStart === null) {
                    $rangeStart  = $slotStart;
                    $prevSlotEnd = $slotEnd;
                } elseif ($slotStart === $prevSlotEnd) {
                    $prevSlotEnd = $slotEnd;
                } else {
                    $ranges[]    = $this->mins($rangeStart) . '–' . $this->mins($prevSlotEnd);
                    $rangeStart  = $slotStart;
                    $prevSlotEnd = $slotEnd;
                }
            }
            if ($rangeStart !== null) {
                $ranges[] = $this->mins($rangeStart) . '–' . $this->mins($prevSlotEnd);
            }
            $result[$day] = implode(', ', $ranges);
        }
        return $result;
    }

    protected function mins(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
