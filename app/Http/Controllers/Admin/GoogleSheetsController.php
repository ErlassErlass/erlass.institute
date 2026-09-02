<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Google\GoogleSheetsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class GoogleSheetsController extends Controller
{
    protected GoogleSheetsService $sheetsService;

    public function __construct(GoogleSheetsService $sheetsService)
    {
        $this->sheetsService = $sheetsService;
    }

    /**
     * Display Google Sheets integration dashboard.
     */
    public function index()
    {
        $spreadsheetId = $this->sheetsService->getSpreadsheetId();
        $spreadsheetUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/edit";
        $isConfigured = $this->sheetsService->isConfigured();
        $serviceAccountEmail = $this->sheetsService->getServiceAccountEmail();
        $lastSync = Cache::get('google_sheets_last_sync');
        $syncSummary = Cache::get('google_sheets_sync_summary', []);

        $tabs = [
            [
                'key' => GoogleSheetsService::TAB_KPI,
                'name' => '1. Ringkasan KPI Instruktur',
                'description' => 'Rekap performa seluruh instruktur, total sesi selesai, ketepatan waktu, dan tingkat kedisiplinan.',
                'icon' => 'bi-bar-chart-fill text-primary',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_KPI, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_LAPORAN,
                'name' => '2. Laporan Mengajar',
                'description' => 'Daftar seluruh laporan mengajar yang telah disubmit lengkap dengan status kendala, denda, dan honor.',
                'icon' => 'bi-file-earmark-text-fill text-success',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_LAPORAN, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_JADWAL,
                'name' => '3. Jadwal Sesi Ekskul',
                'description' => 'Seluruh jadwal sesi ekstrakurikuler, rombel, jam mulai/selesai, check-in aktual, dan status.',
                'icon' => 'bi-calendar-week-fill text-warning',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_JADWAL, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_ABSENSI,
                'name' => '4. Absensi Siswa',
                'description' => 'Riwayat presensi siswa per sesi (Hadir/Sakit/Izin/Alpha) serta rombel asal dan rombel aktif.',
                'icon' => 'bi-people-fill text-info',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_ABSENSI, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_HONOR,
                'name' => '5. Rekap Honor & Payroll',
                'description' => 'Perhitungan estimasi honor kotor, potongan denda, status ACC kendala, dan honor bersih cair.',
                'icon' => 'bi-cash-coin text-danger',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_HONOR, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_REKAP_PERTEMUAN,
                'name' => '6. Rekap Pertemuan Ekskul',
                'description' => 'Rekapitulasi seluruh sesi pertemuan ekskul publik, materi, link foto, dan link cetak presensi.',
                'icon' => 'bi-journal-check text-purple',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_REKAP_PERTEMUAN, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_PROGRAM_EKSKUL,
                'name' => '7. Daftar Program Ekskul',
                'description' => 'Direktori komprehensif portofolio program ekskul seluruh sekolah, status kontrak, sales PIC, rincian jadwal rombel, progres pertemuan, dan kapasitas siswa.',
                'icon' => 'bi-collection-play-fill text-primary',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_PROGRAM_EKSKUL, [])),
            ],
        ];

        return view('admin.google-sheets.index', compact(
            'spreadsheetId',
            'spreadsheetUrl',
            'isConfigured',
            'serviceAccountEmail',
            'lastSync',
            'syncSummary',
            'tabs'
        ));
    }

    /**
     * Trigger instant Full Sync of all 7 tabs.
     */
    public function syncNow(Request $request)
    {
        try {
            $result = $this->sheetsService->syncAllData();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sinkronisasi seluruh 7 tab ke Google Spreadsheet berhasil!',
                    'data' => $result,
                ]);
            }

            return back()->with('success', 'Sinkronisasi seluruh 7 tab ke Google Spreadsheet berhasil!');
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal sinkronisasi: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Gagal sinkronisasi: ' . $e->getMessage()]);
        }
    }

    /**
     * Update Google Spreadsheet ID or Service Account Credentials.
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'spreadsheet_id' => 'required|string',
            'service_account_json' => 'nullable|file|mimes:json,txt',
        ]);

        if ($request->hasFile('service_account_json')) {
            $dir = storage_path('app/google');
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
            }
            $request->file('service_account_json')->move($dir, 'service-account.json');
        }

        // Cache or persist the custom spreadsheet ID
        Cache::put('custom_google_sheets_id', $request->spreadsheet_id, 86400 * 365);

        return back()->with('success', 'Konfigurasi Google Spreadsheet berhasil diperbarui!');
    }

    /**
     * Direct download CSV export of a specific tab.
     */
    public function exportCsv(string $tab)
    {
        $validTabs = [
            'kpi' => GoogleSheetsService::TAB_KPI,
            'laporan' => GoogleSheetsService::TAB_LAPORAN,
            'jadwal' => GoogleSheetsService::TAB_JADWAL,
            'absensi' => GoogleSheetsService::TAB_ABSENSI,
            'honor' => GoogleSheetsService::TAB_HONOR,
            'rekap_pertemuan' => GoogleSheetsService::TAB_REKAP_PERTEMUAN,
            'Rekap_Pertemuan_Ekskul' => GoogleSheetsService::TAB_REKAP_PERTEMUAN,
            'program' => GoogleSheetsService::TAB_PROGRAM_EKSKUL,
            'program_ekskul' => GoogleSheetsService::TAB_PROGRAM_EKSKUL,
            'Daftar_Program_Ekskul' => GoogleSheetsService::TAB_PROGRAM_EKSKUL,
        ];

        $tabKey = $validTabs[$tab] ?? $tab;
        $csv = $this->sheetsService->getCsvContent($tabKey);
        $filename = 'Export_' . $tabKey . '_' . now()->format('Ymd_His') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * API Feed for Google Apps Script / Webhook Sync.
     */
    public function feed(Request $request)
    {
        $token = $request->query('token');
        $expectedToken = config('services.google.feed_token', 'erlass_sheets_sync_2026');

        if ($token !== $expectedToken) {
            return response()->json(['error' => 'Unauthorized token'], 403);
        }

        $allData = $this->sheetsService->getAllTabsData();

        return response()->json([
            'success' => true,
            'timestamp' => now()->toDateTimeString(),
            'tabs' => $allData,
        ]);
    }
}
