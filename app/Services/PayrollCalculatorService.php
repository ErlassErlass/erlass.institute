<?php

namespace App\Services;

use App\Models\EkstrakurikulerSession;
use App\Models\PayrollBatch;
use App\Models\PayrollItem;
use App\Models\SalaryRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollCalculatorService
{
    /**
     * Constant for late penalty amount.
     */
    const LATE_PENALTY_AMOUNT = 25000.00;

    /**
     * Calculate fee and punctuality for a single session according to Memo No. 536/EPI/V/2025 (TAB 2025/2026).
     */
    public function calculateSessionFee(EkstrakurikulerSession $session): array
    {
        // 1. Determine student count for the rombel/session
        $rombel = $session->rombel;
        $studentCount = 0;
        if ($rombel) {
            $studentCount = (int) ($rombel->jumlah_siswa ?? 0);
            if ($studentCount === 0 && method_exists($rombel, 'siswa')) {
                $studentCount = $rombel->siswa()->wherePivot('status', 'aktif')->count();
            }
        }
        if ($studentCount === 0 && $session->laporanMengajar) {
            $studentCount = $session->laporanMengajar->absensi()->count();
        }

        // 2. Base rate calculation based on Official Memo No. 536/EPI/V/2025 (TAB 2025/2026)
        // - Siswa >= 15: Rp 150.000
        // - Siswa 12 - 14: Rp 115.000
        // - Siswa 10 - 11: Rp 100.000
        // - Siswa 8 - 9: Rp 75.000
        // - Siswa < 8: Rp 0 (Pembelajaran Hold)
        $baseRate = 0.00;

        if ($studentCount >= 15) {
            $baseRate = 150000.00;
        } elseif ($studentCount >= 12) {
            $baseRate = 115000.00;
        } elseif ($studentCount >= 10) {
            $baseRate = 100000.00;
        } elseif ($studentCount >= 8) {
            $baseRate = 75000.00;
        } else {
            // < 8 students: Pembelajaran Hold (Rp 0)
            $baseRate = 0.00;
        }

        // Fallback to salary_rates table if baseRate is 0 and student count >= 8 (e.g. general level rate)
        if ($baseRate === 0.00 && $studentCount >= 8) {
            $instructor = $session->instruktur;
            $level = strtolower($instructor->instructorProfile->level ?? 'junior');
            $rateSetting = SalaryRate::where('level', $level)
                ->where(function ($query) {
                    $query->whereNull('product_category')->orWhere('product_category', '');
                })->first();
            $baseRate = $rateSetting ? (float) $rateSetting->base_rate : 100000.00;
        }

        // 3. Product Bonus if applicable
        $productBonus = 0.00;
        if ($session->ekstrakurikuler && $session->ekstrakurikuler->kategori_program) {
            $program = $session->ekstrakurikuler->kategori_program;
            $rateWithBonus = SalaryRate::whereNotNull('product_category')
                ->where('product_category', '!=', '')
                ->get()
                ->first(function ($rate) use ($program) {
                    return stripos($program, $rate->product_category) !== false;
                });
                
            if ($rateWithBonus) {
                $productBonus = (float) $rateWithBonus->product_bonus;
            }
        }

        // 4. Punctuality Check & Penalty
        $checkinStatus = 'on_time';
        $penalty = 0.00;

        if ($session->jam_mulai_aktual && $session->jam_mulai_terjadwal) {
            $scheduled = Carbon::parse($session->jam_mulai_terjadwal);
            $actual = Carbon::parse($session->jam_mulai_aktual);
            $diffMinutes = $scheduled->diffInMinutes($actual, false);

            if ($diffMinutes <= 0) {
                $checkinStatus = ($diffMinutes <= -10) ? 'excellent' : 'on_time';
            } elseif ($diffMinutes > 0 && $diffMinutes < 15) {
                $checkinStatus = 'warning';
            } else {
                $checkinStatus = 'penalty';
                $penalty = self::LATE_PENALTY_AMOUNT;
            }
        }

        // 5. Total Fee (base + bonus)
        $calculatedFee = $baseRate + $productBonus;

        // 6. Transport Fee Calculation according to Memo No. 536/EPI/V/2025:
        // - Guru Internal sekolah / Kegiatan di Kantor Erlass: Transport = Rp 0
        // - Jarak >= 10 KM: (jarak_km * Rp 350) + Rp 7.500 (sewa kendaraan)
        // - Jarak < 10 KM: minimal flat Rp 20.000 / custom fee
        $transportFee = 0.00;
        $ekskul = $session->ekstrakurikuler;
        $sekolah = $ekskul ? $ekskul->sekolah : null;
        $instructor = $session->instruktur;

        $isGuruInternal = false;
        if ($instructor) {
            $isGuruInternal = (bool) ($instructor->is_guru_internal ?? false);
        }
        $isKantorErlass = ($ekskul && stripos($ekskul->alamat_lengkap ?? '', 'Kantor Erlass') !== false);

        if ($isGuruInternal || $isKantorErlass) {
            $transportFee = 0.00;
        } elseif ($ekskul && $ekskul->jarak_km !== null && (float)$ekskul->jarak_km >= 10.0) {
            $distKm = (float) $ekskul->jarak_km;
            $transportFee = ($distKm * 350.00) + 7500.00; // Rp 350/KM + Rp 7.500 sewa kendaraan
        } elseif ($ekskul && $ekskul->jarak_km !== null && (float)$ekskul->jarak_km > 0) {
            $distKm = (float) $ekskul->jarak_km;
            $calculatedTransport = ($distKm * 350.00) + 7500.00;
            $transportFee = max($calculatedTransport, 20000.00);
        } elseif ($sekolah && $sekolah->kustom_transport_fee !== null) {
            $transportFee = (float)$sekolah->kustom_transport_fee;
        } else {
            $transportFee = 30000.00;
        }

        // 7. Override fee if present
        $finalFee = $session->override_fee !== null ? (float) $session->override_fee : $calculatedFee;

        return [
            'student_count' => $studentCount,
            'base_rate' => $baseRate,
            'product_bonus' => $productBonus,
            'calculated_fee' => $calculatedFee,
            'transport_fee' => $transportFee,
            'actual_checkin_status' => $checkinStatus,
            'actual_checkin_penalty' => $penalty,
            'net_fee' => max(0.00, $finalFee - $penalty)
        ];
    }

    /**
     * Compile unpaid completed sessions for a month into a payroll batch.
     */
    public function generateMonthlyPayroll(PayrollBatch $batch): int
    {
        return DB::transaction(function () use ($batch) {
            // Get batch period (month and year)
            $period = Carbon::parse($batch->periode);
            $month = $period->month;
            $year = $period->year;

            // Auto-link any standalone LaporanMengajar in this period to guarantee ALL reports are included
            $standaloneReports = \App\Models\LaporanMengajar::whereNull('ekstrakurikuler_session_id')
                ->whereMonth('jadwal_mengajar', $month)
                ->whereYear('jadwal_mengajar', $year)
                ->whereNotNull('user_id_instruktur')
                ->get();

            foreach ($standaloneReports as $report) {
                $report->ensureSessionLinked();
            }

            // Find all unpaid completed sessions for the period that have reports
            $sessions = EkstrakurikulerSession::where('payment_status', 'unpaid')
                ->where('status', EkstrakurikulerSession::STATUS_SELESAI)
                ->whereMonth('tanggal_pelaksanaan', $month)
                ->whereYear('tanggal_pelaksanaan', $year)
                ->whereHas('laporanMengajar') // Must have teaching report complete
                ->whereNotNull('user_id_instruktur')
                ->get();

            if ($sessions->isEmpty()) {
                return 0;
            }

            // Group sessions by instructor
            $groupedSessions = $sessions->groupBy('user_id_instruktur');
            $itemsCount = 0;

            foreach ($groupedSessions as $instructorId => $instructorSessions) {
                $totalSessions = $instructorSessions->count();
                $totalBaseFee = 0.00;
                $totalProductBonus = 0.00;
                $totalPenalty = 0.00;
                $totalBonus = 0.00;
                $totalTransportFee = 0.00;

                // Process each session to store its details
                foreach ($instructorSessions as $session) {
                    $calc = $this->calculateSessionFee($session);
                    
                    // Update session columns
                    $session->update([
                        'actual_checkin_status' => $calc['actual_checkin_status'],
                        'actual_checkin_penalty' => $calc['actual_checkin_penalty'],
                        'calculated_fee' => $calc['calculated_fee'],
                        'transport_fee' => $calc['transport_fee'],
                        'payment_status' => 'processing',
                    ]);

                    $totalBaseFee += $calc['base_rate'];
                    $totalProductBonus += $calc['product_bonus'];
                    $totalPenalty += $calc['actual_checkin_penalty'];
                }

                // Check override fee and sum up transport fee for sessions
                $actualTotalBase = 0.00;
                $totalTransportFee = 0.00;
                foreach ($instructorSessions as $session) {
                    if ($session->override_fee !== null) {
                        $actualTotalBase += (float) $session->override_fee;
                    } else {
                        $actualTotalBase += (float) $session->calculated_fee;
                    }
                    $totalTransportFee += (float) $session->transport_fee;
                }

                // net_salary = base_fee_with_overrides + total_transport_fee + total_bonus - total_penalty
                $netSalary = max(0.00, $actualTotalBase + $totalTransportFee + $totalBonus - $totalPenalty);

                // Create payroll item
                $payrollItem = PayrollItem::create([
                    'payroll_batch_id' => $batch->id,
                    'user_id_instruktur' => $instructorId,
                    'total_sessions' => $totalSessions,
                    'total_base_fee' => $instructorSessions->sum(function($s) {
                        return $s->override_fee !== null ? (float)$s->override_fee : (float)$s->calculated_fee;
                    }),
                    'total_product_bonus' => $totalProductBonus,
                    'total_penalty' => $totalPenalty,
                    'total_bonus' => $totalBonus,
                    'total_transport_fee' => $totalTransportFee,
                    'net_salary' => $netSalary,
                    'status' => 'pending',
                ]);

                // Link sessions to payroll item
                foreach ($instructorSessions as $session) {
                    $session->update([
                        'payroll_item_id' => $payrollItem->id
                    ]);
                }

                $itemsCount++;
            }

            return $itemsCount;
        });
    }
}
