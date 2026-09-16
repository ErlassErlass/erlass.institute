<?php

namespace App\Services;

use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Notification;
use Carbon\Carbon;

class MilestoneNotificationService
{
    /**
     * Non-teaching / non-conducted statuses that must be excluded from milestone teaching dates.
     */
    const EXCLUDED_STATUSES = [
        EkstrakurikulerSession::STATUS_LIBUR,
        EkstrakurikulerSession::STATUS_DITUNDA,
        EkstrakurikulerSession::STATUS_DIBATALKAN,
        EkstrakurikulerSession::STATUS_TIDAK_HADIR,
    ];

    /**
     * Trigger milestone notification if pertemuan_ke is a multiple of 4 (e.g. 4, 8, 12, 16, 20, 24, 28, 32).
     * Strictly filters out holidays (libur), postponed (ditunda), and non-conducted sessions.
     * Prevents duplicate notifications for the same rombel and milestone.
     */
    public function checkAndTriggerMilestoneNotification(EkstrakurikulerSession $session, LaporanMengajar $laporan): ?Notification
    {
        $pertemuanKe = (int) ($laporan->pertemuan_ke ?: $session->nomor_pertemuan);

        if ($pertemuanKe <= 0 || $pertemuanKe % 4 !== 0) {
            return null;
        }

        // Khusus 21 sekolah dengan sistem pembayaran bulanan langsung oleh sekolah (Sekolah Bayar Instruktur):
        // Notifikasi milestone per-4 pertemuan dinonaktifkan karena menggunakan notifikasi cutoff akhir bulan.
        if ($session->rombel?->ekstrakurikuler?->sekolah?->is_sekolah_bayar_instruktur) {
            return null;
        }

        $rombelId = $session->ekstrakurikuler_rombel_id;

        // Fetch teaching dates for 4 actual completed sessions excluding libur / ditunda / dibatalkan
        $tanggalMengajarList = $this->getTeachingDatesForMilestone($rombelId, $pertemuanKe, $session, $laporan);

        // Syarat mutlak: Harus ada tepat 4 sesi mengajar riil yang selesai
        // Jika ada sesi yang ditunda/libur sehingga baru 1, 2, atau 3 sesi riil yang selesai, milestone BELUM tercapai.
        if (count($tanggalMengajarList) < 4) {
            return null;
        }

        $sekolahNama = $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah 
                    ?? $laporan->sekolah_nama 
                    ?? 'Erlass Institute';
        $kategori = $session->rombel?->ekstrakurikuler?->kategori_program 
                 ?? $laporan->kategori_pengajaran 
                 ?? 'Ekskul';
        $rombelNama = $session->rombel?->nama_rombel ?? $laporan->rombel ?? '-';
        $instrukturNama = $laporan->instruktur?->nama_lengkap 
                        ?? $session->instruktur?->nama_lengkap 
                        ?? 'Instruktur';

        $fotoAbsensiUrl = $laporan->foto_absensi_siswa ? asset('storage/' . $laporan->foto_absensi_siswa) : null;
        $fotoKegiatanUrl = $laporan->foto_kegiatan ? asset('storage/' . $laporan->foto_kegiatan) : null;
        $reportDetailUrl = route('laporan-mengajar.show', $laporan->id);

        $dataPayload = [
            'laporan_id' => $laporan->id,
            'session_id' => $session->id,
            'rombel_id' => $rombelId,
            'sekolah_nama' => $sekolahNama,
            'kategori' => $kategori,
            'rombel' => $rombelNama,
            'instruktur_nama' => $instrukturNama,
            'pertemuan_ke' => $pertemuanKe,
            'jumlah_hadir' => $laporan->jumlah_siswa_hadir,
            'tanggal_mengajar_4' => $tanggalMengajarList,
            'foto_absensi_url' => $fotoAbsensiUrl,
            'foto_kegiatan_url' => $fotoKegiatanUrl,
            'report_detail_url' => $reportDetailUrl,
        ];

        $title = "🔔 Laporan Milestone Pertemuan Ke-{$pertemuanKe} Selesai";
        $message = "{$sekolahNama} — {$kategori} ({$rombelNama}). Instruktur {$instrukturNama} telah menyelesaikan laporan pertemuan ke-{$pertemuanKe}.";

        // Check if a milestone notification for this exact rombel and milestone already exists
        $existingNotifs = Notification::where('type', 'milestone_report')
            ->where(function ($q) use ($rombelId, $pertemuanKe) {
                $q->where('data->rombel_id', $rombelId)
                  ->where('data->pertemuan_ke', $pertemuanKe);
            })
            ->orderBy('id', 'desc')
            ->get();

        if ($existingNotifs->isNotEmpty()) {
            $primary = $existingNotifs->first();
            $primary->update([
                'title' => $title,
                'message' => $message,
                'data' => $dataPayload,
            ]);

            // Purge any redundant extra duplicate notifications
            if ($existingNotifs->count() > 1) {
                $existingNotifs->slice(1)->each->delete();
            }

            return $primary;
        }

        return Notification::create([
            'type' => 'milestone_report',
            'target_roles' => 'admin,webmaster,admin_sistem',
            'title' => $title,
            'message' => $message,
            'data' => $dataPayload,
            'is_read' => false,
        ]);
    }

