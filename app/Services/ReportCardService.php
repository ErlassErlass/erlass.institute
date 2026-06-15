<?php

namespace App\Services;

use App\Models\ReportCard;
use App\Models\StudentScore;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ReportCardService
{
    /**
     * Generate and save report card PDF.
     */
    public function generate(StudentScore $score): ReportCard
    {
        $siswa = $score->siswa;
        $rombel = $score->ekstrakurikulerRombel;
        $ekskul = $score->ekstrakurikuler;

        // Fetch competencies
        $competencies = $score->generateCompetencies();

        // Render PDF
        $pdf = DomPDF::loadView('pdf.report_card', [
            'score' => $score,
            'siswa' => $siswa,
            'rombel' => $rombel,
            'ekskul' => $ekskul,
            'competencies' => $competencies,
        ]);

        $fileName = 'rapor_' . $siswa->id . '_' . $rombel->id . '_' . time() . '.pdf';
        $path = 'uploads/report_cards/' . $fileName;

        // Ensure directory exists in public disk and save the PDF
        Storage::disk('public')->put($path, $pdf->output());

        // Update or create ReportCard record
        $reportCard = ReportCard::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'ekstrakurikuler_rombel_id' => $rombel->id,
                'periode' => $score->periode,
            ],
            [
                'ekstrakurikuler_id' => $ekskul->id,
                'student_score_id' => $score->id,
                'file_path' => $path,
                'generated_at' => now(),
                'generated_by' => Auth::id(),
            ]
        );

        return $reportCard;
    }
}
