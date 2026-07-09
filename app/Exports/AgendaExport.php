<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AgendaExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return collect($this->rows)->map(function ($row) {
            return [
                $row['namsek'],
                $row['kategori_pengajaran'],
                $row['rombel'],
                $row['tanggal_mengajar'],
                $row['pertemuan_ke'],
                $row['jumlah_hadir'],
                $row['print_url'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Sekolah',
            'Kategori Pengajaran',
            'Rombel',
            'Tanggal Mengajar',
            'Pertemuan Ke-',
            'Jumlah Hadir',
            'Link Form Absensi',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'size' => 12],
                'fill'      => [
                    'fillType'   => 'solid',
                    'startColor' => ['argb' => 'FF1E3A5F'],
                ],
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            ],
        ];
    }
}
