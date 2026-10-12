<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EkstrakurikulerSessionExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $sessions;
    private $rowNumber = 0;

    public function __construct($sessions)
    {
        $this->sessions = $sessions;
    }

    public function title(): string
    {
        return 'Jadwal Sesi Ekskul';
    }

    public function collection()
    {
        return collect($this->sessions);
    }

    public function headings(): array
    {
        return [
            'Nama Instruktur',
            'Nama Asisten Instruktur',
            'Nama Sekolah',
            'Rombel',
            'Pertemuan',
            'Jam Mulai',
            'Ekskul',
            'Jml Siswa',
            'Kecamatan',
            'Sales',
            'PIC Ekskul',
            'Status Jadwal',
        ];
    }

    public function map($session): array
    {
        $sekolahNama = $session->ekstrakurikuler->sekolah->namasekolah 
            ?? ($session->rombel->ekstrakurikuler->sekolah->namasekolah ?? '-');

        $rombelNama = $session->rombel?->nomor_rombel 
            ? 'Rombel ' . $session->rombel->nomor_rombel 
            : ($session->rombel?->nama_rombel ?? '-');

        $pertemuan = 'Ke-' . $session->nomor_pertemuan;

        $instrukturNama = $session->instruktur->nama_lengkap ?? '-';
        $asistenNama = $session->asisten->nama_lengkap ?? '-';
        $jumlahSiswa = (int)($session->rombel?->jumlah_siswa ?? 0);

        $kecamatan = $session->ekstrakurikuler->sekolah->kec 
            ?? ($session->rombel->ekstrakurikuler->sekolah->kec ?? '-');

        $kategoriProgram = $session->ekstrakurikuler->kategori_program 
            ?? ($session->rombel->ekstrakurikuler->kategori_program ?? '-');

        $salesNama = $session->ekstrakurikuler->sales->nama_lengkap 
            ?? ($session->ekstrakurikuler->sales->name 
            ?? ($session->rombel->ekstrakurikuler->sales->nama_lengkap ?? '-'));

        $jamMulai = $session->jam_mulai_terjadwal 
            ? \Carbon\Carbon::parse($session->jam_mulai_terjadwal)->format('H:i') 
            : ($session->rombel?->jam_mulai ? \Carbon\Carbon::parse($session->rombel->jam_mulai)->format('H:i') : '-');

        $picEkskul = $session->ekstrakurikuler->penanggung_jawab 
            ?? ($session->rombel->ekstrakurikuler->penanggung_jawab ?? '-');

        $statusJadwal = $session->status_label ?? ucfirst($session->status);

        return [
            $instrukturNama,
            $asistenNama,
            $sekolahNama,
            $rombelNama,
            $pertemuan,
            $jamMulai,
            $kategoriProgram,
            $jumlahSiswa,
            $kecamatan,
            $salesNama,
            $picEkskul,
            $statusJadwal,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(1, count($this->sessions) + 1);

        // Header style row height
        $sheet->getRowDimension(1)->setRowHeight(26);

        // Center alignments: D (Rombel), E (Pertemuan), F (Jam Mulai), H (Jml Siswa), L (Status Jadwal)
        $sheet->getStyle("D2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L2:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Thin borders for entire table (A1:L{$lastRow})
        $sheet->getStyle("A1:L{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF107C41'], // Professional Excel Green
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
