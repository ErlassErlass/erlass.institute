@extends('layouts.app')

@section('title', 'Panduan Administrator & SOP Sistem — Erlass Institute')

@push('styles')
<style>
    /* ─── Design Tokens ─── */
    :root {
        --adm-navy: #0f172a;
        --adm-slate: #1e293b;
        --adm-muted: #64748b;
        --adm-line: #e2e8f0;
        --adm-bg: #f8fafc;
        --adm-blue: #2563eb;
        --adm-blue-subtle: #eff6ff;
        --adm-amber: #d97706;
        --adm-amber-subtle: #fffbeb;
        --adm-emerald: #059669;
        --adm-emerald-subtle: #ecfdf5;
        --adm-rose: #e11d48;
        --adm-rose-subtle: #fff1f2;
        --adm-purple: #7c3aed;
        --adm-purple-subtle: #f5f3ff;
    }

    /* ─── Hero Banner ─── */
    .admin-guide-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #312e81 100%) !important;
        color: #ffffff !important;
        padding: 3rem 2.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.3);
    }
    .admin-guide-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 85% 20%, rgba(99, 102, 241, 0.25) 0%, transparent 50%),
            radial-gradient(circle at 15% 85%, rgba(236, 72, 153, 0.15) 0%, transparent 40%);
        pointer-events: none;
    }
    .admin-guide-hero h1, 
    .admin-guide-hero .hero-title {
        color: #ffffff !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    .admin-guide-hero p,
    .admin-guide-hero .hero-desc {
        color: #e2e8f0 !important;
        line-height: 1.65;
        font-size: 1rem;
    }
    .admin-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.15) !important;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #fef08a !important;
        margin-bottom: 1rem;
    }

    /* ─── Sticky Table of Contents ─── */
    .toc-card {
        position: sticky;
        top: 80px;
        background: #fff;
        border: 1px solid var(--adm-line);
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        padding: 1.25rem;
    }
    .toc-link {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.5rem 0.75rem;
        color: var(--adm-slate);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.15s ease;
    }
    .toc-link:hover, .toc-link.active {
        background: var(--adm-blue-subtle);
        color: var(--adm-blue);
        font-weight: 600;
    }
    .toc-link i {
        font-size: 1rem;
        opacity: 0.75;
    }

    /* ─── Section Cards ─── */
    .guide-card {
        background: #fff;
        border: 1px solid var(--adm-line);
        border-radius: 14px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .guide-card:hover {
        box-shadow: 0 10px 20px -3px rgba(0,0,0,0.06);
    }
    .guide-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-right: 0.75rem;
    }

    /* ─── Step Cards & Callouts ─── */
    .step-box {
        background: #f8fafc;
        border-left: 4px solid var(--adm-blue);
        border-radius: 0 8px 8px 0;
        padding: 1.1rem 1.25rem;
        margin-bottom: 1rem;
    }
    .step-number {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--adm-blue);
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }
    .callout-tip {
        background: var(--adm-emerald-subtle);
        border: 1px solid rgba(5, 150, 105, 0.2);
        border-left: 4px solid var(--adm-emerald);
        border-radius: 8px;
        padding: 1rem 1.25rem;
    }
    .callout-warning {
        background: var(--adm-amber-subtle);
        border: 1px solid rgba(217, 119, 6, 0.2);
        border-left: 4px solid var(--adm-amber);
        border-radius: 8px;
        padding: 1rem 1.25rem;
    }
    .callout-danger {
        background: var(--adm-rose-subtle);
        border: 1px solid rgba(225, 29, 72, 0.2);
        border-left: 4px solid var(--adm-rose);
        border-radius: 8px;
        padding: 1rem 1.25rem;
    }
    .callout-info {
        background: var(--adm-blue-subtle);
        border: 1px solid rgba(37, 99, 235, 0.2);
        border-left: 4px solid var(--adm-blue);
        border-radius: 8px;
        padding: 1rem 1.25rem;
    }

    /* ─── Quick Search ─── */
    .guide-search-wrap {
        position: relative;
        max-width: 540px;
    }
    .guide-search-wrap .bi-search {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #e2e8f0 !important;
        font-size: 1rem;
        pointer-events: none;
    }
    #adminGuideSearch {
        background: rgba(255, 255, 255, 0.18) !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        color: #ffffff !important;
        padding: 0.75rem 1rem 0.75rem 2.75rem;
        border-radius: 10px;
        width: 100%;
        font-size: 0.95rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }
    #adminGuideSearch::placeholder { color: #cbd5e1 !important; }
    #adminGuideSearch:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.28) !important;
        border-color: #ffffff !important;
        color: #ffffff !important;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-md-4">

    <!-- Hero Banner -->
    <div class="admin-guide-hero">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="admin-badge-pill">
                    <i class="bi bi-shield-lock-fill text-warning"></i> Khusus Administrator, Keuangan &amp; Tim Manajemen (v2.9.18)
                </div>
                <h1 class="display-6 fw-bold mb-2 text-white">Panduan Operasional &amp; SOP Sistem Administrator</h1>
                <p class="text-white mb-4" style="max-width: 680px; opacity: 0.92; font-size: 1rem;">
                    Dokumentasi resmi alur kerja (*Standard Operating Procedure*) untuk pengelolaan program ekskul, penanganan sesi libur/ditunda (FIFO Non-Blocking), antrean To-Do List reschedule, cascade shift, modul payroll (asisten &amp; pajak 2.5%), integrasi Google Sheets, dan automasi WhatsApp.
                </p>
                <div class="guide-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="adminGuideSearch" placeholder="Cari panduan (misal: reschedule, libur, cascade, pajak 2.5%, asisten, google sheets)..." onkeyup="filterGuideTopics()">
                </div>
            </div>
            <div class="col-lg-4 text-end d-none d-lg-block">
                <i class="bi bi-person-gear display-1 text-white opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Navigation (Table of Contents) -->
        <div class="col-lg-3">
            <div class="toc-card">
                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                    <i class="bi bi-list-ul me-2 text-primary"></i> Daftar Modul Panduan
                </h6>
                <nav class="nav flex-column gap-1" id="guideTocNav">
                    <a href="#section-dashboard-todo" class="toc-link">
                        <i class="bi bi-pin-angle-fill text-danger"></i> 1. To-Do List Reschedule
                    </a>
                    <a href="#section-libur-reschedule" class="toc-link">
                        <i class="bi bi-calendar-x text-warning"></i> 2. Libur, FIFO &amp; Cascade
                    </a>
                    <a href="#section-program" class="toc-link">
                        <i class="bi bi-journal-bookmark text-primary"></i> 3. Program &amp; Rombel
                    </a>
                    <a href="#section-siswa" class="toc-link">
                        <i class="bi bi-people text-success"></i> 4. Siswa &amp; WhatsApp
                    </a>
                    <a href="#section-analisis" class="toc-link">
                        <i class="bi bi-graph-up-arrow text-info"></i> 5. Analisis Beban Kerja
                    </a>
                    <a href="#section-payroll" class="toc-link">
                        <i class="bi bi-wallet2 text-primary"></i> 6. Payroll, Asisten &amp; Pajak
                    </a>
                    <a href="#section-sheets" class="toc-link">
                        <i class="bi bi-file-earmark-spreadsheet text-success"></i> 7. Integrasi Google Sheets
                    </a>
                    <a href="#section-tiket" class="toc-link">
                        <i class="bi bi-ticket-detailed text-warning"></i> 8. Helpdesk &amp; Audit Log
                    </a>
                </nav>

                <hr class="my-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('help.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka FAQ Instruktur
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9" id="guideContentArea">

            <!-- 1. TO-DO LIST RESCHEDULE ADMIN -->
            <div class="guide-card" id="section-dashboard-todo">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-pin-angle-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">1. Dashboard Command Center &amp; To-Do List Antrean Reschedule</h4>
                        <small class="text-muted">Pemantauan sesi libur/ditunda dan penjadwalan tanggal pengganti langsung dari dashboard.</small>
                    </div>
                </div>

                <div class="p-3.5 rounded-3 mb-3" style="background: #fffbeb; border: 1.5px solid #fde68a;">
                    <h6 class="fw-bold text-dark mb-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> Kebijakan: Strict Reschedule Rule (Tidak Boleh Hangus)
                    </h6>
                    <p class="small text-muted mb-0">
                        Setiap sesi yang ditiadakan karena libur sekolah, kegiatan mendadak, atau izin instruktur <strong>wajib dijadwalkan ulang (reschedule)</strong> ke tanggal pengganti. Sesi tidak boleh dibatalkan atau di-skip begitu saja demi memastikan target kurikulum (12 atau 16 pertemuan) terpenuhi 100%.
                    </p>
                </div>

                <div class="step-box">
                    <div class="step-number">Alur Operasional</div>
                    <h6 class="fw-bold mb-1">Menjalankan Reschedule dari Widget Dashboard</h6>
                    <ol class="small text-secondary mb-0 ps-3">
                        <li class="mb-2">Buka <strong>Dashboard Admin</strong> (<code>/dashboard</code>). Jika ada sesi libur yang belum ditentukan tanggal penggantinya, kartu kuning bertajuk <strong>📌 TO-DO LIST ADMIN: Antrean Reschedule (X Sesi Wajib Dijadwalkan Ulang)</strong> akan otomatis muncul di bagian atas.</li>
                        <li class="mb-2">Pilih sesi yang ingin dijadwalkan, lalu klik tombol <strong>`Reschedule Sekarang`</strong>.</li>
                        <li class="mb-2">Pilih <strong>Tanggal Pengganti Baru</strong> dan masukkan <strong>Alasan Penjadwalan</strong>.</li>
                        <li class="mb-2"><strong>Opsi Pergeseran Berantai (*Cascade Shift*):</strong> Centang kotak <em>"Geser seluruh jadwal pertemuan berikutnya secara berantai"</em> jika Anda ingin memundurkan jadwal pertemuan berikutnya secara proporsional.</li>
                        <li>Klik <strong>Simpan Jadwal Pengganti</strong>. Status sesi kembali menjadi <em>Terjadwal</em> dan otomatis keluar dari antrean to-do list.</li>
                    </ol>
                </div>

                <div class="callout-info">
                    <div class="d-flex gap-2">
                        <i class="bi bi-shield-lock-fill text-primary fs-5"></i>
                        <div class="small">
                            <strong>Otorisasi Eksklusif Admin:</strong> Instruktur dilarang memindahkan atau mengubah tanggal sesi (HTTP 403 Forbidden). Hanya akun dengan role <strong>Admin, Admin Sistem, atau Webmaster</strong> yang memiliki wewenang memindahkan tanggal sesi.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. LIBUR, FIFO & CASCADE SHIFT -->
            <div class="guide-card" id="section-libur-reschedule">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-calendar-x-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">2. Penanganan Sesi Libur, FIFO Non-Blocking, &amp; Relokasi Laporan</h4>
                        <small class="text-muted">Eliminasi penguncian sesi lanjutan, tombol tandai libur, dan perbaikan salah input laporan.</small>
                    </div>
                </div>

                <div class="step-box">
                    <div class="step-number">Fitur v2.9.18</div>
                    <h6 class="fw-bold mb-1">Mekanisme FIFO Non-Blocking &amp; Auto-Bypass Tanggal Merah</h6>
                    <p class="small text-muted mb-2">
                        Sistem tidak lagi mengunci sesi pertemuan lanjutan jika sesi sebelumnya berstatus libur atau bertepatan dengan tanggal merah:
                    </p>
                    <ul class="small text-secondary mb-0">
                        <li><strong>Status Non-Blocking:</strong> Sesi dengan status <code>libur</code>, <code>ditunda</code>, <code>diganti</code>, atau <code>dibatalkan</code> secara otomatis dilewati dan <strong>tidak memblokir</strong> pengisian laporan atau check-in sesi berikutnya.</li>
                        <li><strong>Auto-Bypass Hari Libur Nasional:</strong> Jika sesi lampau jatuh pada tanggal merah nasional yang terdaftar di database <code>holidays</code>, sesi tersebut otomatis dilewati dari penguncian FIFO tanpa perlu intervensi manual.</li>
                    </ul>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-calendar2-minus text-warning me-1"></i> A. Tombol Cepat Tandai Libur</h6>
                            <p class="small text-muted mb-0">
                                Pada halaman Detail Sesi (<code>/ekstrakurikuler/sessions/{id}</code>), gunakan tombol <strong>`[ 📅 Sesi P.X Libur / Ditunda? ]`</strong> untuk menandai status libur dengan alasan resmi. Sesi akan otomatis masuk ke To-Do List Admin.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-arrow-left-right text-primary me-1"></i> B. Relokasi Laporan Mengajar</h6>
                            <p class="small text-muted mb-0">
                                Jika instruktur salah memasukkan laporan di Pertemuan 2 padahal untuk Pertemuan 1, buka Detail Laporan &gt; klik <strong>`⇄ Pindahkan Pertemuan`</strong> &gt; pilih target Pertemuan 1. Laporan, absensi, dan foto akan berpindah secara instan.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="callout-warning">
                    <div class="d-flex gap-2">
                        <i class="bi bi-arrow-counterclockwise text-warning fs-5"></i>
                        <div class="small">
                            <strong>Reset Sesi Berlangsung ke Terjadwal:</strong> Jika instruktur salah menekan tombol *"Mulai Sesi"* sebelum waktu kegiatan, buka Detail Sesi &gt; klik <strong>`↺ Reset ke Terjadwal`</strong> untuk menghapus jam aktual dan mengembalikan status ke terjadwal.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. PROGRAM & ROMBEL -->
            <div class="guide-card" id="section-program">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">3. Manajemen Program Ekstrakurikuler &amp; Rombel</h4>
                        <small class="text-muted">Wizard pembuatan program multi-step, penugasan instruktur utama &amp; asisten, dan sinkronisasi sesi.</small>
                    </div>
                </div>

                <div class="step-box">
                    <div class="step-number">Langkah 1</div>
                    <h6 class="fw-bold mb-1">Membuat Program Ekstrakurikuler Baru (Wizard 6 Langkah)</h6>
                    <p class="small text-muted mb-2">Buka menu <strong>Program Ekskul &gt; Tambah Program</strong>:</p>
                    <ul class="small text-secondary mb-0">
                        <li><strong>1. Info Program:</strong> Tentukan Kategori Program (Coding Scratch, Micro:bit, Python, Robotika) dan Sales PIC.</li>
                        <li><strong>2. Sekolah:</strong> Pilih sekolah mitra dari database klien aktif.</li>
                        <li><strong>3. Kebutuhan Teknis:</strong> Konfigurasi kebutuhan internet, proyektor, kabel terminal.</li>
                        <li><strong>4. Struktur Rombel:</strong> Tentukan jumlah siswa, ruangan, dan total rombel yang dibuka.</li>
                        <li><strong>5. Detail Rombel:</strong> Isi Hari, Jam Mulai/Selesai, Tanggal Mulai &amp; Selesai, serta Total Pertemuan (default: 16 sesi).</li>
                        <li><strong>6. Review &amp; Simpan:</strong> Periksa pratinjau seluruh sesi dan klik <em>Selesai &amp; Simpan</em>.</li>
                    </ul>
                </div>

                <div class="step-box">
                    <div class="step-number">Langkah 2</div>
                    <h6 class="fw-bold mb-1">Menambah Rombel Baru pada Program yang Berjalan</h6>
                    <p class="small text-muted mb-0">
                        Pada detail program ekskul (<code>/ekstrakurikuler/{id}</code>), buka tab <strong>Rombel</strong> &gt; klik <strong>`+ Tambah Rombel`</strong>. Sistem akan otomatis menentukan nomor rombel berikutnya (*Rombel N+1*) dan meng-generate seluruh jadwal sesinya.
                    </p>
                </div>

                <div class="callout-tip">
                    <div class="d-flex gap-2">
                        <i class="bi bi-lightbulb-fill text-success fs-5"></i>
                        <div class="small">
                            <strong>Proteksi Manual Reschedule saat Sinkronisasi:</strong> Mengklik tombol <em>"Sinkronkan Jadwal"</em> hanya memperbarui sesi-sesi normal yang belum berjalan. Sesi yang berstatus <em>Selesai</em>, <em>Berlangsung</em>, atau <em>Direschedule Manual (<code>is_manual_reschedule = true</code>)</em> tidak akan ditimpa atau dihapus.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. PENDAFTARAN SISWA & WHATSAPP -->
            <div class="guide-card" id="section-siswa">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">4. Manajemen Siswa, Enrollment, &amp; WhatsApp Gateway</h4>
                        <small class="text-muted">Import CSV, verifikasi NISN sementara, chat 1-klik, dan automasi notifikasi Fonnte.</small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-filetype-csv text-success me-1"></i> Import CSV Siswa</h6>
                            <p class="small text-muted mb-0">
                                Gunakan template <code>Template_Import_Siswa_Program.csv</code> pada halaman enrollment. Isi kolom: <code>nama_lengkap, nisn, kelas_akademik, no_hp_orangtua, target_rombel_ekskul</code>.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-whatsapp text-success me-1"></i> Welcome Message</h6>
                            <p class="small text-muted mb-0">
                                Sistem otomatis mendeteksi <code>no_hp_orangtua</code> dan menembakkan pesan sambutan via WhatsApp berisi informasi jadwal hari dan jam mulai kelas anak.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-bell-fill text-warning me-1"></i> Progress Kelipatan 4</h6>
                            <p class="small text-muted mb-0">
                                Setiap 4x kehadiran anak (Pertemuan 4, 8, 12, 16), sistem mengirimkan ringkasan materi dan absensi detail dengan emoji (✅ / ❌) ke orang tua.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="callout-info">
                    <div class="d-flex gap-2">
                        <i class="bi bi-chat-dots-fill text-primary fs-5"></i>
                        <div class="small">
                            <strong>Chat WhatsApp 1-Klik di Menu Siswa (<code>/siswa</code>):</strong> Klik ikon hijau WhatsApp pada baris siswa untuk langsung membuka ruang obrolan ke nomor orang tua siswa tanpa perlu menyimpan nomor kontak terlebih dahulu.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. ANALISIS JADWAL & BEBAN KERJA -->
            <div class="guide-card" id="section-analisis">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">5. Analisis Beban Kerja &amp; Matriks Ketersediaan</h4>
                        <small class="text-muted">Monitoring distribusi sesi mengajar instruktur dan pemetaan jadwal luang mingguan.</small>
                    </div>
                </div>

                <p class="small text-muted mb-3">
                    Akses menu <strong>Analisis Jadwal</strong> (<code>/admin/analytics/schedule-distribution</code>):
                </p>

                <div class="step-box">
                    <div class="step-number">Tab 1: Distribusi Sesi</div>
                    <h6 class="fw-bold mb-1">Monitoring Beban Kerja &amp; Keadilan Jam Mengajar</h6>
                    <ul class="small text-secondary mb-0">
                        <li><strong>Filter Multi-Periode:</strong> Pilih periode <em>Periode Honor Berjalan (Siklus 11-10)</em>, <em>Periode Lalu</em>, atau <em>Custom Date Range</em>.</li>
                        <li><strong>Grafik Visual:</strong> Membandingkan total jam mengajar antar-instruktur. Sistem memberi rekomendasi instruktur yang jam mengajarnya masih di bawah rata-rata.</li>
                    </ul>
                </div>

                <div class="step-box">
                    <div class="step-number">Tab 2: Matriks Ketersediaan (Availability Matrix)</div>
                    <h6 class="fw-bold mb-1">Pengecekan Waktu Luang &amp; Filter Domisili Kota</h6>
                    <ul class="small text-secondary mb-0">
                        <li><strong>Interactive Week Picker:</strong> Pilih minggu kalender tertentu lalu klik <em>Cek Ketersediaan</em> untuk melihat jadwal terisi vs waktu luang instruktur.</li>
                        <li><strong>Indikator Warna:</strong> 🟢 Free (Luang), 🟡 Sebagian Terisi, 🔴 Penuh / Busy, ⬜ Libur / Tidak Membuka Jadwal.</li>
                        <li><strong>Filter Domisili:</strong> Saring instruktur berdasarkan kota tempat tinggal untuk menugaskan instruktur ke sekolah terdekat.</li>
                    </ul>
                </div>
            </div>

            <!-- 6. MASTER TARIF & PAYROLL ENGINE -->
            <div class="guide-card" id="section-payroll">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">6. Master Tarif, Payroll Engine v2.9.18 &amp; Ekspor Akuntansi</h4>
                        <small class="text-muted">Kompensasi instruktur utama, honor flat rate asisten, pajak 2.5%, denda check-in, dan ekspor multi-sheet.</small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="step-box h-100">
                            <div class="step-number">Komponen 1 — Instruktur Utama</div>
                            <h6 class="fw-bold mb-1">Tarif Dasar + Bonus Kepakaran + Transport</h6>
                            <p class="small text-muted mb-0">
                                Tarif dasar dihitung per level karir (*Junior, Madya, Senior, Expert, Master Trainer*) ditambah bonus materi (*Scratch, Micro:bit, Python, Robotika*) dan uang transport per zona sekolah.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="step-box h-100">
                            <div class="step-number">Komponen 2 — Asisten Instruktur</div>
                            <h6 class="fw-bold mb-1">Flat Rate Rp 100.000 / Sesi</h6>
                            <p class="small text-muted mb-0">
                                Asisten Instruktur menerima tarif flat <strong>Rp 100.000</strong> per sesi mengajar. Sesuai kebijakan manajemen: Uang transport asisten = Rp 0 dan denda keterlambatan asisten = Rp 0.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-3 mb-3" style="background: #f8fafc; border: 1.5px solid #cbd5e1;">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-calculator text-primary me-1"></i> Formula Akumulasi Gaji, Pajak 2.5%, &amp; Netto (Slip Resmi Erlass)</h6>
                    <div class="small font-monospace text-dark mb-2 p-2 bg-white rounded border">
                        Total Penerimaan Kotor = Honor Utama + Honor Asisten + Bonus Produk + Transport Utama<br>
                        Potongan Pajak (2.5%) = round(Total Penerimaan Kotor * 0.025)<br>
                        Gaji Bersih (Netto) = round(Total Penerimaan Kotor * 0.975) - Total Denda Check-in
                    </div>
                    <small class="text-muted">* Denda keterlambatan check-in GPS (> 15 menit dari jam mulai) adalah Rp 25.000 per kejadian.</small>
                </div>

                <div class="step-box">
                    <div class="step-number">Ekspor &amp; Pembayaran</div>
                    <h6 class="fw-bold mb-1">Format Ekspor Batch Payroll (<code>/payroll/{id}</code>)</h6>
                    <ul class="small text-secondary mb-0">
                        <li><strong>1. Excel Multi-Worksheet (<code>.xlsx</code>):</strong>
                            <ul>
                                <li><code>Transfer_Bank</code>: Rekap nama, bank, no rekening, honor utama/asisten, transport, kotor, pajak 2.5%, denda, netto + formula <code>=SUM()</code>.</li>
                                <li><code>Jurnal_Akuntansi</code>: Jurnal debet/kredit biaya honor, hutang pajak 2.5%, dan kas keluar.</li>
                                <li><code>Rincian_Sesi</code>: Audit detail per pertemuan lengkap dengan badge peran (Utama vs Asisten).</li>
                            </ul>
                        </li>
                        <li><strong>2. CSV Mass Transfer Bank (<code>.csv</code>):</strong> Format ringkas siap unggah ke portal corporate banking BCA / Mandiri / BNI.</li>
                        <li><strong>3. Cetak PDF Slip Gaji Batch / Satuan:</strong> Desain 2 kolom resmi (*PENERIMAAN* vs *POTONGAN*) dan kotak *GAJI BERSIH*.</li>
                    </ul>
                </div>
            </div>

            <!-- 7. GOOGLE SHEETS INTEGRATION -->
            <div class="guide-card" id="section-sheets">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">7. Integrasi Google Spreadsheet (5 Tab Data Live)</h4>
                        <small class="text-muted">Sinkronisasi streaming data operasional ke master spreadsheet manajemen.</small>
                    </div>
                </div>

                <p class="small text-muted mb-3">
                    Akses menu: <strong>Sistem &amp; Pengaturan &gt; Integrasi Google Sheets</strong> (<code>/admin/google-sheets</code>).
                </p>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-layers-fill text-success me-1"></i> Struktur 5 Tab Master Sheet</h6>
                            <ul class="small text-secondary mb-0 ps-3">
                                <li><code>Ringkasan_KPI</code>: Matriks performa instruktur &amp; kedisiplinan.</li>
                                <li><code>Laporan_Mengajar</code>: Riwayat laporan, topik materi &amp; kehadiran.</li>
                                <li><code>Jadwal_Sesi_Ekskul</code>: Jadwal sesi &amp; jam check-in aktual.</li>
                                <li><code>Absensi_Siswa</code>: Rekap hadir, sakit, izin, alpha per anak.</li>
                                <li><code>Rekap_Honor</code>: Estimasi honor kotor, denda &amp; honor bersih.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Fitur Initial Full Sync</h6>
                            <p class="small text-muted mb-2">
                                Gunakan tombol <strong>`⚡ Jalankan Full Sync Sekarang`</strong> untuk menyinkronkan seluruh ribuan data historis dari database ke spreadsheet secara instan melalui background queue.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. HELPDESK & AUDIT LOG -->
            <div class="guide-card" id="section-tiket">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-ticket-detailed-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">8. Manajemen Tiket Bantuan &amp; Log Aktivitas (Audit Trail)</h4>
                        <small class="text-muted">Penyelesaian kendala operasional instruktur dan pencatatan audit trail keamanan.</small>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-ticket-perforated text-warning me-1"></i> Tiket Bantuan (<code>/tickets</code>)</h6>
                            <p class="small text-muted mb-0">
                                Tanggapi tiket kendala instruktur (kategori: *Jadwal / Honor*, *Teknis / Error*, *Keluhan Lain*). Admin dapat membalas pesan, mengunggah lampiran, dan menandai status menjadi *Resolved*.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-shield-lock text-danger me-1"></i> Activity Logs (<code>/activity-logs</code>)</h6>
                            <p class="small text-muted mb-0">
                                Seluruh aksi krusial (penandaan sesi libur, reschedule, relokasi laporan, override tarif, dan delete data) dicatat secara otomatis mencakup: User, Aksi, Waktu WIB, IP Address, dan Device Info.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterGuideTopics() {
    const input = document.getElementById('adminGuideSearch').value.toLowerCase();
    const cards = document.querySelectorAll('#guideContentArea .guide-card');
    
    cards.forEach(card => {
        const text = card.innerText.toLowerCase();
        if (text.includes(input)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Active TOC indicator on scroll
window.addEventListener('scroll', () => {
    const sections = document.querySelectorAll('.guide-card');
    const scrollPos = window.scrollY + 140;
    
    sections.forEach(section => {
        const top = section.offsetTop;
        const height = section.offsetHeight;
        const id = section.getAttribute('id');
        const link = document.querySelector(`#guideTocNav a[href="#${id}"]`);
        
        if (scrollPos >= top && scrollPos < top + height) {
            document.querySelectorAll('#guideTocNav a').forEach(a => a.classList.remove('active'));
            if (link) link.classList.add('active');
        }
    });
});
</script>
@endpush
