<?php

namespace App\Services;

use App\Models\EkstrakurikulerSession;
use App\Models\PayrollBatch;
use App\Models\PayrollItem;
use App\Models\SalaryRate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Layanan Kalkulator Payroll & Kompensasi Instruktur
 * Mengimplementasikan seluruh aturan perhitungan honorarium, transport,
 * kedisiplinan, dan periode cutoff penggajian sesuai Surat Memo Direksi No. 536/EPI/V/2025.
 */
class PayrollCalculatorService
{
    /**
     * Besaran nominal denda keterlambatan check-in (>= 15 menit).
     */
    const LATE_PENALTY_AMOUNT = 25000.00;

    /**
     * Menghitung honorarium, biaya transport, dan status kedisiplinan untuk satu sesi mengajar
     * berdasarkan ketentuan Memo Resmi No. 536/EPI/V/2025 (TAB 2025/2026).
     */
    public function calculateSessionFee(EkstrakurikulerSession $session, string $role = 'utama'): array
    {
        // 1. Tentukan jumlah siswa HADIR (bukan jumlah siswa terdaftar/rombel).
        $studentCount = 0;

        // Prioritas 1: Jumlah siswa HADIR dari absensi laporan mengajar
        if ($session->laporanMengajar) {
            $attendanceCount = $session->laporanMengajar->absensi()->where('status', 'hadir')->count();
            if ($attendanceCount > 0) {
                $studentCount = $attendanceCount;
            }
        }

        // Prioritas 2 (Fallback): Jika data absensi belum ada, gunakan jumlah siswa rombel
        if ($studentCount === 0) {
            $rombel = $session->rombel;
            if ($rombel) {
                $studentCount = (int) ($rombel->jumlah_siswa ?? 0);
                if ($studentCount === 0 && method_exists($rombel, 'siswa')) {
                    $studentCount = $rombel->siswa()->wherePivot('status', 'aktif')->count();
                }
            }
        }

        // Prioritas 3 (Fallback terakhir): Total absensi (hadir + tidak hadir) jika rombel juga kosong
        if ($studentCount === 0 && $session->laporanMengajar) {
            $studentCount = $session->laporanMengajar->absensi()->count();
        }

        // Jika peran adalah Asisten Instruktur: Flat Rp 100.000 / sesi, Transport Rp 0, Denda Rp 0
        if ($role === 'asisten') {
            $baseRate = 100000.00;
            $productBonus = 0.00;
            $calculatedFee = 100000.00;
            $finalFee = $session->override_fee !== null ? (float) $session->override_fee : $calculatedFee;

            return [
                'student_count' => $studentCount,
                'base_rate' => $baseRate,
                'product_bonus' => $productBonus,
                'calculated_fee' => $calculatedFee,
                'transport_fee' => 0.00,
                'actual_checkin_status' => $session->actual_checkin_status ?? 'on_time',
                'actual_checkin_penalty' => 0.00,
                'net_fee' => max(0.00, $finalFee)
            ];
        }

        // 2. Perhitungan Honorarium Dasar Instruktur Utama berdasarkan Memo Resmi No. 536/EPI/V/2025
        $category = '';
        if ($session->laporanMengajar && $session->laporanMengajar->kategori_pengajaran) {
            $category = strtolower($session->laporanMengajar->kategori_pengajaran);
        } elseif ($session->topik_materi) {
            $category = strtolower($session->topik_materi);
        } elseif ($session->ekstrakurikuler && $session->ekstrakurikuler->kategori_program) {
            $category = strtolower($session->ekstrakurikuler->kategori_program);
        }

        $baseRate = 0.00;

        if (str_contains($category, 'sosialisasi')) {
            // Poin No. 6: Honor Sosialisasi bersama Sales = Rp 75.000
            $baseRate = 75000.00;
        } elseif (str_contains($category, 'free trial') || str_contains($category, 'trial class') || str_contains($category, 'trial')) {
            // Poin No. 7: Honor Free Trial / Trial Class (Siswa > 6 => Rp 100.000, Siswa <= 6 => Rp 75.000)
            $baseRate = ($studentCount > 6) ? 100000.00 : 75000.00;
        } elseif (str_contains($category, 'pameran')) {
            // Poin No. 5: Honor Pameran di Sekolah / Kegiatan Luar = Rp 100.000
            $baseRate = 100000.00;
        } elseif (str_contains($category, 'lomba') || str_contains($category, 'pendampingan')) {
            // Poin No. 4: Honor Pendampingan Lomba = Rp 75.000
            $baseRate = 75000.00;
        } elseif (str_contains($category, 'per-pertemuan') || str_contains($category, 'per pertemuan')) {
            // Poin No. 3: Honor Sekolah Pembayaran Per-Pertemuan = Rp 100.000
            $baseRate = 100000.00;
        } else {
            // Poin No. 1: Skala Honor Utama berdasarkan Kuota Jumlah Siswa Rombel
            // - Siswa >= 15 orang: Rp 150.000 / sesi
            // - Siswa 12 - 14 orang: Rp 115.000 / sesi
            // - Siswa 10 - 11 orang: Rp 100.000 / sesi
            // - Siswa 8 - 9 orang: Rp 75.000 / sesi
            // - Siswa < 8 orang: Rp 0 (Pembelajaran Ditunda / Hold)
            if ($studentCount >= 15) {
                $baseRate = 150000.00;
            } elseif ($studentCount >= 12) {
                $baseRate = 115000.00;
            } elseif ($studentCount >= 10) {
                $baseRate = 100000.00;
            } elseif ($studentCount >= 8) {
                $baseRate = 75000.00;
            } else {
                $baseRate = 0.00;
            }
        }

        // Jalur Cadangan (Fallback): Ambil dari tabel master salary_rates jika baseRate bernilai 0 dan siswa >= 8
        if ($baseRate === 0.00 && $studentCount >= 8) {
            $instructor = $session->instruktur;
            $level = strtolower($instructor->instructorProfile->level ?? 'junior');
            $rateSetting = SalaryRate::where('level', $level)
                ->where(function ($query) {
                    $query->whereNull('product_category')->orWhere('product_category', '');
                })->first();
            $baseRate = $rateSetting ? (float) $rateSetting->base_rate : 100000.00;
        }

        // 3. Perhitungan Bonus Produk (jika ada skema bonus kategori produk)
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

        // 4. Pemeriksaan Kedisiplinan Check-in & Perhitungan Denda Keterlambatan
        $checkinStatus = 'on_time';
        $penalty = 0.00;

        if ($session->jam_mulai_aktual && $session->jam_mulai_terjadwal) {
            $scheduled = Carbon::parse($session->jam_mulai_terjadwal);
            $actual = Carbon::parse($session->jam_mulai_aktual);
            $diffMinutes = $scheduled->diffInMinutes($actual, false);

            if ($diffMinutes <= 0) {
                $checkinStatus = ($diffMinutes <= -10) ? 'excellent' : 'on_time';
            } elseif ($diffMinutes > 0 && $diffMinutes < 15) {
                $checkinStatus = 'warning'; // Toleransi 15 menit pertama (bebas denda)
            } else {
                $checkinStatus = 'penalty'; // Terlambat >= 15 menit (dikenakan denda Rp 25.000)
                $penalty = self::LATE_PENALTY_AMOUNT;
            }
        }

        // 5. Total Honorarium (Honor Dasar + Bonus Produk)
        $calculatedFee = $baseRate + $productBonus;

        // 6. Perhitungan Biaya Transportasi Operasional sesuai Memo No. 536/EPI/V/2025 & Kebijakan Baru:
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
        } elseif ($ekskul && $ekskul->jarak_km !== null && (float)$ekskul->jarak_km < 10.0 && (float)$ekskul->jarak_km > 0) {
            // Sekolah berjarak < 10 KM dari Pejaten: Sewa Kendaraan saja = Rp 7.500 (tanpa komponen bensin)
            $transportFee = 7500.00;
        } elseif ($ekskul && $ekskul->jarak_km !== null && (float)$ekskul->jarak_km >= 10.0) {
            // Sekolah berjarak >= 10 KM dari Pejaten: (Jarak KM x Rp 350 x 2 PP) + Rp 7.500 (Sewa Kendaraan)
            $distKm = (float) $ekskul->jarak_km;
            $bensinPP = $distKm * 350.00 * 2; // Bensin 2x PP (Pulang-Pergi)
            $sewaKendaraan = 7500.00; // Fixed flat fee 1x
            $transportFee = $bensinPP + $sewaKendaraan;
        } elseif ($sekolah && $sekolah->kustom_transport_fee !== null) {
            $transportFee = (float)$sekolah->kustom_transport_fee * 2; // 2x PP
        } else {
            $transportFee = 0.00;
        }

