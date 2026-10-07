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
                'description' => 'Direktori komprehensif portofolio program ekskul dipisah per rombel, mencakup instruktur bertugas, jadwal belajar, progres pertemuan rombel, dan kapasitas siswa.',
                'icon' => 'bi-collection-play-fill text-primary',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_PROGRAM_EKSKUL, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_REKAP_HONOR_INSTRUKTUR,
                'name' => '8. Rekap Honor Instruktur',
                'description' => 'Rekapitulasi per instruktur: nama asisten, tanggal & jam submit laporan terbaru, sekolah, program ekskul, rombel, total siswa hadir, honor final (net), nomor rekening, dan NIK.',
                'icon' => 'bi-person-badge-fill text-success',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_REKAP_HONOR_INSTRUKTUR, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_PROFIL_INSTRUKTUR,
                'name' => '9. Profil & Rekening Instruktur',
                'description' => 'Master profil seluruh instruktur & asisten: NIK, NPWP, 4 kolom rekening (Bank, No Rek, Atas Nama, & Rekening Gabungan), kontak, peran mengajar, serta kompetensi 1 & 2.',
                'icon' => 'bi-person-vcard-fill text-primary',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_PROFIL_INSTRUKTUR, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_DATA_SISWA,
                'name' => '10. Data Siswa',
                'description' => 'Direktori master seluruh siswa aktif ekstrakurikuler lengkap dengan NISN, nama lengkap, sekolah mitra, kelas, program, dan nama ekskul.',
                'icon' => 'bi-mortarboard-fill text-warning',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_DATA_SISWA, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN,
                'name' => '11. Monitoring Belum Laporan',
                'description' => 'Daftar seluruh sesi ekskul yang sudah tiba/lewat jadwalnya namun belum disubmit laporan mengajarnya oleh instruktur.',
                'icon' => 'bi-exclamation-octagon-fill text-danger',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN, [])),
                'has_excel' => true,
            ],
            [
                'key' => GoogleSheetsService::TAB_INVOICE,
                'name' => '12. Rekap Invoice & Penagihan',
                'description' => 'Monitoring status penagihan invoice: draft menggantung, persetujuan Gate 1 (Operasional / Dinda & Novandi) & Gate 2 (Akunting / Rendy), aging hari, rincian rombel, dan siswa billable.',
                'icon' => 'bi-receipt-cutoff text-purple',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_INVOICE, [])),
            ],
            [
                'key' => GoogleSheetsService::TAB_INVOICE_MARKETING,
                'name' => '13. Detail Invoice Marketing (Pivot Ready)',
                'description' => 'Data granular per item rombel/program untuk Pivot Table Marketing: Group Leader, Sales, Kode Sales, Area, Sekolah, Billable Efektif murni angka, Aging Hari murni angka, status verifikasi.',
                'icon' => 'bi-pie-chart-fill text-success',
                'cached_rows' => count(Cache::get('google_sheets_data_' . GoogleSheetsService::TAB_INVOICE_MARKETING, [])),
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
     * Trigger instant Full Sync of all 13 tabs.
     */
    public function syncNow(Request $request)
    {
        try {
            $result = $this->sheetsService->syncAllData();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sinkronisasi seluruh 13 tab ke Google Spreadsheet berhasil!',
                    'data' => $result,
                ]);
            }

            return back()->with('success', 'Sinkronisasi seluruh 13 tab ke Google Spreadsheet berhasil!');
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
            'rekap_honor_instruktur' => GoogleSheetsService::TAB_REKAP_HONOR_INSTRUKTUR,
            'Rekap_Honor_Instruktur' => GoogleSheetsService::TAB_REKAP_HONOR_INSTRUKTUR,
            'profil' => GoogleSheetsService::TAB_PROFIL_INSTRUKTUR,
            'profil_instruktur' => GoogleSheetsService::TAB_PROFIL_INSTRUKTUR,
            'Profil_Instruktur' => GoogleSheetsService::TAB_PROFIL_INSTRUKTUR,
            'siswa' => GoogleSheetsService::TAB_DATA_SISWA,
            'data_siswa' => GoogleSheetsService::TAB_DATA_SISWA,
            'Data_Siswa' => GoogleSheetsService::TAB_DATA_SISWA,
            'monitoring_belum_laporan' => GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN,
            'Monitoring_Belum_Laporan' => GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN,
            'belum_laporan' => GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN,
            'invoice' => GoogleSheetsService::TAB_INVOICE,
            'rekap_invoice' => GoogleSheetsService::TAB_INVOICE,
            'Rekap_Invoice' => GoogleSheetsService::TAB_INVOICE,
            'invoice_marketing' => GoogleSheetsService::TAB_INVOICE_MARKETING,
            'detail_invoice_marketing' => GoogleSheetsService::TAB_INVOICE_MARKETING,
            'Detail_Invoice_Marketing' => GoogleSheetsService::TAB_INVOICE_MARKETING,
            'marketing' => GoogleSheetsService::TAB_INVOICE_MARKETING,
        ];

        $tabKey = $validTabs[$tab] ?? $tab;
        $csv = $this->sheetsService->getCsvContent($tabKey);
        $filename = 'Export_' . $tabKey . '_' . now()->format('Ymd_His') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Direct download Excel (.xlsx) export of a specific tab.
     */
    public function exportExcel(string $tab)
    {
        $validTabs = [
            'monitoring_belum_laporan' => GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN,
            'Monitoring_Belum_Laporan' => GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN,
            'belum_laporan' => GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN,
        ];

        $tabKey = $validTabs[$tab] ?? $tab;

        if ($tabKey === GoogleSheetsService::TAB_MONITORING_BELUM_LAPORAN) {
            $filename = 'Monitoring_Belum_Laporan_' . now()->format('Ymd_His') . '.xlsx';
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MonitoringBelumLaporanExport(), $filename);
        }

        return $this->exportCsv($tab);
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
