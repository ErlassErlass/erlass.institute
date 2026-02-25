<?php

namespace App\Services;

use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Service untuk menangani logic scheduling ekstrakurikuler
 * yang kompleks dan intelligent.
 */
class SchedulingService
{
    /**
     * Hari libur nasional Indonesia (dapat dikonfigurasi)
     */
    protected array $nationalHolidays = [
        // Format: 'YYYY-MM-DD'
        '2026-01-01', // Tahun Baru Masehi
        '2026-01-16', // Isra Mikraj
        '2026-02-16', // Cuti Bersama Imlek
        '2026-02-17', // Tahun Baru Imlek
        '2026-03-18', // Cuti Bersama Nyepi
        '2026-03-19', // Hari Suci Nyepi
        '2026-03-20', // Cuti Bersama Idul Fitri
        '2026-03-21', // Idul Fitri
        '2026-03-22', // Idul Fitri
        '2026-03-23', // Cuti Bersama Idul Fitri
        '2026-03-24', // Cuti Bersama Idul Fitri
        '2026-04-03', // Wafat Yesus Kristus
        '2026-05-01', // Hari Buruh
        '2026-05-14', // Kenaikan Yesus Kristus
        '2026-05-15', // Cuti Bersama Kenaikan
        '2026-05-27', // Idul Adha
        '2026-05-28', // Cuti Bersama Idul Adha
        '2026-05-31', // Hari Raya Waisak
        '2026-06-01', // Hari Lahir Pancasila
        '2026-06-16', // Tahun Baru Islam
        '2026-08-17', // Hari Kemerdekaan RI
        '2026-08-25', // Maulid Nabi Muhammad SAW
        '2026-12-24', // Cuti Bersama Natal
        '2026-12-25', // Hari Raya Natal
    ];

