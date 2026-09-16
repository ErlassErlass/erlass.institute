<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SchoolScheduleDistributionExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    private $rowNumber = 0;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            'No',
            'Wilayah Kota',
            'Kecamatan',
            'Nama Sales',
            'Group Leader Sales',
            'Jenis Program',
            'KodLan / NPSN',
            'Nama Sekolah',
            'Rombel',
            'Instruktur Utama (> 2x Jadwal)',
            'Total Sesi Jadwal',
            'Asisten Instruktur',
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;
        $row = is_array($item) ? (object) $item : $item;

        return [
            $this->rowNumber,
            $row->kota ?? '-',
            $row->kec ?? '-',
            $row->nama_salesman ?? 'Belum Ditentukan',
            $row->group_leader ?? '-',
            $row->kategori_program ?? '-',
            $row->kodlan ?? '-',
            $row->namasekolah ?? '-',
            $row->nama_rombel ?? '-',
            $row->instruktur_utama ?? '-',
            $row->total_sesi ?? 0,
            $row->asisten_instruktur ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['argb' => 'FF1E3A8A'],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