        // 7. Penggunaan nilai koreksi manual (Override Fee) jika Admin mengisi nilai khusus
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
     * Mengompilasi seluruh sesi mengajar selesai yang belum dibayar dalam rentang Cutoff ke Batch Payroll.
     */
    public function generateMonthlyPayroll(PayrollBatch $batch): int
    {
        return DB::transaction(function () use ($batch) {
            // Ambil tanggal periode batch (misal: 2026-07-01)
            $period = Carbon::parse($batch->periode);

            // Rentang Waktu Cutoff: Tanggal 11 Bulan Lalu s.d. Tanggal 10 Bulan Berjalan
            // Contoh Batch Periode Juli 2026 => 11 Juni 2026 00:00:00 s.d. 10 Juli 2026 23:59:59
            $startDate = $period->copy()->subMonth()->day(11)->startOfDay();
            $endDate = $period->copy()->day(10)->endOfDay();

            // Auto-Link seluruh Laporan Mengajar mandiri di rentang cutoff agar 100% terekap di payroll
            $standaloneReports = \App\Models\LaporanMengajar::whereNull('ekstrakurikuler_session_id')
                ->whereBetween('jadwal_mengajar', [$startDate, $endDate])
                ->whereNotNull('user_id_instruktur')
                ->get();

            foreach ($standaloneReports as $report) {
                $report->ensureSessionLinked();
            }

            // Clean up any orphaned processing sessions without batch items
            EkstrakurikulerSession::where('payment_status', 'processing')
                ->whereDoesntHave('payrollItem')
                ->whereDoesntHave('payrollItemSessions')
                ->update(['payment_status' => 'unpaid', 'payroll_item_id' => null]);

            // Ambil seluruh sesi mengajar selesai yang berstatus unpaid dan memiliki laporan lengkap di rentang cutoff (termasuk carry-over)
            $sessions = EkstrakurikulerSession::where('payment_status', 'unpaid')
                ->where('status', EkstrakurikulerSession::STATUS_SELESAI)
                ->where(function ($q) use ($startDate, $endDate) {
                    // Sesi dalam rentang cutoff normal
                    $q->whereBetween('tanggal_pelaksanaan', [$startDate, $endDate])
                      ->orWhere(function ($subQ) use ($startDate, $endDate) {
                          $subQ->whereNull('tanggal_pelaksanaan')
                               ->whereBetween('tanggal_terjadwal', [$startDate, $endDate]);
                      })
                      // Carry-over: Sesi lampau yang baru dibuatkan laporannya pada rentang cutoff berjalan
                      ->orWhere(function ($carryQ) use ($startDate, $endDate) {
                          $carryQ->where('tanggal_pelaksanaan', '<', $startDate)
                                 ->whereHas('laporanMengajar', function ($lq) use ($startDate, $endDate) {
                                     $lq->whereBetween('created_at', [$startDate, $endDate]);
                                 });
                      });
                })
                ->whereHas('laporanMengajar', function ($lq) {
                    // Hanya sertakan laporan yang sudah disetujui kendalanya (atau laporan normal yang tidak pending/rejected)
                    $lq->where(function ($sub) {
                        $sub->whereNull('metadata_json->status_approval_kendala')
                            ->orWhere('metadata_json->status_approval_kendala', 'approved');
                    });
                })
                ->where(function ($q) {
                    $q->whereNotNull('user_id_instruktur')
                      ->orWhereNotNull('user_id_asisten');
                })
                ->get();

            if ($sessions->isEmpty()) {
                return 0;
            }

            // Kumpulkan penugasan (duties) per instruktur: role 'utama' dan role 'asisten'
            $instructorDuties = collect();

            foreach ($sessions as $session) {
                // Instruktur Utama
                if ($session->user_id_instruktur) {
                    $instructorDuties->push([
                        'user_id' => $session->user_id_instruktur,
                        'role' => 'utama',
                        'session' => $session,
                    ]);
                }

                // Asisten Instruktur
                if ($session->user_id_asisten) {
                    $instructorDuties->push([
                        'user_id' => $session->user_id_asisten,
                        'role' => 'asisten',
                        'session' => $session,
                    ]);
                }
            }

            // Kelompokkan penugasan berdasarkan ID Pengajar
            $groupedDuties = $instructorDuties->groupBy('user_id');
            $itemsCount = 0;

            foreach ($groupedDuties as $userId => $duties) {
                $utamaDuties = $duties->where('role', 'utama');
                $asistenDuties = $duties->where('role', 'asisten');

                $totalUtamaSessions = $utamaDuties->count();
                $totalAsistenSessions = $asistenDuties->count();
                $totalSessions = $totalUtamaSessions + $totalAsistenSessions;

                $totalBaseFee = 0.00;
                $totalAsistenFee = 0.00;
                $totalProductBonus = 0.00;
                $totalPenalty = 0.00;
                $totalTransportFee = 0.00;

                $processedUtama = [];
                $processedAsisten = [];
                $transportPaidKeys = []; // Track deduplikasi transport: "sekolah_kodlan|tanggal" => true

                // 1. Proses Penugasan Instruktur Utama
                foreach ($utamaDuties as $duty) {
                    $session = $duty['session'];
                    $calc = $this->calculateSessionFee($session, 'utama');

                    $sessionDate = $session->tanggal_pelaksanaan
                        ? Carbon::parse($session->tanggal_pelaksanaan)->toDateString()
                        : ($session->tanggal_terjadwal
                            ? Carbon::parse($session->tanggal_terjadwal)->toDateString()
                            : 'unknown');

                    $sekolahKey = 'default';
                    if ($session->ekstrakurikuler && $session->ekstrakurikuler->sekolah_kodlan) {
                        $sekolahKey = $session->ekstrakurikuler->sekolah_kodlan;
                    }

                    $transportKey = $sekolahKey . '|' . $sessionDate;
                    $sessionTransport = 0.00;

                    if (!isset($transportPaidKeys[$transportKey])) {
                        $sessionTransport = (float) $calc['transport_fee'];
                        $transportPaidKeys[$transportKey] = true;
                    }

                    $sessionBaseFee = $session->override_fee !== null ? (float) $session->override_fee : (float) $calc['calculated_fee'];

                    $session->update([
                        'actual_checkin_status' => $calc['actual_checkin_status'],
                        'actual_checkin_penalty' => $calc['actual_checkin_penalty'],
                        'calculated_fee' => $calc['calculated_fee'],
                        'transport_fee' => $sessionTransport,
                        'payment_status' => 'processing',
                    ]);

                    $totalBaseFee += $sessionBaseFee;
                    $totalProductBonus += (float) $calc['product_bonus'];
                    $totalPenalty += (float) $calc['actual_checkin_penalty'];
                    $totalTransportFee += $sessionTransport;

                    $processedUtama[] = [
                        'session' => $session,
                        'base_fee' => $sessionBaseFee,
                        'transport_fee' => $sessionTransport,
                        'penalty_fee' => (float) $calc['actual_checkin_penalty'],
                        'bonus_fee' => (float) $calc['product_bonus'],
                        'net_fee' => max(0.00, $sessionBaseFee + $sessionTransport - (float) $calc['actual_checkin_penalty']),
                        'override_fee' => $session->override_fee,
                    ];
                }

                // 2. Proses Penugasan Asisten Instruktur (Flat Rp 100.000 / sesi, Transport Rp 0)
                foreach ($asistenDuties as $duty) {
                    $session = $duty['session'];
                    $asistenFee = 100000.00;
                    $totalAsistenFee += $asistenFee;

                    $session->update([
                        'payment_status' => 'processing',
                    ]);

                    $processedAsisten[] = [
                        'session' => $session,
                        'base_fee' => $asistenFee,
                        'transport_fee' => 0.00,
                        'penalty_fee' => 0.00,
                        'bonus_fee' => 0.00,
                        'net_fee' => $asistenFee,
                        'override_fee' => null,
                    ];
                }

                // Total Penerimaan Kotor (Gross) Sesuai Slip Resmi Erlass
                $totalGrossSalary = $totalBaseFee + $totalAsistenFee + $totalProductBonus + $totalTransportFee;

                // Potongan Pajak 2.5% dari Total Penerimaan Kotor
                $taxRate = 2.50;
                $taxAmount = round($totalGrossSalary * 0.025);

                // Gaji Bersih = Penerimaan Bersih (Gross * 0.975) - Total Denda Keterlambatan
                $netSalary = max(0.00, round($totalGrossSalary * 0.975) - $totalPenalty);

                // Buat entri PayrollItem untuk instruktur/asisten ini
                $payrollItem = PayrollItem::create([
                    'payroll_batch_id' => $batch->id,
                    'user_id_instruktur' => $userId,
                    'total_sessions' => $totalSessions,
                    'total_sessions_utama' => $totalUtamaSessions,
                    'total_sessions_asisten' => $totalAsistenSessions,
                    'total_base_fee' => $totalBaseFee,
                    'total_asisten_fee' => $totalAsistenFee,
                    'total_product_bonus' => $totalProductBonus,
                    'total_transport_fee' => $totalTransportFee,
                    'total_gross_salary' => $totalGrossSalary,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'total_penalty' => $totalPenalty,
                    'total_bonus' => 0.00,
                    'net_salary' => $netSalary,
                    'status' => 'pending',
                ]);

                // Simpan rincian sesi ke tabel pivot payroll_item_session
                foreach ($processedUtama as $itemData) {
                    \App\Models\PayrollItemSession::create([
                        'payroll_item_id' => $payrollItem->id,
                        'ekstrakurikuler_session_id' => $itemData['session']->id,
                        'user_id' => $userId,
                        'role' => 'utama',
                        'base_fee' => $itemData['base_fee'],
                        'transport_fee' => $itemData['transport_fee'],
                        'penalty_fee' => $itemData['penalty_fee'],
                        'bonus_fee' => $itemData['bonus_fee'],
                        'net_fee' => $itemData['net_fee'],
                        'override_fee' => $itemData['override_fee'],
                    ]);

                    // Update legacy single foreign key if null
                    if (!$itemData['session']->payroll_item_id) {
                        $itemData['session']->update(['payroll_item_id' => $payrollItem->id]);
                    }
                }

                foreach ($processedAsisten as $itemData) {
                    \App\Models\PayrollItemSession::create([
                        'payroll_item_id' => $payrollItem->id,
                        'ekstrakurikuler_session_id' => $itemData['session']->id,
                        'user_id' => $userId,
                        'role' => 'asisten',
                        'base_fee' => $itemData['base_fee'],
                        'transport_fee' => 0.00,
                        'penalty_fee' => 0.00,
                        'bonus_fee' => 0.00,
                        'net_fee' => $itemData['net_fee'],
                        'override_fee' => null,
                    ]);

                    // Update legacy single foreign key if null
                    if (!$itemData['session']->payroll_item_id) {
                        $itemData['session']->update(['payroll_item_id' => $payrollItem->id]);
                    }
                }

                $itemsCount++;
            }

            return $itemsCount;
        });
    }
}

