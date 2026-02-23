<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiRekapExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $rekapData;
    protected $students;
    protected $selectedRombel;
    protected $selectedSekolahData;

    public function __construct($data, $selectedRombel, $selectedSekolahData)
    {
        $this->rekapData = $data['rekapData'];
        $this->students = $data['students'];
        $this->selectedRombel = $selectedRombel;
        $this->selectedSekolahData = $selectedSekolahData;
    }

    public function view(): View
    {
        return view('absensi.export_excel', [
            'rekapData' => $this->rekapData,
            'students' => $this->students,
            'selectedRombel' => $this->selectedRombel,
            'selectedSekolahData' => $this->selectedSekolahData,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Bold header rows
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Range of headers table
            5 => ['font' => ['bold' => true]], 
        ];
    }
}
