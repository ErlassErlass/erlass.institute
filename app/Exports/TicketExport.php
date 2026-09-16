<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TicketExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
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
            'No. Tiket',
            'Tanggal Dibuat',
            'Pembuat Tiket / Instruktur',
            'Kontak / No. WA',
            'Kategori',
            'Prioritas',
            'Status',
            'Subjek / Judul Kendala',
            'Deskripsi Kendala',
            'Sekolah Terkait',
            'Rombel / Program Terkait',
            'Tanggal Sesi Mengajar',
            'PIC Penangan / Ditugaskan Ke',
            'Total Balasan Diskusi',
            'Waktu Selesai (Resolved At)',
            'Waktu Ditutup (Closed At)',
        ];
    }

    public function map($ticket): array
    {
        $this->rowNumber++;

        // Format dates
        $createdAt = $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-';
        $resolvedAt = $ticket->resolved_at ? $ticket->resolved_at->format('d/m/Y H:i') : '-';
        $closedAt = $ticket->closed_at ? $ticket->closed_at->format('d/m/Y H:i') : '-';

        // Session & school related info
        $session = $ticket->session;
        $sekolahNama = $session?->rombel?->ekstrakurikuler?->sekolah?->namasekolah 
            ?? $session?->ekstrakurikuler?->sekolah?->namasekolah 
            ?? '-';
        $rombelNama = $session?->rombel?->nama_rombel 
            ?? $session?->ekstrakurikuler?->nama_ekstrakurikuler 
            ?? '-';
        $tanggalSesi = $session?->tanggal_terjadwal 
            ? Carbon::parse($session->tanggal_terjadwal)->format('d/m/Y') . ($session->jam_mulai ? ' (' . substr($session->jam_mulai, 0, 5) . ')' : '') 
            : '-';

        // User / creator info
        $creatorName = $ticket->user?->nama_lengkap ?? 'Instruktur';
        $creatorContact = $ticket->user?->no_wa ?? $ticket->user?->email ?? '-';

        // Assigned staff
        $assignedName = $ticket->assignedStaff?->nama_lengkap ?? 'Belum Ditugaskan';

        // Total replies
        $totalReplies = $ticket->replies_count ?? ($ticket->relationLoaded('replies') ? $ticket->replies->count() : 0);

        return [
            $this->rowNumber,
            $ticket->ticket_number,
            $createdAt,
            $creatorName,
            $creatorContact,
            $ticket->kategori_label ?? ucfirst(str_replace('_', ' ', $ticket->kategori)),
            $ticket->prioritas_label ?? ucfirst($ticket->prioritas ?? 'Medium'),
            $ticket->status_label ?? ucfirst($ticket->status),
            $ticket->judul ?? '-',
            $ticket->deskripsi ?? '-',
            $sekolahNama,
            $rombelNama,
            $tanggalSesi,
            $assignedName,
            $totalReplies,
            $resolvedAt,
            $closedAt,
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
