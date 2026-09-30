<?php

namespace App\Exports;

use App\Models\EkstrakurikulerSession;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonitoringBelumLaporanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle, WithColumnFormatting
{
    protected $sessions;
    private $rowNumber = 0;

    public function __construct($sessions = null)
    {
        $this->sessions = $sessions;
    }

    public function title(): string
    {
        return 'Monitoring Belum Laporan';
    }

    public function collection()
    {
        if ($this->sessions !== null) {
            return collect($this->sessions);
        }

        return EkstrakurikulerSession::with([
            'ekstrakurikuler.sekolah',
            'ekstrakurikuler.sales',
            'rombel.ekstrakurikuler.sekolah',
            'rombel.ekstrakurikuler.sales',
            'instruktur.instructorProfile',
            'asisten',
        ])
        ->whereDoesntHave('laporanMengajar')
        ->where('status', '!=', 'dibatalkan')
        ->whereDate('tanggal_terjadwal', '<=', Carbon::today())
        ->orderBy('tanggal_terjadwal', 'asc')
        ->orderBy('jam_mulai_terjadwal', 'asc')
        ->orderBy('id', 'asc')
        ->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'ID Sesi',
            'Tanggal Sesi',
            'Hari',
            'Jam Sesi',
            'Kode Sekolah',
            'Nama Sekolah',
            'Program Ekskul',
            'Rombel',
            'Pertemuan Ke',
            'Nama Instruktur',
            'No. WhatsApp / HP',
            'Nama Asisten',
            'Status Sesi',
            'Jam Check-in Aktual',
            'Status Keterlambatan',
            'Sales PIC',
            'Link Sesi',
        ];
    }

    public function map($session): array
    {
        $this->rowNumber++;

        $ekskul = $session->ekstrakurikuler;
        $rombel = $session->rombel;
        $sekolah = $ekskul?->sekolah ?? $rombel?->ekstrakurikuler?->sekolah;

        $tglObj = $session->tanggal_terjadwal ? Carbon::parse($session->tanggal_terjadwal) : null;
        $tanggalSesi = $tglObj ? $tglObj->format('d/m/Y') : '-';
        $hari = $tglObj ? $tglObj->locale('id')->isoFormat('dddd') : (ucfirst($rombel?->hari ?? '-'));

        $jamMulai = $session->jam_mulai_terjadwal ? Carbon::parse($session->jam_mulai_terjadwal)->format('H:i') : '-';
        $jamSelesai = $session->jam_selesai_terjadwal ? Carbon::parse($session->jam_selesai_terjadwal)->format('H:i') : '-';
        $jamSesi = ($jamMulai !== '-' || $jamSelesai !== '-') ? "{$jamMulai} - {$jamSelesai}" : '-';

        $kodeSekolah = $sekolah?->kodlan ?? $ekskul?->sekolah_kodlan ?? '-';
        $namaSekolah = $sekolah?->namasekolah ?? $ekskul?->sekolah_kodlan ?? 'N/A';
        $programEkskul = $ekskul?->nama_ekskul ?: ($ekskul?->kategori_program ?? '-');
        $namaRombel = $rombel?->nama_rombel ?? ($rombel?->nomor_rombel ? 'Rombel ' . $rombel->nomor_rombel : 'Rombel 1');
        $pertemuanKe = $session->nomor_pertemuan ?? '-';

        $instruktur = $session->instruktur;
        $namaInstruktur = $instruktur?->nama_lengkap ?? $instruktur?->name ?? 'Belum Ditugaskan';

        $prof = $instruktur?->instructorProfile;
        $rawPhone = $instruktur?->no_telephone ?: ($prof?->no_hp ?? $prof?->no_wa ?? $prof?->no_telepon);
        $noHp = ($rawPhone && trim($rawPhone) !== '' && trim($rawPhone) !== '-') ? trim($rawPhone) : '-';

        $namaAsisten = $session->asisten?->nama_lengkap ?? $session->asisten?->name ?? '-';
        $statusSesi = ucfirst($session->status);
        $jamCheckin = $session->jam_mulai_aktual ? Carbon::parse($session->jam_mulai_aktual)->format('H:i:s') : '-';

        $keterlambatan = '-';
        if ($tglObj) {
            $diff = (int) $tglObj->startOfDay()->diffInDays(Carbon::today(), false);
            if ($diff === 0) {
                $keterlambatan = 'Hari Ini (Belum Laporan)';
            } elseif ($diff > 0) {
                $keterlambatan = "Terlambat {$diff} Hari";
            } else {
                $keterlambatan = 'Mendatang';
            }
        }

        $salesPic = $ekskul?->sales?->nama_lengkap 
            ?? $ekskul?->sales?->name 
            ?? ($rombel?->ekstrakurikuler?->sales?->nama_lengkap ?? '-');

        $linkSesi = url("/ekstrakurikuler/sessions/{$session->id}");

        return [
            $this->rowNumber,
            $session->id,
            $tanggalSesi,
            $hari,
            $jamSesi,
            $kodeSekolah,
            $namaSekolah,
            $programEkskul,
            $namaRombel,
            $pertemuanKe,
            $namaInstruktur,
            $noHp,
            $namaAsisten,
            $statusSesi,
            $jamCheckin,
            $keterlambatan,
            $salesPic,
            $linkSesi,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // ID Sesi
            'L' => NumberFormat::FORMAT_TEXT, // No. HP / WhatsApp
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(1, $this->rowNumber + 1);

        // Header style row height
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Center alignments for specific columns
        $sheet->getStyle("A2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("J2:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("L2:P{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Vertical center for all cells
        $sheet->getStyle("A1:R{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Thin borders for entire table
        $sheet->getStyle("A1:R{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDC2626'], // Tailwind Red-600 for urgent monitoring
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
