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
     * Calculate fee and punctuality for a single session.
     */
    public function calculateSessionFee(EkstrakurikulerSession $session): array
    {
        // 1. Determine base rate based on instructor level
        $instructor = $session->instruktur;
        $level = 'junior';
        
        if ($instructor && $instructor->instructorProfile) {
            $level = $instructor->instructorProfile->level ?? 'junior';
        }
        
        $level = strtolower($level);
        
        // Find base rate for the level
        $rateSetting = SalaryRate::where('level', $level)
            ->where(function ($query) {
                $query->whereNull('product_category')
                      ->orWhere('product_category', '');
            })
            ->first();
            
        $baseRate = $rateSetting ? (float) $rateSetting->base_rate : 100000.00;

        // 2. Check product bonus if extracurricular exists
        $productBonus = 0.00;
        if ($session->ekstrakurikuler && $session->ekstrakurikuler->kategori_program) {
            $program = $session->ekstrakurikuler->kategori_program;
            
            // Find a rate setting where product_category matches (substring or exact)
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

        // 3. Determine punctuality status & penalty
        $checkinStatus = 'on_time';
        $penalty = 0.00;

        if ($session->jam_mulai_aktual && $session->jam_mulai_terjadwal) {
            $scheduled = Carbon::parse($session->jam_mulai_terjadwal);
            $actual = Carbon::parse($session->jam_mulai_aktual);
            
            // Difference in minutes (signed)
            $diffMinutes = $scheduled->diffInMinutes($actual, false);

            if ($diffMinutes <= 0) {
                if ($diffMinutes <= -10) {
                    $checkinStatus = 'excellent';
                } else {
                    $checkinStatus = 'on_time';
                }
            } elseif ($diffMinutes > 0 && $diffMinutes < 15) {
                $checkinStatus = 'warning';
            } else {
                $checkinStatus = 'penalty';
                $penalty = self::LATE_PENALTY_AMOUNT;
            }
        }

        // 4. Calculate total fee (base + bonus)
        $calculatedFee = $baseRate + $productBonus;

        // 5. Apply override if present
        $finalFee = $session->override_fee !== null ? (float) $session->override_fee : $calculatedFee;

        return [
            'base_rate' => $baseRate,
            'product_bonus' => $productBonus,
            'calculated_fee' => $calculatedFee,
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

                // Process each session to store its details
                foreach ($instructorSessions as $session) {
                    $calc = $this->calculateSessionFee($session);
                    
                    // Update session columns
                    $session->update([
                        'actual_checkin_status' => $calc['actual_checkin_status'],
                        'actual_checkin_penalty' => $calc['actual_checkin_penalty'],
                        'calculated_fee' => $calc['calculated_fee'],
                        'payment_status' => 'processing',
                    ]);

                    $totalBaseFee += $calc['base_rate'];
                    $totalProductBonus += $calc['product_bonus'];
                    $totalPenalty += $calc['actual_checkin_penalty'];
                }

                // Check override fee for sessions
                $actualTotalBase = 0.00;
                foreach ($instructorSessions as $session) {
                    if ($session->override_fee !== null) {
                        $actualTotalBase += (float) $session->override_fee;
                    } else {
                        $actualTotalBase += (float) $session->calculated_fee;
                    }
                }

                $netSalary = max(0.00, $actualTotalBase - $totalPenalty);

                // Create payroll item
                $payrollItem = PayrollItem::create([
                    'payroll_batch_id' => $batch->id,
                    'user_id_instruktur' => $instructorId,
                    'total_sessions' => $totalSessions,
                    'total_base_fee' => $instructorSessions->sum(function($s) {
                        return $s->override_fee !== null ? (float)$s->override_fee - $s->calculated_fee + (float)$s->calculated_fee : (float)$s->calculated_fee;
                    }),
                    'total_product_bonus' => $totalProductBonus,
                    'total_penalty' => $totalPenalty,
                    'total_bonus' => $totalBonus,
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
