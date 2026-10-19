<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccessMatrixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:webmaster');
    }

    /**
     * Tampilkan halaman matrix akses role — hanya untuk webmaster.
     */
    public function index()
    {
        $matrix = $this->getAccessMatrix();
        $roles  = $this->getRoleLabels();

        return view('admin.access-matrix.index', compact('matrix', 'roles'));
    }

    /**
     * Definisi matrix akses seluruh modul & fitur sistem.
     * Format per item: ['label' => '', 'webmaster' => bool, 'admin_sistem' => bool, 'admin' => bool, 'akunting' => bool, 'sales' => bool, 'instruktur' => bool]
     */
    private function getAccessMatrix(): array
    {
        return [
            [
                'group' => 'Manajemen User & Keamanan',
                'icon'  => 'bi-people-fill',
                'color' => 'danger',
                'items' => [
                    ['label' => 'Lihat daftar semua user',             'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Buat akun user baru',                 'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Edit profil & data user',             'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Reset password user',                 'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Hapus / nonaktifkan user',            'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Verifikasi akun instruktur baru',     'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Lihat Matrix Akses ini',              'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Laporan Mengajar & BAP',
                'icon'  => 'bi-journal-check',
                'color' => 'primary',
                'items' => [
                    ['label' => 'Buat laporan mengajar (sendiri)',     'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => true],
                    ['label' => 'Lihat laporan sendiri',               'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => true],
                    ['label' => 'Lihat laporan semua instruktur',      'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Edit laporan (oleh pemilik)',         'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => true],
                    ['label' => 'Hapus laporan (admin/webmaster)',     'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Upload foto kegiatan & absensi fisik','webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => true],
                    ['label' => 'Approve laporan terlambat',           'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'GPS & Check-in Sesi',
                'icon'  => 'bi-geo-alt-fill',
                'color' => 'warning',
                'items' => [
                    ['label' => 'Check-in sesi mengajar (GPS)',        'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => true],
                    ['label' => 'Lihat validasi GPS / geotag foto',    'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Override / bypass check-in GPS',      'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Lihat log deteksi fake GPS',          'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Jadwal & Sesi Ekskul',
                'icon'  => 'bi-calendar3',
                'color' => 'info',
                'items' => [
                    ['label' => 'Lihat jadwal sendiri',                'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => true],
                    ['label' => 'Lihat semua jadwal',                  'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => false],
                    ['label' => 'Buat / edit jadwal sesi',             'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Reschedule sesi (manual)',            'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Kelola program ekskul',               'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Kelola enrollment siswa',             'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Sekolah & Data Master',
                'icon'  => 'bi-building',
                'color' => 'secondary',
                'items' => [
                    ['label' => 'Kelola data sekolah',                 'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Kelola data siswa',                   'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Kelola rombel / kelas',               'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Kelola instruktur (profil)',           'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Kelola master produk & salesman',     'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'Faktur & Invoice Tagihan',
                'icon'  => 'bi-receipt-cutoff',
                'color' => 'danger',
                'items' => [
                    ['label' => 'Buat draf invoice & quick-generate',  'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Koreksi billable & checklist sesi',   'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Verifikasi Gate 1 (Produksi/Ops)',    'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Serah terima berkas ke Akunting',     'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Approval Gate 2 (Staff Akunting)',    'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Download PDF invoice resmi',          'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => false],
                    ['label' => 'Atur skema tagihan sekolah',          'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Payroll & Honorarium',
                'icon'  => 'bi-cash-coin',
                'color' => 'success',
                'items' => [
                    ['label' => 'Lihat slip honor sendiri',            'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => true],
                    ['label' => 'Lihat daftar batch payroll',          'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Buat & hitung batch payroll',         'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Proses & cairkan batch (Pay)',        'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Export payroll ke Excel & CSV Bank',  'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Atur tarif master (salary rates)',    'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'akunting' => true,  'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Google Sheets & Integrasi',
                'icon'  => 'bi-file-earmark-spreadsheet',
                'color' => 'success',
                'items' => [
                    ['label' => 'Lihat status sinkronisasi Sheets',    'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Trigger sync data manual',            'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                    ['label' => 'Ubah konfigurasi Spreadsheet ID/Key', 'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Export data tab ke Excel/CSV',        'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Analitik & Monitoring',
                'icon'  => 'bi-graph-up-arrow',
                'color' => 'primary',
                'items' => [
                    ['label' => 'Dashboard ringkasan utama',           'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => true],
                    ['label' => 'Dashboard analitik eksekutif',        'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => false],
                    ['label' => 'Peta distribusi jadwal',              'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => true,  'instruktur' => false],
                    ['label' => 'Monitoring sesi aktif real-time',     'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Sistem, Log & Dukungan',
                'icon'  => 'bi-shield-lock',
                'color' => 'warning',
                'items' => [
                    ['label' => 'Audit log aktivitas admin',           'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Panduan operasional admin',           'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => false],
                    ['label' => 'Kirim broadcast WhatsApp (Fonnte)',   'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => false, 'sales' => false, 'instruktur' => false],
                    ['label' => 'Tiket bantuan helpdesk',              'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => true],
                ],
            ],
            [
                'group' => 'Portal Publik (Tanpa Login)',
                'icon'  => 'bi-globe',
                'color' => 'secondary',
                'items' => [
                    ['label' => 'Rekap pertemuan ekskul publik',       'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => true],
                    ['label' => 'Download PDF presensi (publik)',       'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => true],
                    ['label' => 'Pusat bantuan /help',                 'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'akunting' => true,  'sales' => true,  'instruktur' => true],
                ],
            ],
        ];
    }

    /**
     * Label tampilan untuk tiap role.
     */
    private function getRoleLabels(): array
    {
        return [
            'webmaster'    => ['label' => 'Webmaster',    'icon' => 'bi-shield-fill-check', 'color' => 'danger',    'hex' => '#dc2626', 'desc' => 'Super-admin, akses penuh teknis & sistem'],
            'admin_sistem' => ['label' => 'Admin Sistem', 'icon' => 'bi-gear-fill',          'color' => 'warning',   'hex' => '#d97706', 'desc' => 'IT Admin, kelola infrastruktur & akun'],
            'admin'        => ['label' => 'Admin (Ops)',  'icon' => 'bi-person-badge-fill',  'color' => 'info',      'hex' => '#0284c7', 'desc' => 'Staf Produksi & Operasional Ekskul'],
            'akunting'     => ['label' => 'Akunting',     'icon' => 'bi-calculator-fill',    'color' => 'success',   'hex' => '#059669', 'desc' => 'Staf Finance/Accounting & Payroll'],
            'sales'        => ['label' => 'Sales',        'icon' => 'bi-briefcase-fill',     'color' => 'secondary', 'hex' => '#7c3aed', 'desc' => 'Tim Marketing & Kemitraan Sekolah'],
            'instruktur'   => ['label' => 'Instruktur',   'icon' => 'bi-person-video3',      'color' => 'success',   'hex' => '#16a34a', 'desc' => 'Pengajar & Instruktur Ekskul'],
        ];
    }
}
