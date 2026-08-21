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
     * Format per item: ['label' => '', 'webmaster' => bool, 'admin_sistem' => bool, 'admin' => bool, 'instruktur' => bool]
     */
    private function getAccessMatrix(): array
    {
        return [
            [
                'group' => 'Manajemen User',
                'icon'  => 'bi-people-fill',
                'color' => 'danger',
                'items' => [
                    ['label' => 'Lihat daftar semua user',             'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                    ['label' => 'Buat akun user baru',                 'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => false],
                    ['label' => 'Edit profil & data user',             'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                    ['label' => 'Reset password user',                 'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => false],
                    ['label' => 'Hapus / nonaktifkan user',            'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => false],
                    ['label' => 'Verifikasi akun instruktur baru',     'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                    ['label' => 'Lihat Matrix Akses ini',              'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Laporan Mengajar',
                'icon'  => 'bi-journal-check',
                'color' => 'primary',
                'items' => [
                    ['label' => 'Buat laporan mengajar (sendiri)',     'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => true],
                    ['label' => 'Lihat laporan sendiri',               'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Lihat laporan semua instruktur',      'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Edit laporan (oleh pemilik)',         'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => true],
                    ['label' => 'Hapus laporan (admin/webmaster)',     'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Upload foto kegiatan & absensi fisik','webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => true],
                    ['label' => 'Approve laporan terlambat',           'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'GPS & Check-in Sesi',
                'icon'  => 'bi-geo-alt-fill',
                'color' => 'warning',
                'items' => [
                    ['label' => 'Check-in sesi mengajar (GPS)',        'webmaster' => true,  'admin_sistem' => false, 'admin' => false, 'instruktur' => true],
                    ['label' => 'Lihat validasi GPS / geotag foto',    'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Override / bypass check-in GPS',      'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                    ['label' => 'Lihat log deteksi fake GPS',          'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                ],
            ],
            [
                'group' => 'Jadwal & Sesi Ekskul',
                'icon'  => 'bi-calendar3',
                'color' => 'info',
                'items' => [
                    ['label' => 'Lihat jadwal sendiri',                'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Lihat semua jadwal',                  'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Buat / edit jadwal sesi',             'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Reschedule sesi (manual)',            'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Kelola program ekskul',               'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Kelola enrollment siswa',             'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'Sekolah & Data Master',
                'icon'  => 'bi-building',
                'color' => 'secondary',
                'items' => [
                    ['label' => 'Kelola data sekolah',                 'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Kelola data siswa',                   'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Kelola rombel / kelas',               'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Kelola instruktur (profil)',           'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'Payroll & Honor',
                'icon'  => 'bi-cash-coin',
                'color' => 'success',
                'items' => [
                    ['label' => 'Lihat slip honor sendiri',            'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Kelola batch payroll',                'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                    ['label' => 'Atur tarif honor (salary rates)',     'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                    ['label' => 'Lihat semua data payroll',            'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Export payroll ke Excel/PDF',         'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'Analitik & Dashboard',
                'icon'  => 'bi-graph-up-arrow',
                'color' => 'primary',
                'items' => [
                    ['label' => 'Dashboard utama',                     'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Dashboard analitik lanjutan',         'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Distribusi jadwal',                   'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Real-time monitoring sesi aktif',     'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'Sistem & Log',
                'icon'  => 'bi-shield-lock',
                'color' => 'danger',
                'items' => [
                    ['label' => 'Log pergerakan admin',                'webmaster' => true,  'admin_sistem' => true,  'admin' => false, 'instruktur' => false],
                    ['label' => 'Panduan admin',                       'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Kelola notifikasi sistem',            'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Pengaturan produk & salesman',        'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'Tiket & Support',
                'icon'  => 'bi-headset',
                'color' => 'warning',
                'items' => [
                    ['label' => 'Buat tiket bantuan',                  'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Lihat tiket sendiri',                 'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Lihat & balas semua tiket',           'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                    ['label' => 'Tutup / arsip tiket',                 'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => false],
                ],
            ],
            [
                'group' => 'Portal Publik (Tanpa Login)',
                'icon'  => 'bi-globe',
                'color' => 'secondary',
                'items' => [
                    ['label' => 'Rekap pertemuan ekskul publik',       'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Download PDF presensi (publik)',       'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
                    ['label' => 'Pusat bantuan /help',                 'webmaster' => true,  'admin_sistem' => true,  'admin' => true,  'instruktur' => true],
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
            'webmaster'    => ['label' => 'Webmaster',    'icon' => 'bi-shield-fill-check', 'color' => 'danger',  'desc' => 'Super-admin, akses penuh sistem'],
            'admin_sistem' => ['label' => 'Admin Sistem', 'icon' => 'bi-gear-fill',          'color' => 'warning', 'desc' => 'IT Admin, kelola sistem & user'],
            'admin'        => ['label' => 'Admin',        'icon' => 'bi-person-badge-fill',  'color' => 'info',    'desc' => 'Admin operasional harian'],
            'instruktur'   => ['label' => 'Instruktur',   'icon' => 'bi-person-video3',      'color' => 'success', 'desc' => 'Instruktur pengajar ekskul'],
        ];
    }
}
