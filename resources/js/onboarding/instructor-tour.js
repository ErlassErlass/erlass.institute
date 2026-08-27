/**
 * Erlass Institute - Instructor Onboarding Tour Steps
 * The Teaching, GPS Check-in & H+1 Reporting Journey
 */

export const instructorTourSteps = [
    {
        element: '#tour-welcome-card',
        popover: {
            title: '👋 Selamat Datang di Erlass Ekskul!',
            description: '<span class="badge-step-pill">Langkah 1 dari 5</span><br>Halo Rekan Instruktur! Ini adalah dasbor utama Anda untuk memantau jadwal mengajar, mencatat kehadiran GPS, dan mengirim laporan mengajar secara tertib.',
            side: 'bottom',
            align: 'start'
        }
    },
    {
        element: '#tour-instructor-today',
        popover: {
            title: '📍 Check-in GPS: SAAT TIBA (Sebelum Kelas)',
            description: '<span class="badge-step-pill">Langkah 2 dari 5</span><br><strong>PENTING:</strong> Tekan tombol Check-in <strong>begitu Anda tiba di sekolah SEBELUM mengajar</strong>, <strong class="text-danger">BUKAN saat kelas selesai</strong>.<br><small class="text-primary fw-semibold">💡 Tips: Check-in mencatat jam kedatangan fisik Anda. Jika baru check-in saat kelas selesai, sistem akan mengira Anda terlambat hadir dan memicu penalti honor.</small>',
            side: 'top',
            align: 'start'
        }
    },
    {
        element: '#tour-instructor-todo',
        popover: {
            title: '📝 Laporan Mengajar: SETELAH Kelas Selesai',
            description: '<span class="badge-step-pill">Langkah 3 dari 5</span><br>Setelah sesi kelas selesai mengajar, barulah Anda mengisi <strong>Laporan Mengajar</strong> (presensi kehadiran siswa & foto kegiatan). Batas waktu submit adalah <strong>H+1 (pukul 23:59 WIB)</strong>.',
            side: 'top',
            align: 'start'
        }
    },
    {
        element: '#tour-instructor-late-request',
        popover: {
            title: '🔓 Dispensasi Buka Akses Laporan',
            description: '<span class="badge-step-pill">Langkah 4 dari 5</span><br>Jika Anda terlewat batas waktu H+1 sehingga form sesi terkunci, ajukan permohonan pembukaan akses di menu ini agar dapat diverifikasi dan di-ACC oleh Admin.',
            side: 'top',
            align: 'start'
        }
    },
    {
        element: '#tour-instructor-earnings',
        popover: {
            title: '📊 Statistik Jam & Aktivitas Mengajar',
            description: '<span class="badge-step-pill">Langkah 5 dari 5</span><br>Pantau akumulasi jam mengajar, laporan terkirim, dan sesi selesai periode cutoff (tgl 11 s.d. 10) serta evaluasi kedisiplinan ketepatan waktu check-in & pelaporan Anda secara transparan.',
            side: 'top',
            align: 'end'
        }
    }
];