    /**
     * Generate sessions untuk sebuah rombel dengan logic yang intelligent.
     */
    public function generateSessionsForRombel(EkstrakurikulerRombel $rombel, array $options = []): Collection
    {
        $sessions = collect();

        // Validasi data rombel
        if (! $this->validateRombel($rombel)) {
            throw new \InvalidArgumentException('Data rombel tidak valid untuk generate sessions');
        }

        // Hapus sessions yang sudah ada jika diminta
        if ($options['replace_existing'] ?? false) {
            $this->clearExistingSessions($rombel);
        }

        $sessionDates = $this->calculateSessionDates($rombel, $options);

        foreach ($sessionDates as $index => $date) {
            $sessionNumber = $index + 1;

            $sessionData = [
                'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
                'ekstrakurikuler_rombel_id' => $rombel->id,
                'nomor_pertemuan' => $sessionNumber,
                'tanggal_terjadwal' => $date->toDateString(),
                'jam_mulai_terjadwal' => $rombel->jam_mulai,
                'jam_selesai_terjadwal' => $rombel->jam_selesai,
                'user_id_instruktur' => $rombel->user_id_instruktur,
                'user_id_asisten' => $rombel->user_id_asisten,
                'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];

            $session = EkstrakurikulerSession::create($sessionData);
            $sessions->push($session);
        }

        return $sessions;
    }

    /**
     * Hitung tanggal-tanggal sessions berdasarkan jadwal rombel.
     */
    public function calculateSessionDates(EkstrakurikulerRombel $rombel, array $options = []): Collection
    {
        $dates = collect();
        $currentDate = Carbon::parse($rombel->tanggal_mulai);
        $endDate = Carbon::parse($rombel->tanggal_selesai);

        // Mapping hari ke nomor hari dalam minggu (1=Senin, 7=Minggu)
        $hariMapping = [
            EkstrakurikulerRombel::HARI_SENIN => Carbon::MONDAY,
            EkstrakurikulerRombel::HARI_SELASA => Carbon::TUESDAY,
            EkstrakurikulerRombel::HARI_RABU => Carbon::WEDNESDAY,
            EkstrakurikulerRombel::HARI_KAMIS => Carbon::THURSDAY,
            EkstrakurikulerRombel::HARI_JUMAT => Carbon::FRIDAY,
            EkstrakurikulerRombel::HARI_SABTU => Carbon::SATURDAY,
            EkstrakurikulerRombel::HARI_MINGGU => Carbon::SUNDAY,
        ];

        $targetDayOfWeek = $hariMapping[$rombel->hari] ?? Carbon::FRIDAY;

        // Interval berdasarkan frekuensi
        $intervalDays = match ($rombel->frekuensi) {
            EkstrakurikulerRombel::FREKUENSI_HARIAN => 1,
            EkstrakurikulerRombel::FREKUENSI_MINGGUAN => 7,
            EkstrakurikulerRombel::FREKUENSI_DUA_MINGGU => 14,
            EkstrakurikulerRombel::FREKUENSI_BULANAN => 30,
            default => 7
        };

        // Cari hari pertama yang sesuai dengan jadwal
        while ($currentDate->dayOfWeek !== $targetDayOfWeek && $currentDate->lte($endDate)) {
            $currentDate->addDay();
        }

        $sessionCount = 0;
        $maxSessions = $rombel->total_pertemuan ?? 999;

        // Skip holidays option
        $skipHolidays = $options['skip_holidays'] ?? true;

        // Generate tanggal sessions
        // Generate tanggal sessions
        // Logic Updated: Prioritize meeting total_pertemuan if set. Only stop at endDate if total_pertemuan is not set.
        $isTargetingCount = !empty($rombel->total_pertemuan);

        while ($sessionCount < $maxSessions) {
            // Stop if we passed the end date AND we are not strictly targeting a specific count
            if (!$isTargetingCount && $currentDate->gt($endDate)) {
                break;
            }

            // Skip jika tanggal adalah hari libur
            if ($skipHolidays && $this->isHoliday($currentDate)) {
                $currentDate->addDays($intervalDays);

                continue;
            }

            $dates->push($currentDate->copy());
            $sessionCount++;
            $currentDate->addDays($intervalDays);
        }

        return $dates;
    }

    /**
     * Bulk update sessions untuk mengubah instructor atau waktu.
     */
    public function bulkUpdateSessions(Collection $sessions, array $updates): bool
    {
        $successCount = 0;

        foreach ($sessions as $session) {
            if ($this->canUpdateSession($session)) {
                $updateData = $this->prepareSessionUpdateData($updates);

                if ($session->update($updateData)) {
                    $successCount++;
                }
            }
        }

        return $successCount === $sessions->count();
    }

    /**
     * Assign instructor ke multiple sessions dengan validasi conflict.
     */
    public function assignInstructorToSessions(Collection $sessions, User $instructor, ?User $assistant = null): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'conflicts' => [],
        ];

        foreach ($sessions as $session) {
            $conflicts = $this->checkInstructorConflicts($instructor, $session, $assistant);

            if (empty($conflicts)) {
                $session->update([
                    'user_id_instruktur' => $instructor->id,
                    'user_id_asisten' => $assistant?->id,
                    'updated_by' => auth()->id(),
                ]);
                $results['success']++;
            } else {
                $results['failed']++;
                $results['conflicts'][] = [
                    'session_id' => $session->id,
                    'conflicts' => $conflicts,
                ];
            }
        }