    /**
     * Get the 4 valid teaching dates for a milestone block, ignoring libur/ditunda/dibatalkan sessions.
     * Strictly verifies that all 4 meetings in the block (e.g. 1..4, 5..8) are completed without libur/ditunda.
     */
    public function getTeachingDatesForMilestone(?int $rombelId, int $pertemuanKe, ?EkstrakurikulerSession $currentSession = null, ?LaporanMengajar $currentLaporan = null): array
    {
        if (!$rombelId || $pertemuanKe <= 0 || $pertemuanKe % 4 !== 0) {
            return [];
        }

        $startPertemuan = $pertemuanKe - 3;
        $endPertemuan = $pertemuanKe;

        // Fetch sessions in this exact 4-meeting window
        $blockSessions = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombelId)
            ->where('nomor_pertemuan', '>=', $startPertemuan)
            ->where('nomor_pertemuan', '<=', $endPertemuan)
            ->with('laporanMengajar')
            ->orderBy('nomor_pertemuan', 'asc')
            ->get();

        if ($blockSessions->count() < 4) {
            return [];
        }

        $tanggalMengajarList = [];
        for ($pNum = $startPertemuan; $pNum <= $endPertemuan; $pNum++) {
            $bSession = $blockSessions->firstWhere('nomor_pertemuan', $pNum);
            if (!$bSession) {
                return [];
            }

            // If any session in this block is libur, ditunda, dibatalkan, or tidak_hadir -> milestone not reached
            if (in_array($bSession->status, self::EXCLUDED_STATUSES)) {
                return [];
            }

            $isCurrent = ($currentSession && $bSession->id === $currentSession->id);
            $hasReport = $bSession->laporanMengajar || ($isCurrent && $currentLaporan);
            $isCompleted = ($bSession->status === EkstrakurikulerSession::STATUS_SELESAI) || $hasReport;

            if (!$isCompleted) {
                return [];
            }

            $tgl = null;
            if ($bSession->laporanMengajar && $bSession->laporanMengajar->jadwal_mengajar) {
                $tgl = Carbon::parse($bSession->laporanMengajar->jadwal_mengajar)->format('d-m-Y');
            } elseif ($isCurrent && $currentLaporan && $currentLaporan->jadwal_mengajar) {
                $tgl = Carbon::parse($currentLaporan->jadwal_mengajar)->format('d-m-Y');
            } elseif ($bSession->tanggal_pelaksanaan) {
                $tgl = Carbon::parse($bSession->tanggal_pelaksanaan)->format('d-m-Y');
            } elseif ($bSession->tanggal_terjadwal) {
                $tgl = Carbon::parse($bSession->tanggal_terjadwal)->format('d-m-Y');
            }

            if (!$tgl) {
                return [];
            }

            $jam = null;
            if ($bSession->laporanMengajar && $bSession->laporanMengajar->jam_mulai && $bSession->laporanMengajar->jam_selesai) {
                $jam = substr($bSession->laporanMengajar->jam_mulai, 0, 5) . ' - ' . substr($bSession->laporanMengajar->jam_selesai, 0, 5);
            } elseif ($isCurrent && $currentLaporan && $currentLaporan->jam_mulai && $currentLaporan->jam_selesai) {
                $jam = substr($currentLaporan->jam_mulai, 0, 5) . ' - ' . substr($currentLaporan->jam_selesai, 0, 5);
            } elseif ($bSession->jam_mulai_aktual && $bSession->jam_selesai_aktual) {
                $jam = substr($bSession->jam_mulai_aktual, 0, 5) . ' - ' . substr($bSession->jam_selesai_aktual, 0, 5);
            } elseif ($bSession->jam_mulai_terjadwal && $bSession->jam_selesai_terjadwal) {
                $jam = substr($bSession->jam_mulai_terjadwal, 0, 5) . ' - ' . substr($bSession->jam_selesai_terjadwal, 0, 5);
            }

            $tanggalMengajarList[] = [
                'pertemuan_ke' => $bSession->nomor_pertemuan,
                'tanggal' => $tgl,
                'jam' => $jam,
            ];
        }

