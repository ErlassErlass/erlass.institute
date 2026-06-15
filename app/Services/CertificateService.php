<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\StudentScore;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificateService
{
    /**
     * Check if a student is eligible for a certificate.
     * Minimum attendance of 75% is required.
     */
    public function isEligible(StudentScore $score): bool
    {
        return $score->nilai_kehadiran >= 75;
    }

    /**
     * Generate certificate PDF for an eligible score.
     */
    public function generate(StudentScore $score): ?Certificate
    {
        if (!$this->isEligible($score)) {
            return null;
        }

        $siswa = $score->siswa;
        $ekskul = $score->ekstrakurikuler;
        $rombel = $score->ekstrakurikulerRombel;

        // Generate a unique certificate code (e.g. CERT-2026-XXXXX)
        $year = date('Y');
        $random = strtoupper(Str::random(5));
        $code = "CERT-{$year}-{$siswa->id}{$rombel->id}-{$random}";

        // Create initial Certificate record
        $certificate = Certificate::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'ekstrakurikuler_id' => $ekskul->id,
            ],
            [
                'certificate_code' => $code,
                'issued_at' => now(),
                'status' => 'issued',
            ]
        );

        // Generate QR code and save it locally
        $verifyUrl = route('certificates.verify', $certificate->certificate_code);
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($verifyUrl);
        
        try {
            $qrContent = file_get_contents($qrCodeUrl);
            if ($qrContent) {
                $qrPath = 'uploads/qrcodes/qr_' . $certificate->id . '.png';
                Storage::disk('public')->put($qrPath, $qrContent);
                $certificate->update(['qr_code_path' => $qrPath]);
            }
        } catch (\Exception $e) {
            // Fallback handled in template
        }

        // Fetch competencies
        $competencies = $score->generateCompetencies();

        // Render 2-page Landscape PDF
        // Halaman 1: Certificate
        // Halaman 2: Transcript
        $pdf = DomPDF::loadView('pdf.certificate', [
            'certificate' => $certificate,
            'score' => $score,
            'siswa' => $siswa,
            'ekskul' => $ekskul,
            'rombel' => $rombel,
            'competencies' => $competencies,
        ]);
        $pdf->setPaper('a4', 'landscape');

        $fileName = 'certificate_' . $siswa->id . '_' . $ekskul->id . '_' . time() . '.pdf';
        $path = 'uploads/certificates/' . $fileName;

        // Save PDF to public storage
        Storage::disk('public')->put($path, $pdf->output());

        // Update Certificate record with file path
        $certificate->update([
            'file_path' => $path,
        ]);

        return $certificate;
    }
}
