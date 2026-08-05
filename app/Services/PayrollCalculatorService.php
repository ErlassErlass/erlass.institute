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
    public function calculateSessionFee(EkstrakurikulerSession $session): array
    {
        // 1. Tentukan jumlah siswa HADIR (bukan jumlah siswa terdaftar/rombel).
        //    Kebijakan: Honor dihitung berdasarkan jumlah siswa yang HADIR pada sesi tersebut,
        //    diambil dari data absensi laporan mengajar (status = 'hadir').
        //    Fallback ke jumlah_siswa rombel jika data absensi belum tersedia.
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

        // 2. Perhitungan Honorarium Dasar berdasarkan Memo Resmi No. 536/EPI/V/2025
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

        // 6. Perhitungan Biaya Transportasi Operasional sesuai Memo No. 536/EPI/V/2025:
        // - Instruktur Guru Internal Sekolah / Kegiatan di Kantor Erlass: Transport = Rp 0
        // - Jarak >= 10 KM dari Pejaten: (Jarak KM * Rp 350) + Rp 7.500 (sewa kendaraan)
        // - Jarak < 10 KM: Tarif minimal flat Rp 20.000 / Custom Transport Fee Sekolah
        //
        // KEBIJAKAN BARU: Transport dihitung 2x Pulang-Pergi (PP).
        //   Tarif transport satu arah dikalikan 2.
        //   Deduplikasi per sekolah per hari dilakukan di generateMonthlyPayroll().
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
            // Sekolah berjarak < 10 KM dari Pejaten: Uang Transport = Rp 0
            $transportFee = 0.00;
        } elseif ($ekskul && $ekskul->jarak_km !== null && (float)$ekskul->jarak_km >= 10.0) {
            // Sekolah berjarak >= 10 KM dari Pejaten: (Jarak KM x Rp 350 + Rp 7.500 sewa kendaraan) x 2 PP
            $distKm = (float) $ekskul->jarak_km;
            $oneWay = ($distKm * 350.00) + 7500.00;
            $transportFee = $oneWay * 2; // 2x PP (Pulang-Pergi)
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
                ->update(['payment_status' => 'unpaid', 'payroll_item_id' => null]);

            // Ambil seluruh sesi mengajar selesai yang berstatus unpaid dan memiliki laporan lengkap di rentang cutoff
            $sessions = EkstrakurikulerSession::where('payment_status', 'unpaid')
                ->where('status', EkstrakurikulerSession::STATUS_SELESAI)
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_pelaksanaan', [$startDate, $endDate])
                      ->orWhere(function ($subQ) use ($startDate, $endDate) {
                          $subQ->whereNull('tanggal_pelaksanaan')
                               ->whereBetween('tanggal_terjadwal', [$startDate, $endDate]);
                      });
                })
                ->whereHas('laporanMengajar') // Wajib memiliki bukti Laporan Mengajar
                ->whereNotNull('user_id_instruktur')
                ->get();

            if ($sessions->isEmpty()) {
                return 0;
            }

            // Kelompokkan sesi mengajar berdasarkan ID Instruktur
            $groupedSessions = $sessions->groupBy('user_id_instruktur');
            $itemsCount = 0;

            foreach ($groupedSessions as $instructorId => $instructorSessions) {
                $totalSessions = $instructorSessions->count();
                $totalBaseFee = 0.00;
                $totalProductBonus = 0.00;
                $totalPenalty = 0.00;
                $totalBonus = 0.00;
                $totalTransportFee = 0.00;

                // Proses setiap sesi untuk menyimpan rincian kalkulasinya
                foreach ($instructorSessions as $session) {
                    $calc = $this->calculateSessionFee($session);
                    
                    // Update kolom-kolom kalkulasi pada sesi
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

                // Hitung total honor dasar (termasuk nilai override fee) dan total biaya transportasi
                // KEBIJAKAN BARU: Transport hanya dibayar 1x per sekolah per hari.
                //   Jika instruktur mengajar >1 sesi di sekolah yang sama pada hari yang sama,
                //   hanya sesi pertama yang mendapatkan transport, sesi berikutnya = Rp 0.
                $actualTotalBase = 0.00;
                $totalTransportFee = 0.00;
                $transportPaidKeys = []; // Track: "sekolah_kodlan|tanggal" => true

                foreach ($instructorSessions as $session) {
                    if ($session->override_fee !== null) {
                        $actualTotalBase += (float) $session->override_fee;
                    } else {
                        $actualTotalBase += (float) $session->calculated_fee;
                    }

                    // Deduplikasi transport per sekolah per hari
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

                    if (!isset($transportPaidKeys[$transportKey])) {
                        // Pertama kali di sekolah ini pada tanggal ini → bayar transport
                        $totalTransportFee += (float) $session->transport_fee;
                        $transportPaidKeys[$transportKey] = true;
                    } else {
                        // Sudah dibayar transport di sekolah ini hari ini → set transport sesi ini = 0
                        $session->update(['transport_fee' => 0.00]);
                    }
                }

                // Gaji Bersih (Net Salary) = Honor Dasar + Total Transport + Bonus - Total Denda
                $netSalary = max(0.00, $actualTotalBase + $totalTransportFee + $totalBonus - $totalPenalty);

                // Buat entri rincian Payroll Item per Instruktur
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

                // Hubungkan sesi-sesi mengajar ke Payroll Item ini
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