        if (count($tanggalMengajarList) !== 4) {
            return [];
        }

        // Integrity check: if dates are identical between two sessions, ensure they are distinct sessions
        for ($i = 0; $i < 3; $i++) {
            for ($j = $i + 1; $j < 4; $j++) {
                if ($tanggalMengajarList[$i]['tanggal'] === $tanggalMengajarList[$j]['tanggal']) {
                    $sA = $blockSessions->firstWhere('nomor_pertemuan', $tanggalMengajarList[$i]['pertemuan_ke']);
                    $sB = $blockSessions->firstWhere('nomor_pertemuan', $tanggalMengajarList[$j]['pertemuan_ke']);
                    if ($sA && $sB && $sA->id === $sB->id) {
                        return [];
                    }
                }
            }
        }

        return $tanggalMengajarList;
    }

    /**
     * Recalibrate existing milestone notifications in database:
     * - Purges premature notifications (< 4 completed sessions)
     * - Deletes duplicate milestone notifications for the same rombel and milestone
     * - Corrects and updates teaching dates and session times
     */
    public function recalibrateExistingMilestoneNotifications(): array
    {
        $notifications = Notification::where('type', 'milestone_report')->orderBy('id', 'desc')->get();
        
        $deletedCount = 0;
        $duplicatesCount = 0;
        $updatedCount = 0;
        $unchangedCount = 0;

        $seenRombelMilestone = [];

        foreach ($notifications as $notif) {
            $data = $notif->data ?? [];
            $sessionId = $data['session_id'] ?? null;
            $pertemuanKe = (int) ($data['pertemuan_ke'] ?? 0);
            $rombelId = $data['rombel_id'] ?? null;

            if (!$rombelId && $sessionId) {
                $session = EkstrakurikulerSession::find($sessionId);
                $rombelId = $session?->ekstrakurikuler_rombel_id;
            }

            if (!$rombelId || $pertemuanKe <= 0) {
                $notif->delete();
                $deletedCount++;
                continue;
            }

            $key = "{$rombelId}_{$pertemuanKe}";

            // If we already saw a newer/valid notification for this milestone, delete this duplicate
            if (isset($seenRombelMilestone[$key])) {
                $notif->delete();
                $duplicatesCount++;
                continue;
            }

            // Update session / report references to the true milestone session if available
            $targetSession = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombelId)
                ->where('nomor_pertemuan', $pertemuanKe)
                ->with(['laporanMengajar.instruktur', 'instruktur', 'rombel.ekstrakurikuler.sekolah'])
                ->first();

            $sekolah = $targetSession?->rombel?->ekstrakurikuler?->sekolah 
                    ?? (\App\Models\EkstrakurikulerRombel::find($rombelId)?->ekstrakurikuler?->sekolah);

            // Bersihkan notifikasi milestone per-4 pertemuan untuk sekolah bayar instruktur
            // karena sekolah ini menggunakan skema notifikasi cutoff akhir bulan (monthly_school_payout)
            if ($sekolah && $sekolah->is_sekolah_bayar_instruktur) {
                $notif->delete();
                $deletedCount++;
                continue;
            }

            $recalibratedDates = $this->getTeachingDatesForMilestone($rombelId, $pertemuanKe);

            // Jika sesi mengajar riil yang selesai kurang dari 4 (misal karena ada yang libur/ditunda/belum jadwalnya),
            // maka milestone tersebut belum lengkap dan harus dihapus dari daftar notifikasi.
            if (count($recalibratedDates) < 4) {
                $notif->delete();
                $deletedCount++;
                continue;
            }

            $seenRombelMilestone[$key] = $notif->id;

            $changed = false;
            $oldDatesJson = json_encode($data['tanggal_mengajar_4'] ?? []);
            $newDatesJson = json_encode($recalibratedDates);

            if ($oldDatesJson !== $newDatesJson) {
                $data['tanggal_mengajar_4'] = $recalibratedDates;
                $changed = true;
            }

            if ($targetSession) {
                if (($data['session_id'] ?? null) !== $targetSession->id) {
                    $data['session_id'] = $targetSession->id;
                    $changed = true;
                }
                if ($targetSession->laporanMengajar && ($data['laporan_id'] ?? null) !== $targetSession->laporanMengajar->id) {
                    $data['laporan_id'] = $targetSession->laporanMengajar->id;
                    $data['report_detail_url'] = route('laporan-mengajar.show', $targetSession->laporanMengajar->id);
                    $data['jumlah_hadir'] = $targetSession->laporanMengajar->jumlah_siswa_hadir;
                    if ($targetSession->laporanMengajar->foto_absensi_siswa) {
                        $data['foto_absensi_url'] = asset('storage/' . $targetSession->laporanMengajar->foto_absensi_siswa);
                    }
                    if ($targetSession->laporanMengajar->foto_kegiatan) {
                        $data['foto_kegiatan_url'] = asset('storage/' . $targetSession->laporanMengajar->foto_kegiatan);
                    }
                    $changed = true;
                }
                $sekolahNama = $targetSession->rombel?->ekstrakurikuler?->sekolah?->namasekolah;
                if ($sekolahNama && ($data['sekolah_nama'] ?? null) !== $sekolahNama) {
                    $data['sekolah_nama'] = $sekolahNama;
                    $changed = true;
                }
                $instrukturNama = $targetSession->laporanMengajar?->instruktur?->nama_lengkap 
                               ?? $targetSession->instruktur?->nama_lengkap;
                if ($instrukturNama && ($data['instruktur_nama'] ?? null) !== $instrukturNama) {
                    $data['instruktur_nama'] = $instrukturNama;
                    $changed = true;
                }
            }

            $data['rombel_id'] = $rombelId;
            $data['pertemuan_ke'] = $pertemuanKe;

            if ($changed) {
                $notif->data = $data;
                $notif->save();
                $updatedCount++;
            } else {
                $unchangedCount++;
            }
        }

        return [
            'deleted' => $deletedCount,
            'duplicates_purged' => $duplicatesCount,
            'updated' => $updatedCount,
            'unchanged' => $unchangedCount,
            'total_processed' => $notifications->count(),
        ];
    }

    /**
     * Generate or update monthly payout notifications for schools where school pays instructor directly
     * (is_sekolah_bayar_instruktur = true). Cutoff is strictly the last day of the calendar month.
     * Captures all completed/conducted sessions within the calendar month.
     */
    public function generateMonthlySchoolPayoutNotifications(?Carbon $targetMonth = null): array
    {
        $targetMonth = $targetMonth ? $targetMonth->copy() : now();
        $startOfMonth = $targetMonth->copy()->startOfMonth();
        $endOfMonth = $targetMonth->copy()->endOfMonth();
        $monthKey = $targetMonth->format('Y-m');
        $monthLabel = $targetMonth->translatedFormat('F Y');

        // Query sessions belonging to is_sekolah_bayar_instruktur schools within this month
        $sessions = EkstrakurikulerSession::whereHas('rombel.ekstrakurikuler.sekolah', function ($q) {
                $q->where('is_sekolah_bayar_instruktur', true);
            })
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('tanggal_pelaksanaan', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                  ->orWhere(function ($sq) use ($startOfMonth, $endOfMonth) {
                      $sq->whereNull('tanggal_pelaksanaan')
                         ->whereBetween('tanggal_terjadwal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
                  });
            })
            ->with([
                'rombel.ekstrakurikuler.sekolah',
                'instruktur',
                'laporanMengajar.instruktur'
            ])
            ->get();

        // Exclude non-teaching statuses; require completed status or existing report
        $completedSessions = $sessions->filter(function ($s) {
            if (in_array($s->status, self::EXCLUDED_STATUSES)) {
                return false;
            }
            return ($s->status === EkstrakurikulerSession::STATUS_SELESAI) || ($s->laporanMengajar !== null);
        });

        $groupedByRombel = $completedSessions->groupBy('ekstrakurikuler_rombel_id');

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($groupedByRombel as $rombelId => $rSessions) {
            if (!$rombelId || $rSessions->isEmpty()) {
                continue;
            }

            // Sort chronologically by actual teaching date
            $sortedSessions = $rSessions->sort(function ($a, $b) {
                $dateA = $a->laporanMengajar?->jadwal_mengajar ?? $a->tanggal_pelaksanaan ?? $a->tanggal_terjadwal;
                $dateB = $b->laporanMengajar?->jadwal_mengajar ?? $b->tanggal_pelaksanaan ?? $b->tanggal_terjadwal;
                return strcmp($dateA, $dateB);
            })->values();

            $firstSession = $sortedSessions->first();
            $sekolah = $firstSession->rombel?->ekstrakurikuler?->sekolah;
            $sekolahNama = $sekolah?->namasekolah ?? 'Sekolah';
            $kategori = $firstSession->rombel?->ekstrakurikuler?->kategori_program ?? 'Ekskul';
            $rombelNama = $firstSession->rombel?->nama_rombel ?? '-';

            // Collect unique instructor names for this rombel in this month
            $instructorNames = $sortedSessions->map(function ($s) {
                return $s->laporanMengajar?->instruktur?->nama_lengkap ?? $s->instruktur?->nama_lengkap;
            })->filter()->unique()->values();

            $instrukturNama = $instructorNames->isNotEmpty() 
                ? $instructorNames->implode(', ') 
                : 'Instruktur';

            $totalSesi = $sortedSessions->count();
            $totalMinutes = 0;
            $teachingDates = [];

            foreach ($sortedSessions as $s) {
                $tgl = null;
                if ($s->laporanMengajar && $s->laporanMengajar->jadwal_mengajar) {
                    $tgl = Carbon::parse($s->laporanMengajar->jadwal_mengajar)->format('d-m-Y');
                } elseif ($s->tanggal_pelaksanaan) {
                    $tgl = Carbon::parse($s->tanggal_pelaksanaan)->format('d-m-Y');
                } elseif ($s->tanggal_terjadwal) {
                    $tgl = Carbon::parse($s->tanggal_terjadwal)->format('d-m-Y');
                }

                $jam = null;
                if ($s->laporanMengajar && $s->laporanMengajar->jam_mulai && $s->laporanMengajar->jam_selesai) {
                    $jam = substr($s->laporanMengajar->jam_mulai, 0, 5) . ' - ' . substr($s->laporanMengajar->jam_selesai, 0, 5);
                } elseif ($s->jam_mulai_aktual && $s->jam_selesai_aktual) {
                    $jam = substr($s->jam_mulai_aktual, 0, 5) . ' - ' . substr($s->jam_selesai_aktual, 0, 5);
                } elseif ($s->jam_mulai_terjadwal && $s->jam_selesai_terjadwal) {
                    $jam = substr($s->jam_mulai_terjadwal, 0, 5) . ' - ' . substr($s->jam_selesai_terjadwal, 0, 5);
                }

                // Duration in minutes
                $mins = $s->durasi_aktual ?: $s->durasi_terjadwal;
                if (!$mins && $s->jam_mulai_terjadwal && $s->jam_selesai_terjadwal) {
                    $mins = Carbon::parse($s->jam_mulai_terjadwal)->diffInMinutes(Carbon::parse($s->jam_selesai_terjadwal));
                }
                $totalMinutes += ($mins ?: 60);

                $insSession = $s->laporanMengajar?->instruktur?->nama_lengkap ?? $s->instruktur?->nama_lengkap;

                $teachingDates[] = [
                    'session_id' => $s->id,
                    'laporan_id' => $s->laporanMengajar?->id,
                    'pertemuan_ke' => $s->nomor_pertemuan,
                    'tanggal' => $tgl,
                    'jam' => $jam,
                    'instruktur' => $insSession,
                ];
            }

            $hours = floor($totalMinutes / 60);
            $remainingMins = $totalMinutes % 60;
            $totalJamFormatted = $hours > 0 
                ? ($remainingMins > 0 ? "{$hours} Jam {$remainingMins} Menit" : "{$hours} Jam")
                : "{$remainingMins} Menit";

            $reportDetailUrl = route('laporan-mengajar.index', ['search' => $sekolahNama]);

            $dataPayload = [
                'is_priority' => true,
                'priority_level' => 'high',
                'type' => 'monthly_school_payout',
                'badge' => '🔥 PRIORITAS: SEKOLAH BAYAR INSTRUKTUR',
                'bulan_key' => $monthKey,
                'bulan_label' => $monthLabel,
                'cutoff_date' => $endOfMonth->format('d-m-Y'),
                'rombel_id' => $rombelId,
                'sekolah_nama' => $sekolahNama,
                'kategori' => $kategori,
                'rombel' => $rombelNama,
                'instruktur_nama' => $instrukturNama,
                'total_sesi' => $totalSesi,
                'total_jam' => $totalJamFormatted,
                'total_menit' => $totalMinutes,
                'tanggal_mengajar' => $teachingDates,
                'report_detail_url' => $reportDetailUrl,
            ];

            $title = "🔥 [PRIORITAS] Cutoff Akhir Bulan ({$monthLabel}) — {$sekolahNama}";
            $message = "{$sekolahNama} — {$kategori} ({$rombelNama}). Instruktur {$instrukturNama} menyelesaikan {$totalSesi} sesi ({$totalJamFormatted}) pada periode {$monthLabel}. Pembayaran langsung oleh sekolah.";

            $existing = Notification::where('type', 'monthly_school_payout')
                ->where(function ($q) use ($rombelId, $monthKey) {
                    $q->where('data->rombel_id', $rombelId)
                      ->where('data->bulan_key', $monthKey);
                })
                ->first();

            if ($existing) {
                $existing->update([
                    'title' => $title,
                    'message' => $message,
                    'data' => $dataPayload,
                ]);
                $updatedCount++;
            } else {
                Notification::create([
                    'type' => 'monthly_school_payout',
                    'target_roles' => 'admin,webmaster,admin_sistem',
                    'title' => $title,
                    'message' => $message,
                    'data' => $dataPayload,
                    'is_read' => false,
                ]);
                $createdCount++;
            }
        }

        return [
            'month' => $monthKey,
            'month_label' => $monthLabel,
            'created' => $createdCount,
            'updated' => $updatedCount,
            'total_rombels' => count($groupedByRombel),
        ];
    }
}