        return $results;
    }

    /**
     * Reschedule sessions ke tanggal baru dengan validasi.
     */
    public function rescheduleSessions(Collection $sessions, array $newSchedule): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($sessions as $session) {
            try {
                if ($session->canReschedule()) {
                    $newDate = Carbon::parse($newSchedule['tanggal_terjadwal']);

                    $session->update([
                        'tanggal_terjadwal' => $newDate->toDateString(),
                        'jam_mulai_terjadwal' => $newSchedule['jam_mulai'] ?? $session->jam_mulai_terjadwal,
                        'jam_selesai_terjadwal' => $newSchedule['jam_selesai'] ?? $session->jam_selesai_terjadwal,
                        'updated_by' => auth()->id(),
                    ]);

                    $results['success']++;
                } else {
                    $results['failed']++;
                    $results['errors'][] = "Session ID {$session->id} tidak dapat direschedule";
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Error pada session ID {$session->id}: ".$e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Cari slot waktu kosong untuk instructor dalam rentang tanggal.
     */
    public function findAvailableSlots(User $instructor, Carbon $startDate, Carbon $endDate, int $durationMinutes = 120): Collection
    {
        $availableSlots = collect();
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dailySlots = $this->findDailyAvailableSlots($instructor, $currentDate, $durationMinutes);
            $availableSlots = $availableSlots->merge($dailySlots);
            $currentDate->addDay();
        }

        return $availableSlots;
    }

    /**
     * Generate laporan scheduling untuk rombel.
     */
    public function generateSchedulingReport(EkstrakurikulerRombel $rombel): array
    {
        $sessions = $rombel->sessions()->orderBy('tanggal_terjadwal')->get();

        return [
            'rombel_info' => [
                'nama' => $rombel->nama_rombel,
                'total_pertemuan' => $rombel->total_pertemuan,
                'periode' => $rombel->tanggal_mulai->format('d/m/Y').' - '.$rombel->tanggal_selesai->format('d/m/Y'),
                'jadwal' => $rombel->hari_label.' '.$rombel->jadwal_waktu,
                'instruktur' => $rombel->instruktur?->name,
                'asisten' => $rombel->asisten?->name,
            ],
            'session_statistics' => [
                'total_sessions' => $sessions->count(),
                'terjadwal' => $sessions->where('status', EkstrakurikulerSession::STATUS_TERJADWAL)->count(),
                'selesai' => $sessions->where('status', EkstrakurikulerSession::STATUS_SELESAI)->count(),
                'dibatalkan' => $sessions->where('status', EkstrakurikulerSession::STATUS_DIBATALKAN)->count(),
                'ditunda' => $sessions->where('status', EkstrakurikulerSession::STATUS_DITUNDA)->count(),
            ],
            'upcoming_sessions' => $sessions->where('status', EkstrakurikulerSession::STATUS_TERJADWAL)
                ->where('tanggal_terjadwal', '>=', now())
                ->take(5)
                ->map(fn ($s) => [
                    'id' => $s->id,
                    'nomor_pertemuan' => $s->nomor_pertemuan,
                    'tanggal' => $s->tanggal_terjadwal->format('d/m/Y'),
                    'waktu' => $s->jadwal_waktu,
                    'instruktur' => $s->instruktur?->name,
                ]),
            'potential_conflicts' => $this->findPotentialConflicts($rombel),
        ];
    }

    /**
     * Validasi data rombel untuk generate sessions.
     */
    public function validateRombel(EkstrakurikulerRombel $rombel): bool
    {
        return $rombel->tanggal_mulai &&
               $rombel->tanggal_selesai &&
               $rombel->hari &&
               $rombel->jam_mulai &&
               $rombel->jam_selesai &&
               $rombel->tanggal_mulai->lte($rombel->tanggal_selesai);
    }

    /**
     * Hapus sessions yang sudah ada (hanya yang belum dimulai).
     */
    public function clearExistingSessions(EkstrakurikulerRombel $rombel): void
    {
        // Use forceDelete to avoid unique constraint violations with soft deleted records
        $rombel->sessions()
            ->where('status', EkstrakurikulerSession::STATUS_TERJADWAL)
            ->forceDelete();
    }

    /**
     * Cek apakah tanggal adalah hari libur.
     */
    public function isHoliday(Carbon $date): bool
    {
        // Cek hari libur nasional
        if (in_array($date->toDateString(), $this->nationalHolidays)) {
            return true;
        }

        // Bisa ditambah logic lain seperti cek hari libur daerah
        // atau hari libur sekolah

        return false;
    }

    /**
     * Cek apakah session dapat diupdate.
     */
    public function canUpdateSession(EkstrakurikulerSession $session): bool
    {
        return in_array($session->status, [
            EkstrakurikulerSession::STATUS_TERJADWAL,
            EkstrakurikulerSession::STATUS_DITUNDA,
        ]);
    }

    /**
     * Prepare data untuk update session.
     */
    public function prepareSessionUpdateData(array $updates): array
    {
        $allowedFields = [
            'tanggal_terjadwal',
            'jam_mulai_terjadwal',
            'jam_selesai_terjadwal',
            'user_id_instruktur',
            'user_id_asisten',
            'topik_materi',
            'catatan',
        ];

        $updateData = array_intersect_key($updates, array_flip($allowedFields));
        $updateData['updated_by'] = auth()->id();

        return $updateData;
    }

    /**
     * Cek konflik jadwal instructor.
     */
    public function checkInstructorConflicts(User $instructor, EkstrakurikulerSession $session, ?User $assistant = null): array
    {
        $conflicts = [];

        // Cek konflik instructor
        $instructorConflicts = EkstrakurikulerSession::where('user_id_instruktur', $instructor->id)
            ->where('id', '!=', $session->id)
            ->where('tanggal_terjadwal', $session->tanggal_terjadwal)
            ->where('status', '!=', EkstrakurikulerSession::STATUS_DIBATALKAN)
            ->where(function ($q) use ($session) {
                $q->whereBetween('jam_mulai_terjadwal', [
                    $session->jam_mulai_terjadwal,
                    $session->jam_selesai_terjadwal,
                ])
                    ->orWhereBetween('jam_selesai_terjadwal', [
                        $session->jam_mulai_terjadwal,
                        $session->jam_selesai_terjadwal,
                    ]);
            })
            ->exists();

        if ($instructorConflicts) {
            $conflicts[] = 'Instructor sudah memiliki jadwal lain pada waktu yang sama';
        }

        // Cek konflik assistant jika ada
        if ($assistant) {
            $assistantConflicts = EkstrakurikulerSession::where('user_id_asisten', $assistant->id)
                ->where('id', '!=', $session->id)
                ->where('tanggal_terjadwal', $session->tanggal_terjadwal)
                ->where('status', '!=', EkstrakurikulerSession::STATUS_DIBATALKAN)
                ->where(function ($q) use ($session) {
                    $q->whereBetween('jam_mulai_terjadwal', [
                        $session->jam_mulai_terjadwal,
                        $session->jam_selesai_terjadwal,
                    ])
                        ->orWhereBetween('jam_selesai_terjadwal', [
                            $session->jam_mulai_terjadwal,
                            $session->jam_selesai_terjadwal,
                        ]);
                })
                ->exists();

            if ($assistantConflicts) {
                $conflicts[] = 'Assistant sudah memiliki jadwal lain pada waktu yang sama';
            }
        }

        return $conflicts;
    }

    /**
     * Cari slot waktu kosong dalam satu hari untuk instructor.
     */
    /**
     * Cari slot waktu kosong dalam satu hari untuk instructor.
     * Menggunakan preferensi waktu_mengajar dari profile jika ada.
     */
    public function findDailyAvailableSlots(User $instructor, Carbon $date, int $durationMinutes): Collection
    {
        $slots = collect();
        
        // Ambil range availability dari profile
        $availabilityRanges = $this->getInstructorAvailabilityRanges($instructor, $date);

        // Ambil semua sessions instructor di hari tersebut (Busy Slots)
        $busySlots = EkstrakurikulerSession::where('user_id_instruktur', $instructor->id)
            ->where('tanggal_terjadwal', $date->toDateString())
            ->where('status', '!=', EkstrakurikulerSession::STATUS_DIBATALKAN)
            ->orderBy('jam_mulai_terjadwal')
            ->get();

        foreach ($availabilityRanges as $range) {
            $workingStart = Carbon::parse($date->toDateString().' '.$range['start']);
            $workingEnd = Carbon::parse($date->toDateString().' '.$range['end']);

            $currentTime = $workingStart->copy();

            // Iterate through busy slots to find gaps WITHIN this availability range
            foreach ($busySlots as $busySlot) {
                $busyStart = Carbon::parse($date->toDateString().' '.$busySlot->jam_mulai_terjadwal->format('H:i'));
                $busyEnd = Carbon::parse($date->toDateString().' '.$busySlot->jam_selesai_terjadwal->format('H:i'));

                // Skip if busy slot is completely outside or before current time
                if ($busyEnd->lte($currentTime) || $busyStart->gte($workingEnd)) {
                    continue;
                }

                // If busy slot overlaps, check for gap before it
                if ($busyStart->gt($currentTime)) {
                    // Ada gap sebelum busy slot ini
                    $gapDuration = $currentTime->diffInMinutes($busyStart);
                    if ($gapDuration >= $durationMinutes) {
                        $slots->push([
                            'date' => $date->toDateString(),
                            'start_time' => $currentTime->format('H:i'),
                            'end_time' => $busyStart->format('H:i'),
                            'duration_available' => $gapDuration,
                        ]);
                    }
                }

                // Move current time to end of busy slot (if it pushes past current)
                if ($busyEnd->gt($currentTime)) {
                    $currentTime = $busyEnd->copy();
                }
            }

            // Cek gap terakhir setelah semua busy slots sampai akhir range ini
            if ($currentTime->lt($workingEnd)) {
                $gapDuration = $currentTime->diffInMinutes($workingEnd);
                if ($gapDuration >= $durationMinutes) {
                    $slots->push([
                        'date' => $date->toDateString(),
                        'start_time' => $currentTime->format('H:i'),
                        'end_time' => $workingEnd->format('H:i'),
                        'duration_available' => $gapDuration,
                    ]);
                }
            }
        }

        return $slots;
    }

    /**
     * Helper: Dapatkan range waktu tersedia berdasarkan profile.
     * Mengubah format checkbox ['08:00', '09:00'] menjadi range [['start'=>'08:00', 'end'=>'10:00']]
     */
    public function getInstructorAvailabilityRanges(User $instructor, Carbon $date): array
    {
        // Load profile
        $profile = $instructor->instructorProfile;
        
        // Default working hours jika tidak ada profile atau tidak ada preferensi
        $defaultRanges = [['start' => '08:00', 'end' => '17:00']];

        if (!$profile || empty($profile->waktu_mengajar)) {
            return $defaultRanges;
        }

        // Mapping hari Carbon ke nama hari Indonesia
        $hariMapping = [
            Carbon::MONDAY => 'Senin',
            Carbon::TUESDAY => 'Selasa',
            Carbon::WEDNESDAY => 'Rabu',
            Carbon::THURSDAY => 'Kamis',
            Carbon::FRIDAY => 'Jumat',
            Carbon::SATURDAY => 'Sabtu',
            Carbon::SUNDAY => 'Minggu',
        ];

        $dayName = $hariMapping[$date->dayOfWeek] ?? '';
        $waktuMengajar = $profile->waktu_mengajar;

        if (empty($dayName) || empty($waktuMengajar[$dayName])) {
            // Jika hari ini tidak ada di preferensi, asumsi TIDAK TERSEDIA (kosong)
            // Atau mau asumsi Full Day? Biasanya jika sudah isi preferensi, kosong = tidak bisa.
            return []; // Strict: Empty means unavailable
        }

        $selectedHours = $waktuMengajar[$dayName]; // Array of ["08:00", "09:00", ...]
        sort($selectedHours);

        $ranges = [];
        $currentStart = null;
        $currentEnd = null;

        foreach ($selectedHours as $hour) {
            $time = Carbon::createFromFormat('H:i', $hour);
            // Asumsi 1 slot = 60 menit
            $slotEnd = $time->copy()->addHour();

            if ($currentStart === null) {
                // Init new range
                $currentStart = $time;
                $currentEnd = $slotEnd;
            } else {
                // Cek kontinuitas
                // Jika jam ini sama dengan previous end, extend range
                if ($time->format('H:i') === $currentEnd->format('H:i')) {
                    $currentEnd = $slotEnd;
                } else {
                    // Gap found, push previous range and start new
                    $ranges[] = [
                        'start' => $currentStart->format('H:i'),
                        'end' => $currentEnd->format('H:i'),
                    ];
                    $currentStart = $time;
                    $currentEnd = $slotEnd;
                }
            }
        }

        if ($currentStart) {
            $ranges[] = [
                'start' => $currentStart->format('H:i'),
                'end' => $currentEnd->format('H:i'),
            ];
        }

        return $ranges;
    }

    /**
     * Cek apakah session berada di luar preferensi waktu instruktur (Soft Conflict).
     */
    public function checkInstructorSoftConflicts(User $instructor, EkstrakurikulerSession $session, ?User $assistant = null): array
    {
        $warnings = [];
        $date = $session->tanggal_terjadwal;
        
        // Cek Instructor
        $ranges = $this->getInstructorAvailabilityRanges($instructor, $date);
        $isWithinPreference = false;

        $sessionStart = Carbon::parse($date->toDateString().' '.$session->jam_mulai_terjadwal->format('H:i'));
        $sessionEnd = Carbon::parse($date->toDateString().' '.$session->jam_selesai_terjadwal->format('H:i'));

        // Jika ranges kosong (instructor tidak centang hari ini), langsung warning
        if (empty($ranges)) {
             $warnings[] = "Instruktur tidak menandai ketersediaan pada hari " . $date->translatedFormat('l');
        } else {
            foreach ($ranges as $range) {
                $prefStart = Carbon::parse($date->toDateString().' '.$range['start']);
                $prefEnd = Carbon::parse($date->toDateString().' '.$range['end']);

                // Cek apakah session sepenuhnya ada di dalam range preferensi ini
                if ($sessionStart->gte($prefStart) && $sessionEnd->lte($prefEnd)) {
                    $isWithinPreference = true;
                    break;
                }
            }

            if (!$isWithinPreference) {
                $warnings[] = "Jadwal ({$sessionStart->format('H:i')} - {$sessionEnd->format('H:i')}) berada di luar preferensi waktu instruktur.";
            }
        }
        
        // Assistant check could be added similarly here if needed

        return $warnings;
    }

    /**
     * Cari potential conflicts dalam rombel.
     */
    public function findPotentialConflicts(EkstrakurikulerRombel $rombel): array
    {
        $conflicts = [];

        // Cek konflik instructor dengan rombel lain
        if ($rombel->user_id_instruktur) {
            $otherRombels = EkstrakurikulerRombel::where('user_id_instruktur', $rombel->user_id_instruktur)
                ->where('id', '!=', $rombel->id)
                ->where('hari', $rombel->hari)
                ->where('status', '!=', EkstrakurikulerRombel::STATUS_DIBATALKAN)
                ->where(function ($q) use ($rombel) {
                    $q->whereBetween('tanggal_mulai', [$rombel->tanggal_mulai, $rombel->tanggal_selesai])
                        ->orWhereBetween('tanggal_selesai', [$rombel->tanggal_mulai, $rombel->tanggal_selesai]);
                })
                ->get();

            foreach ($otherRombels as $otherRombel) {
                $conflicts[] = [
                    'type' => 'instructor_time_conflict',
                    'message' => "Instructor memiliki rombel lain '{$otherRombel->nama_rombel}' di hari yang sama",
                    'related_rombel_id' => $otherRombel->id,
                ];
            }
        }

        return $conflicts;
    }
}
