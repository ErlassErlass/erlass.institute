<?php

namespace App\Services\Google;

use App\Models\Absensi;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\User;
use App\Services\PunctualityKpiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleSheetsService
{
    protected string $spreadsheetId;
    protected ?array $credentials = null;
    protected ?string $accessToken = null;

    const TAB_KPI = 'Ringkasan_KPI';
    const TAB_LAPORAN = 'Laporan_Mengajar';
    const TAB_JADWAL = 'Jadwal_Sesi_Ekskul';
    const TAB_ABSENSI = 'Absensi_Siswa';
    const TAB_HONOR = 'Rekap_Honor';
    const TAB_REKAP_PERTEMUAN = 'Rekap_Pertemuan_Ekskul';

    public function __construct()
    {
        $this->spreadsheetId = config('services.google.sheets_id', '11wujw9dpKt_LV4gBCTefKL1wWP4Aned-Khv4sGUeGXk');
        $this->loadCredentials();
    }

    /**
     * Set target spreadsheet ID.
     */
    public function setSpreadsheetId(string $id): self
    {
        $this->spreadsheetId = $id;
        return $this;
    }

    public function getSpreadsheetId(): string
    {
        return $this->spreadsheetId;
    }

    /**
     * Load credentials from config / storage / env.
     */
    protected function loadCredentials(): void
    {
        $path = storage_path('app/google/service-account.json');
        if (file_exists($path)) {
            $json = file_get_contents($path);
            $this->credentials = json_decode($json, true);
        } elseif (env('GOOGLE_SERVICE_ACCOUNT_JSON')) {
            $this->credentials = json_decode(env('GOOGLE_SERVICE_ACCOUNT_JSON'), true);
        }
    }

    /**
     * Check if service account credentials are valid.
     */
    public function isConfigured(): bool
    {
        return !empty($this->credentials) 
            && !empty($this->credentials['client_email']) 
            && !empty($this->credentials['private_key']);
    }

    /**
     * Get Service Account Email.
     */
    public function getServiceAccountEmail(): string
    {
        return $this->credentials['client_email'] ?? 'erlass-sync@serviceaccount.google.com';
    }

    /**
     * Obtain OAuth2 Access Token from Google using JWT bearer token.
     */
    public function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $cached = Cache::get('google_sheets_access_token');
        if ($cached) {
            $this->accessToken = $cached;
            return $this->accessToken;
        }

        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $now = time();
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $claim = json_encode([
                'iss' => $this->credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/spreadsheets',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]);

            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claim));

            $signature = '';
            $privateKey = openssl_pkey_get_private($this->credentials['private_key']);
            if (!$privateKey) {
                Log::error('Invalid Google Service Account private key.');
                return null;
            }

            openssl_sign("$base64UrlHeader.$base64UrlClaim", $signature, $privateKey, OPENSSL_ALGO_SHA256);
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            $jwt = "$base64UrlHeader.$base64UrlClaim.$base64UrlSignature";

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->accessToken = $data['access_token'];
                Cache::put('google_sheets_access_token', $this->accessToken, ($data['expires_in'] ?? 3500) - 100);
                return $this->accessToken;
            } else {
                Log::error('Failed to get Google OAuth2 Token: ' . $response->body());
                return null;
            }
        } catch (\Throwable $e) {
            Log::error('Exception generating Google OAuth2 token: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ensure all 5 standard tabs exist in the spreadsheet.
     */
    public function ensureTabsExist(): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Service Account belum dikonfigurasi.'];
        }

        try {
            // Get current spreadsheet metadata
            $response = Http::withToken($token)->get("https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}");
            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Gagal mengakses Spreadsheet. Pastikan ID benar dan telah di-share ke ' . $this->getServiceAccountEmail()];
            }

            $meta = $response->json();
            $existingSheets = collect($meta['sheets'] ?? [])->pluck('properties.title')->toArray();

            $requiredTabs = [
                self::TAB_KPI,
                self::TAB_LAPORAN,
                self::TAB_JADWAL,
                self::TAB_ABSENSI,
                self::TAB_HONOR,
                self::TAB_REKAP_PERTEMUAN,
            ];

            $requests = [];
            foreach ($requiredTabs as $tabTitle) {
                if (!in_array($tabTitle, $existingSheets)) {
                    $requests[] = [
                        'addSheet' => [
                            'properties' => [
                                'title' => $tabTitle,
                                'gridProperties' => [
                                    'frozenRowCount' => 1,
                                ],
                            ],
                        ],
                    ];
                }
            }

            if (!empty($requests)) {
                $batchResponse = Http::withToken($token)->post("https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}:batchUpdate", [
                    'requests' => $requests,
                ]);

                if (!$batchResponse->successful()) {
                    Log::warning('Batch update sheet creation partial fail: ' . $batchResponse->body());
                }
            }

            return ['success' => true, 'existing_tabs' => array_unique(array_merge($existingSheets, $requiredTabs))];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Execute a Full Initial Sync of all 6 tabs.
     */
    public function syncAllData(): array
    {
        $this->ensureTabsExist();
        $token = $this->getAccessToken();

        $results = [
            self::TAB_KPI => $this->syncTabKpi($token),
            self::TAB_LAPORAN => $this->syncTabLaporan($token),
            self::TAB_JADWAL => $this->syncTabJadwal($token),
            self::TAB_ABSENSI => $this->syncTabAbsensi($token),
            self::TAB_HONOR => $this->syncTabHonor($token),
            self::TAB_REKAP_PERTEMUAN => $this->syncTabRekapPertemuan($token),
        ];

        Cache::put('google_sheets_last_sync', now()->toDateTimeString(), 86400 * 30);
        Cache::put('google_sheets_sync_summary', $results, 86400 * 30);

        return [
            'success' => true,
            'timestamp' => now()->toDateTimeString(),
            'spreadsheet_url' => "https://docs.google.com/spreadsheets/d/{$this->spreadsheetId}/edit",
            'results' => $results,
        ];
    }

    /**
     * Tab 1: Ringkasan KPI Instruktur
     */
    public function syncTabKpi(?string $token = null): array
    {
        $headers = [
            'ID Instruktur', 'Nama Lengkap', 'Email', 'No Telepon', 
            'Total Sesi Terjadwal', 'Total Sesi Selesai', 
            'Laporan Tepat Waktu', 'Keterlambatan Laporan', 'Keterlambatan Hadir GPS',
            'Punctuality Rate (%)', 'Status Disiplin', 'Waktu Update Terakhir'
        ];

        $instructors = User::where('role', 'instruktur')->orderBy('nama_lengkap')->get();
        $kpiService = app(PunctualityKpiService::class);

        $rows = [$headers];

        foreach ($instructors as $inst) {
            $kpi = $kpiService->getPersonalKpi($inst);
            $totalTerjadwal = EkstrakurikulerSession::where('user_id_instruktur', $inst->id)->count();

            $rows[] = [
                $inst->id,
                $inst->nama_lengkap ?? $inst->name,
                $inst->email,
                $inst->no_telepon ?? $inst->phone ?? '-',
                $totalTerjadwal,
                $kpi['total_laporan'] ?? 0,
                $kpi['on_time_count'] ?? 0,
                $kpi['late_report_count'] ?? 0,
                $kpi['late_arrival_count'] ?? 0,
                ($kpi['punctuality_rate'] ?? 100) . '%',
                $kpi['status_label'] ?? 'Sangat Disiplin',
                now()->toDateTimeString(),
            ];
        }

        return $this->writeTab(self::TAB_KPI, $rows, $token);
    }

    /**
     * Tab 2: Laporan Mengajar
     */
    public function syncTabLaporan(?string $token = null): array
    {
        $headers = [
            'ID Laporan', 'ID Sesi', 'Tanggal Sesi', 'Jam Sesi', 
            'Nama Sekolah', 'Program Ekskul', 'Rombel', 'Pertemuan Ke', 
            'Nama Instruktur', 'Nama Asisten', 'Waktu Submit Laporan', 
            'Status Keterlambatan', 'Alasan Kendala', 'Status Approval Admin', 
            'Jml Hadir', 'Jml Tidak Hadir', 'Refleksi Guru', 'Catatan Admin'
        ];

        $reports = LaporanMengajar::with(['session.ekstrakurikuler.sekolah', 'session.rombel', 'instruktur', 'asisten'])
            ->orderBy('id', 'asc')
            ->get();

        $rows = [$headers];

        foreach ($reports as $r) {
            $session = $r->session;
            $ekskul = $session?->ekstrakurikuler;
            $sekolah = $ekskul?->sekolah?->namasekolah ?? $r->sekolah_nama ?? $r->sekolah_kodlan ?? 'N/A';
            $program = $ekskul?->nama_ekskul ?: ($ekskul?->kategori_program ?? $r->kategori_pengajaran ?? '-');
            $rombel = $session?->rombel?->nama_rombel ?? $r->rombel ?? 'Rombel 1';
            $pertemuan = $session?->nomor_pertemuan ?? $r->pertemuan_ke ?? 1;

            $meta = $r->metadata_json ?? [];
            $approvalStatus = $meta['status_approval_kendala'] ?? ($r->isSevereLate() ? 'pending_approval' : 'approved');
            $alasanKendala = $meta['alasan_kendala_keterlambatan'] ?? '-';
            $catatanApproval = $meta['catatan_approval'] ?? '-';

            $rows[] = [
                $r->id,
                $session?->id ?? '-',
                $r->jadwal_mengajar ? Carbon::parse($r->jadwal_mengajar)->toDateString() : '-',
                ($r->jam_mulai ? Carbon::parse($r->jam_mulai)->format('H:i') : '-') . ' - ' . ($r->jam_selesai ? Carbon::parse($r->jam_selesai)->format('H:i') : '-'),
                $sekolah,
                $program,
                $rombel,
                $pertemuan,
                $r->instruktur?->nama_lengkap ?? $r->instruktur?->name ?? '-',
                $r->asisten?->nama_lengkap ?? $r->asisten?->name ?? '-',
                $r->created_at ? $r->created_at->toDateTimeString() : '-',
                $r->is_late ? 'Terlambat' : 'Tepat Waktu',
                $alasanKendala,
                ucfirst($approvalStatus),
                $r->jumlah_siswa_hadir ?? 0,
                $r->jumlah_siswa_tidak_hadir ?? 0,
                $r->refleksi_capaian ?? $r->refleksi_siswa ?? '-',
                $catatanApproval,
            ];
        }

        return $this->writeTab(self::TAB_LAPORAN, $rows, $token);
    }

    /**
     * Tab 3: Jadwal Sesi Ekskul
     */
    public function syncTabJadwal(?string $token = null): array
    {
        $headers = [
            'ID Sesi', 'ID Ekskul', 'Nama Sekolah', 'Program Ekskul', 
            'Rombel', 'Pertemuan Ke', 'Hari', 'Tanggal Terjadwal', 
            'Jam Mulai Jadwal', 'Jam Selesai Jadwal', 'Instruktur Terjadwal', 
            'Status Sesi', 'Jam Checkin Aktual', 'Checkin Status', 'Laporan ID'
        ];

        $sessions = EkstrakurikulerSession::with(['ekstrakurikuler.sekolah', 'rombel', 'instruktur', 'laporanMengajar'])
            ->orderBy('tanggal_terjadwal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $rows = [$headers];

        foreach ($sessions as $s) {
            $ekskul = $s->ekstrakurikuler;
            $sekolah = $ekskul?->sekolah?->namasekolah ?? $ekskul?->sekolah_kodlan ?? 'N/A';
            $program = $ekskul?->nama_ekskul ?: ($ekskul?->kategori_program ?? '-');
            $rombel = $s->rombel?->nama_rombel ?? 'Rombel 1';

            $rows[] = [
                $s->id,
                $s->ekstrakurikuler_id,
                $sekolah,
                $program,
                $rombel,
                $s->nomor_pertemuan,
                ucfirst($s->rombel?->hari ?? '-'),
                $s->tanggal_terjadwal ? Carbon::parse($s->tanggal_terjadwal)->toDateString() : '-',
                $s->jam_mulai_terjadwal ? Carbon::parse($s->jam_mulai_terjadwal)->format('H:i') : '-',
                $s->jam_selesai_terjadwal ? Carbon::parse($s->jam_selesai_terjadwal)->format('H:i') : '-',
                $s->instruktur?->nama_lengkap ?? $s->instruktur?->name ?? '-',
                ucfirst($s->status),
                $s->jam_mulai_aktual ? Carbon::parse($s->jam_mulai_aktual)->format('H:i:s') : '-',
                $s->actual_checkin_status ?: 'Normal',
                $s->laporanMengajar?->id ?? '-',
            ];
        }

        return $this->writeTab(self::TAB_JADWAL, $rows, $token);
    }

    /**
     * Tab 4: Absensi Siswa
     */
    public function syncTabAbsensi(?string $token = null): array
    {
        $headers = [
            'ID Absensi', 'ID Laporan', 'Tanggal Sesi', 'Nama Sekolah', 
            'Program Ekskul', 'Rombel', 'ID Siswa', 'Nama Siswa', 
            'Kelas', 'Status Kehadiran', 'Waktu Dicatat'
        ];

        $attendances = Absensi::with(['siswa', 'laporanMengajar.session.ekstrakurikuler.sekolah', 'laporanMengajar.session.rombel'])
            ->orderBy('id', 'desc')
            ->take(5000)
            ->get();

        $rows = [$headers];

        foreach ($attendances as $a) {
            $laporan = $a->laporanMengajar;
            $session = $laporan?->session;
            $ekskul = $session?->ekstrakurikuler;
            $sekolah = $ekskul?->sekolah?->namasekolah ?? $laporan?->sekolah_kodlan ?? '-';
            $program = $ekskul?->nama_ekskul ?: ($ekskul?->kategori_program ?? '-');
            $rombel = $session?->rombel?->nama_rombel ?? $laporan?->rombel ?? '-';

            $rows[] = [
                $a->id,
                $a->laporan_mengajar_id,
                $laporan?->jadwal_mengajar ? Carbon::parse($laporan->jadwal_mengajar)->toDateString() : ($a->created_at ? $a->created_at->toDateString() : '-'),
                $sekolah,
                $program,
                $rombel,
                $a->siswa_id,
                $a->siswa?->nama_lengkap ?? $a->siswa?->name ?? 'Siswa #' . $a->siswa_id,
                $a->siswa?->kelas ?? '-',
                strtoupper($a->status),
                $a->created_at ? $a->created_at->toDateTimeString() : '-',
            ];
        }

        return $this->writeTab(self::TAB_ABSENSI, $rows, $token);
    }

    /**
     * Tab 5: Rekap Honor
     */
    public function syncTabHonor(?string $token = null): array
    {
        $headers = [
            'Periode Bulan', 'ID Instruktur', 'Nama Instruktur', 
            'Total Sesi Mengajar', 'Sesi Tepat Waktu', 'Sesi Terlambat', 
            'Estimasi Honor Kotor (Rp)', 'Total Potongan Denda (Rp)', 
            'Status ACC Kendala', 'Estimasi Honor Bersih Cair (Rp)', 'Waktu Hitung'
        ];

        $instructors = User::where('role', 'instruktur')->orderBy('nama_lengkap')->get();
        $currentMonth = now()->format('Y-m');

        $rows = [$headers];

        foreach ($instructors as $inst) {
            $reports = LaporanMengajar::where('user_id_instruktur', $inst->id)
                ->where('jadwal_mengajar', 'like', "$currentMonth%")
                ->get();

            $totalSesi = $reports->count();
            $totalLate = $reports->where('is_late', true)->count();
            $onTime = $totalSesi - $totalLate;

            $baseRatePerSession = 75000;
            $honorKotor = $totalSesi * $baseRatePerSession;
            $denda = 0;

            $allApproved = true;
            foreach ($reports as $rep) {
                $meta = $rep->metadata_json ?? [];
                if (($meta['status_approval_kendala'] ?? 'approved') === 'pending_approval') {
                    $allApproved = false;
                }
            }

            $rows[] = [
                $currentMonth,
                $inst->id,
                $inst->nama_lengkap ?? $inst->name,
                $totalSesi,
                $onTime,
                $totalLate,
                $honorKotor,
                $denda,
                $allApproved ? 'Semua ACC (Cair)' : 'Ada Pending Audit',
                $honorKotor - $denda,
                now()->toDateTimeString(),
            ];
        }

        return $this->writeTab(self::TAB_HONOR, $rows, $token);
    }

    /**
     * Tab 6: Rekap Pertemuan Ekskul (Public Portal Feed https://erlass.institute/rekap-pertemuan-ekskul)
     */
    public function syncTabRekapPertemuan(?string $token = null): array
    {
        $headers = [
            'ID Sesi', 'Nama Sekolah', 'Kota / Wilayah', 'Program Ekskul', 
            'Rombel', 'Pertemuan Ke', 'Tanggal Pelaksanaan', 'Nama Instruktur', 
            'Topik / Materi Pengajaran', 'Jml Siswa Hadir', 'Link Cetak Presensi', 
            'Link Foto Kegiatan', 'Link Foto Absensi', 'Link File Project'
        ];

        $sessions = EkstrakurikulerSession::with([
            'rombel.ekstrakurikuler.sekolah',
            'instruktur',
            'laporanMengajar'
        ])
        ->where('status', 'selesai')
        ->orderByDesc('tanggal_pelaksanaan')
        ->orderBy('nomor_pertemuan')
        ->get();

        $rows = [$headers];

        foreach ($sessions as $session) {
            $rombel = $session->rombel;
            $ekskul = $rombel?->ekstrakurikuler;
            $sekolah = $ekskul?->sekolah;
            $laporan = $session->laporanMengajar;
            $instruktur = $session->instruktur;

            $fotoKegiatanUrl = $laporan?->foto_kegiatan ? url('storage/' . ltrim($laporan->foto_kegiatan, '/')) : '-';
            $fotoAbsensiUrl = $laporan?->foto_absensi_siswa ? url('storage/' . ltrim($laporan->foto_absensi_siswa, '/')) : '-';
            $projectUrl = $laporan?->file_project ? url('storage/' . ltrim($laporan->file_project, '/')) : '-';
            $printUrl = route('ekstrakurikuler-session.print-session', ['session' => $session->id]);

            $tanggal = $session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal;

            $rows[] = [
                $session->id,
                $sekolah?->namasekolah ?? '—',
                $sekolah?->kota ?? '—',
                $ekskul?->nama_ekskul ?: ($ekskul?->kategori_program ?? '—'),
                $rombel?->nama_rombel ?? '—',
                $session->nomor_pertemuan ?? '—',
                $tanggal ? $tanggal->format('Y-m-d') : '-',
                $instruktur?->nama_lengkap ?? $instruktur?->name ?? '—',
                $laporan?->materi_pengajaran ?? '—',
                $laporan?->jumlah_siswa_hadir ?? 0,
                $printUrl,
                $fotoKegiatanUrl,
                $fotoAbsensiUrl,
                $projectUrl,
            ];
        }

        return $this->writeTab(self::TAB_REKAP_PERTEMUAN, $rows, $token);
    }

    /**
     * Append a single Laporan row in Realtime.
     */
    public function appendLaporanRealtime(LaporanMengajar $r): bool
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        $session = $r->session;
        $ekskul = $session?->ekstrakurikuler;
        $sekolah = $ekskul?->sekolah?->namasekolah ?? $r->sekolah_nama ?? $r->sekolah_kodlan ?? 'N/A';
        $program = $ekskul?->nama_ekskul ?: ($ekskul?->kategori_program ?? $r->kategori_pengajaran ?? '-');
        $rombel = $session?->rombel?->nama_rombel ?? $r->rombel ?? 'Rombel 1';
        $pertemuan = $session?->nomor_pertemuan ?? $r->pertemuan_ke ?? 1;

        $meta = $r->metadata_json ?? [];
        $approvalStatus = $meta['status_approval_kendala'] ?? ($r->isSevereLate() ? 'pending_approval' : 'approved');
        $alasanKendala = $meta['alasan_kendala_keterlambatan'] ?? '-';
        $catatanApproval = $meta['catatan_approval'] ?? '-';

        $row = [
            $r->id,
            $session?->id ?? '-',
            $r->jadwal_mengajar ? Carbon::parse($r->jadwal_mengajar)->toDateString() : '-',
            ($r->jam_mulai ? Carbon::parse($r->jam_mulai)->format('H:i') : '-') . ' - ' . ($r->jam_selesai ? Carbon::parse($r->jam_selesai)->format('H:i') : '-'),
            $sekolah,
            $program,
            $rombel,
            $pertemuan,
            $r->instruktur?->nama_lengkap ?? $r->instruktur?->name ?? '-',
            $r->asisten?->nama_lengkap ?? $r->asisten?->name ?? '-',
            $r->created_at ? $r->created_at->toDateTimeString() : '-',
            $r->is_late ? 'Terlambat' : 'Tepat Waktu',
            $alasanKendala,
            ucfirst($approvalStatus),
            $r->jumlah_siswa_hadir ?? 0,
            $r->jumlah_siswa_tidak_hadir ?? 0,
            $r->refleksi_capaian ?? $r->refleksi_siswa ?? '-',
            $catatanApproval,
        ];

        return $this->appendRow(self::TAB_LAPORAN, $row, $token);
    }

    /**
     * Helper to overwrite a tab with formatted grid values.
     */
    protected function writeTab(string $tabTitle, array $values, ?string $token): array
    {
        $rowCount = count($values);
        $colCount = !empty($values) ? count($values[0]) : 0;

        if (!$token) {
            Cache::put("google_sheets_data_{$tabTitle}", $values, 86400 * 30);
            return [
                'tab' => $tabTitle,
                'status' => 'cached_locally',
                'rows' => $rowCount,
                'cols' => $colCount,
            ];
        }

        try {
            $range = urlencode("{$tabTitle}!A1");
            $response = Http::withToken($token)
                ->put("https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$range}?valueInputOption=USER_ENTERED", [
                    'range' => "{$tabTitle}!A1",
                    'majorDimension' => 'ROWS',
                    'values' => $values,
                ]);

            if ($response->successful()) {
                Cache::put("google_sheets_data_{$tabTitle}", $values, 86400 * 30);
                return [
                    'tab' => $tabTitle,
                    'status' => 'synced_to_google',
                    'rows' => $rowCount,
                    'cols' => $colCount,
                ];
            } else {
                Log::error("Google Sheet writeTab fail for {$tabTitle}: " . $response->body());
                Cache::put("google_sheets_data_{$tabTitle}", $values, 86400 * 30);
                return [
                    'tab' => $tabTitle,
                    'status' => 'api_error',
                    'error' => $response->body(),
                    'rows' => $rowCount,
                ];
            }
        } catch (\Throwable $e) {
            Log::error("Google Sheet writeTab exception for {$tabTitle}: " . $e->getMessage());
            Cache::put("google_sheets_data_{$tabTitle}", $values, 86400 * 30);
            return [
                'tab' => $tabTitle,
                'status' => 'exception',
                'error' => $e->getMessage(),
                'rows' => $rowCount,
            ];
        }
    }

    /**
     * Helper to append a single row to a tab.
     */
    protected function appendRow(string $tabTitle, array $row, string $token): bool
    {
        try {
            $range = urlencode("{$tabTitle}!A:A");
            $response = Http::withToken($token)
                ->post("https://sheets.googleapis.com/v4/spreadsheets/{$this->spreadsheetId}/values/{$range}:append?valueInputOption=USER_ENTERED", [
                    'range' => "{$tabTitle}!A:A",
                    'majorDimension' => 'ROWS',
                    'values' => [$row],
                ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Append row fail for {$tabTitle}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate CSV string from tab data.
     */
    public function getCsvContent(string $tabTitle): string
    {
        $data = Cache::get("google_sheets_data_{$tabTitle}");
        if (!$data) {
            match ($tabTitle) {
                self::TAB_KPI => $this->syncTabKpi(),
                self::TAB_LAPORAN => $this->syncTabLaporan(),
                self::TAB_JADWAL => $this->syncTabJadwal(),
                self::TAB_ABSENSI => $this->syncTabAbsensi(),
                self::TAB_HONOR => $this->syncTabHonor(),
                self::TAB_REKAP_PERTEMUAN => $this->syncTabRekapPertemuan(),
                default => null,
            };
            $data = Cache::get("google_sheets_data_{$tabTitle}", []);
        }

        $fp = fopen('php://temp', 'r+');
        foreach ($data as $line) {
            fputcsv($fp, $line);
        }
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);

        return $csv;
    }

    /**
     * Get array data of all 6 tabs.
     */
    public function getAllTabsData(): array
    {
        $this->syncTabKpi();
        $this->syncTabLaporan();
        $this->syncTabJadwal();
        $this->syncTabAbsensi();
        $this->syncTabHonor();
        $this->syncTabRekapPertemuan();

        return [
            self::TAB_KPI => Cache::get("google_sheets_data_" . self::TAB_KPI, []),
            self::TAB_LAPORAN => Cache::get("google_sheets_data_" . self::TAB_LAPORAN, []),
            self::TAB_JADWAL => Cache::get("google_sheets_data_" . self::TAB_JADWAL, []),
            self::TAB_ABSENSI => Cache::get("google_sheets_data_" . self::TAB_ABSENSI, []),
            self::TAB_HONOR => Cache::get("google_sheets_data_" . self::TAB_HONOR, []),
            self::TAB_REKAP_PERTEMUAN => Cache::get("google_sheets_data_" . self::TAB_REKAP_PERTEMUAN, []),
        ];
    }
}
