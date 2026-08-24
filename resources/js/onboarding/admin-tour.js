/**
 * Erlass Institute - Admin & Coordinator Onboarding Tour Steps
 * The Operational Monitoring & Governance Journey
 */

export const adminTourSteps = [
    {
        element: '#tour-welcome-card',
        popover: {
            title: '🛡️ Selamat Datang di Pusat Komando Erlass',
            description: '<span class="badge-step-pill">Langkah 1 dari 5</span><br>Dasbor ini dirancang untuk memantau kelancaran seluruh operasional ekstrakurikuler, evaluasi instruktur, dan manajemen sekolah mitra.',
            side: 'bottom',
            align: 'start'
        }
    },
    {
        element: '#tour-admin-stats',
        popover: {
            title: '📊 Ringkasan Data Operasional',
            description: '<span class="badge-step-pill">Langkah 2 dari 5</span><br>Pantau akumulasi sekolah aktif, jumlah siswa terdaftar, total rombel, dan laporan mengajar yang masuk secara real-time.',
            side: 'bottom',
            align: 'start'
        }
    },
    {
        element: '#tour-admin-pending-reports',
        popover: {
            title: '⚠️ Monitoring Sesi Belum Lapor',
            description: '<span class="badge-step-pill">Langkah 3 dari 5</span><br>Daftar sesi yang sudah selesai namun belum dilaporkan oleh instruktur. Anda bisa langsung mengirimkan <strong>Pengingat WhatsApp Otomatis via Fonnte</strong> hanya dengan satu klik.',
            side: 'top',
            align: 'start'
        }
    },
    {
        element: '#tour-admin-late-approval',
        popover: {
            title: '📬 Persetujuan Dispensasi Laporan',
            description: '<span class="badge-step-pill">Langkah 4 dari 5</span><br>Tinjau dan setujui/tolak permohonan pembukaan akses laporan susulan yang diajukan oleh instruktur yang melewati deadline H+1.',
            side: 'top',
            align: 'start'
        }
    },
    {
        element: '#tour-admin-urgent-sessions',
        popover: {
            title: '📅 Sesi Mendesak Tanpa Instruktur',
            description: '<span class="badge-step-pill">Langkah 5 dari 5</span><br>Peringatan dini untuk sesi mengajar terjadwal yang belum memiliki instruktur plotting agar kegiatan siswa tidak terhambat.',
            side: 'top',
            align: 'end'
        }
    }
];
