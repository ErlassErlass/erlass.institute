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
        padding: 1rem 1.25rem;
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

    /* ─── Feature Pill Badges ─── */
    .feature-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* ─── Quick Search ─── */
    .guide-search-wrap {
        position: relative;
        max-width: 520px;
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
                    <i class="bi bi-shield-lock-fill text-warning"></i> Khusus Administrator &amp; Tim Manajemen
                </div>
                <h1 class="display-6 fw-bold mb-2 text-white">Panduan Operasional &amp; SOP Sistem</h1>
                <p class="text-white mb-4" style="max-width: 650px; opacity: 0.92; font-size: 1rem;">
                    Dokumentasi lengkap panduan teknis, alur kerja (workflow), kebijakan penjadwalan, payroll, dan mitigasi kendala operasional harian pada sistem <strong>Erlass Institute</strong>.
                </p>
                <div class="guide-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" id="adminGuideSearch" placeholder="Cari panduan (misal: reschedule, reset sesi, payroll, rombel)..." onkeyup="filterGuideTopics()">
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
                    <i class="bi bi-list-ul me-2 text-primary"></i> Daftar Topik Panduan
                </h6>
                <nav class="nav flex-column gap-1" id="guideTocNav">
                    <a href="#section-program" class="toc-link">
                        <i class="bi bi-journal-bookmark text-primary"></i> 1. Program &amp; Rombel
                    </a>
                    <a href="#section-siswa" class="toc-link">
                        <i class="bi bi-people text-success"></i> 2. Pendaftaran Siswa
                    </a>
                    <a href="#section-jadwal" class="toc-link">
                        <i class="bi bi-calendar3 text-warning"></i> 3. Jadwal &amp; Sesi
                    </a>
                    <a href="#section-reschedule" class="toc-link">
                        <i class="bi bi-calendar-x text-danger"></i> 4. Libur, Reschedule &amp; Reset
                    </a>
                    <a href="#section-instruktur" class="toc-link">
                        <i class="bi bi-person-badge text-info"></i> 5. Instruktur &amp; Laporan
                    </a>
                    <a href="#section-payroll" class="toc-link">
                        <i class="bi bi-wallet2 text-primary"></i> 6. Master Tarif &amp; Payroll
                    </a>
                    <a href="#section-absensi" class="toc-link">
                        <i class="bi bi-qr-code-scan text-secondary"></i> 7. Presensi &amp; Rekap PDF
                    </a>
                    <a href="#section-tiket" class="toc-link">
                        <i class="bi bi-ticket-detailed text-warning"></i> 8. Helpdesk &amp; Audit Log
                    </a>
                </nav>

                <hr class="my-3">
                <div class="d-grid">
                    <a href="{{ route('help.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Panduan Instruktur / FAQ
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-9" id="guideContentArea">

            <!-- 1. PROGRAM & ROMBEL -->
            <div class="guide-card" id="section-program">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">1. Manajemen Program &amp; Rombongan Belajar (Rombel)</h4>
                        <small class="text-muted">Pembuatan program ekskul, konfigurasi jadwal mingguan, dan kuota rombel.</small>
                    </div>
                </div>

                <div class="step-box">
                    <div class="step-number">Langkah 1</div>
                    <h6 class="fw-bold mb-1">Membuat Program Ekstrakurikuler Baru</h6>
                    <p class="small text-muted mb-2">Buka menu <strong>Program Ekskul &gt; Buat Program Baru</strong>. Lengkapi formulir multi-tahap:</p>
                    <ul class="small text-secondary mb-0">
                        <li><strong>Identitas:</strong> Pilih Sekolah mitra, Kategori Program (misal: Coding Scratch, Robotik), dan Sales penanggung jawab.</li>
                        <li><strong>Rombel &amp; Jadwal:</strong> Tentukan jumlah pertemuan (default 24 pertemuan), hari mengajar, dan jam mulai/selesai.</li>
                        <li><strong>Instruktur &amp; Asisten:</strong> Tugaskan Instruktur Utama dan Asisten Instruktur (bisa dipilih langsung atau disusulkan nanti).</li>
                    </ul>
                </div>

                <div class="step-box">
                    <div class="step-number">Langkah 2</div>
                    <h6 class="fw-bold mb-1">Menambah Rombel Baru pada Program yang Sudah Berjalan</h6>
                    <p class="small text-muted mb-0">
                        Pada halaman detail program ekskul (<code>/ekstrakurikuler/{id}</code>), klik tombol <strong>+ Tambah Rombel</strong> di tab Rombongan Belajar. Sistem akan otomatis men-generate seluruh sesi pertemuan untuk rombel baru tersebut sesuai tanggal mulai dan frekuensinya.
                    </p>
                </div>

                <div class="callout-tip">
                    <div class="d-flex gap-2">
                        <i class="bi bi-lightbulb-fill text-success fs-5"></i>
                        <div class="small">
                            <strong>Tips Admin:</strong> Kuota siswa pada rombel bersifat target kuota kelas. Jumlah siswa aktif yang sesungguhnya dihitung secara otomatis dan real-time dari data enrollment siswa yang aktif.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. PENDAFTARAN SISWA -->
            <div class="guide-card" id="section-siswa">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">2. Pendaftaran Siswa (Enrollment)</h4>
                        <small class="text-muted">Metode memasukkan siswa ke rombel ekskul dan filter data kelas.</small>
                    </div>
                </div>

                <p class="small text-muted">
                    Masuk ke halaman <strong>Manajemen Siswa</strong> program melalui tombol <em>Daftar Siswa</em> pada detail ekskul (<code>/ekstrakurikuler/{id}/enrollment</code>). Tersedia 3 metode pendaftaran:
                </p>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-person-plus text-primary me-1"></i> 1. Input Manual</h6>
                            <p class="small text-muted mb-0">Mendaftarkan siswa satu per satu dengan memilih siswa terdaftar dari database sekolah mitra.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-collection-fill text-success me-1"></i> 2. Dari Kelas Sekolah</h6>
                            <p class="small text-muted mb-0">Fitur <em>Bulk Import Rombel</em>: Mendaftarkan langsung seluruh siswa dari satu rombel kelas reguler (misal: Kelas 7A) sekaligus ke ekskul.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-file-earmark-excel text-success me-1"></i> 3. Unggah File Excel</h6>
                            <p class="small text-muted mb-0">Mengunggah file CSV/Excel daftar siswa (Nama, NISN, Kelas) secara massal menggunakan template yang disediakan.</p>
                        </div>
                    </div>
                </div>

                <div class="callout-tip">
                    <div class="d-flex gap-2">
                        <i class="bi bi-info-circle-fill text-success fs-5"></i>
                        <div class="small">
                            <strong>Pembeda Kelas vs Rombel:</strong> Kolom <strong>Kelas</strong> menunjukkan kelas reguler siswa di sekolah (contoh: 7B, 8A). Sedangkan kolom <strong>Rombel</strong> menunjukkan kelompok belajar ekstrakurikuler yang diikuti.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. JADWAL & SESI -->
            <div class="guide-card" id="section-jadwal">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">3. Penjadwalan Cerdas &amp; Siklus Sesi (Smart Scheduling)</h4>
                        <small class="text-muted">Mekanisme auto-generate, deteksi libur nasional, dan sinkronisasi sesi.</small>
                    </div>
                </div>

                <div class="step-box">
                    <div class="step-number">Alur Penjadwalan</div>
                    <h6 class="fw-bold mb-1">Mekanisme Penjadwalan Otomatis</h6>
                    <p class="small text-muted mb-2">Saat rombel dibuat, sistem melakukan kalkulasi tanggal sesi dengan aturan:</p>
                    <ul class="small text-secondary mb-0">
                        <li>Mencari hari pertama yang cocok antara tanggal mulai rombel dan hari yang dipilih (misal: Jumat).</li>
                        <li>Melompati tanggal yang terdaftar dalam daftar <strong>Hari Libur Nasional</strong> kalender akademik.</li>
                        <li>Membuat record sesi pertemuan dari pertemuan ke-1 hingga pertemuan target (misal: 24 sesi).</li>
                    </ul>
                </div>

                <div class="step-box">
                    <div class="step-number">Fitur Sinkronisasi</div>
                    <h6 class="fw-bold mb-1">Tombol "Sinkronkan Jadwal" (Regenerate Sessions)</h6>
                    <p class="small text-muted mb-0">
                        Tombol ini digunakan saat ada perubahan konfigurasi rombel (misal: jam digeser atau hari diubah). Sistem akan memperbarui sesi-sesi yang <strong>belum dimulai (status terjadwal)</strong>. Sesi yang sudah <em>Selesai</em>, <em>Berlangsung</em>, atau <em>Direschedule Manual</em> akan <strong>tetap aman dan terlindungi</strong> dari penimpaan.
                    </p>
                </div>
            </div>

            <!-- 4. LIBUR, RESCHEDULE & RESET SESI -->
            <div class="guide-card" id="section-reschedule">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-calendar-x-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">4. Penanganan Libur Mendadak, Reschedule &amp; Reset Sesi</h4>
                        <small class="text-muted">SOP penggantian tanggal sesi libur dan perbaikan salah status sesi berlangsung.</small>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-arrow-repeat text-warning me-1"></i> A. Kasus Libur Mendadak / Reschedule Jadwal</h6>
                <p class="small text-muted mb-3">
                    Bila ada hari libur mendadak (kegiatan sekolah, cuaca ekstrem, atau instruktur berhalangan):
                </p>
                <div class="p-3 border rounded-3 bg-light mb-3">
                    <ol class="small text-secondary mb-0 ps-3">
                        <li class="mb-2">Buka tab <strong>Jadwal Sesi</strong> pada halaman detail program ekstrakurikuler.</li>
                        <li class="mb-2">Pada card sesi yang terdampak (status <em>Terjadwal</em> atau <em>Ditunda</em>), klik tombol <span class="badge bg-warning text-dark"><i class="bi bi-calendar-x me-1"></i>Libur / Jadwal Ulang</span>.</li>
                        <li class="mb-2">
                            <strong>Opsi 1 (Ganti Tanggal):</strong> Isi alasan libur dan pilih <strong>Tanggal Pengganti</strong>. Sistem akan langsung memindahkan jadwal sesi ke tanggal baru dan otomatis menandai flag <code>is_manual_reschedule</code> agar tidak terhapus saat ada sinkronisasi jadwal.
                        </li>
                        <li>
                            <strong>Opsi 2 (Tunda Saja):</strong> Kosongkan tanggal pengganti dan isi alasan. Status sesi akan berubah menjadi <strong>Ditunda</strong> hingga tanggal pengganti ditentukan kemudian.
                        </li>
                    </ol>
                </div>

                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-arrow-counterclockwise text-danger me-1"></i> B. Kasus Salah Klik "Mulai" (Reset Sesi Berlangsung)</h6>
                <p class="small text-muted mb-2">
                    Kadang instruktur atau admin tidak sengaja menekan tombol <em>"Mulai Sesi"</em> padahal kelas belum dimulai, sehingga sesi terkunci dalam status <strong>Berlangsung</strong>.
                </p>
                <div class="callout-warning mb-3">
                    <div class="d-flex gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                        <div class="small">
                            <strong>Fitur Khusus Admin:</strong> Sesi yang berstatus <em>Berlangsung</em> kini memiliki tombol <span class="badge bg-danger"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset Sesi</span> (tersedia di tab Jadwal Sesi detail program, daftar sesi, dan detail sesi). Mengklik tombol ini akan mengembalikan sesi ke status <strong>Terjadwal</strong> dan membersihkan jam pelaksanaan aktual yang keliru.
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. INSTRUKTUR & LAPORAN MENGAJAR -->
            <div class="guide-card" id="section-instruktur">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">5. Verifikasi Instruktur &amp; Laporan Mengajar</h4>
                        <small class="text-muted">Prosedur verifikasi akun instruktur dan persetujuan laporan terlambat.</small>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="step-box h-100">
                            <div class="step-number">Verifikasi Akun</div>
                            <h6 class="fw-bold mb-1">Verifikasi Profil Instruktur</h6>
                            <p class="small text-muted mb-0">
                                Menu <strong>Verifikasi Instruktur</strong> menampilkan calon instruktur yang baru mendaftar. Admin memeriksa kelengkapan KTP, NPWP, nomor rekening bank, dan portofolio keahlian sebelum menyetujui akun menjadi aktif.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="step-box h-100">
                            <div class="step-number">Approval Laporan</div>
                            <h6 class="fw-bold mb-1">Request Laporan Terlambat (Late Report)</h6>
                            <p class="small text-muted mb-0">
                                Instruktur memiliki batas waktu pengisian laporan (maksimal 24 jam). Jika terlewat, instruktur harus mengajukan permohonan isi laporan terlambat di menu <strong>Request Laporan</strong> untuk di-approve oleh Admin.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 6. MASTER TARIF & PAYROLL -->
            <div class="guide-card" id="section-payroll">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">6. Master Tarif &amp; Pencairan Payroll</h4>
                        <small class="text-muted">Kalkulasi honor instruktur, pembuatan batch payroll bulanan, dan cetak slip gaji.</small>
                    </div>
                </div>

                <div class="step-box">
                    <div class="step-number">SOP 1</div>
                    <h6 class="fw-bold mb-1">Konfigurasi Master Tarif (Salary Rates)</h6>
                    <p class="small text-muted mb-0">
                        Menu <strong>Master Tarif</strong> digunakan untuk menentukan honor standar per pertemuan atau per jam bagi Instruktur Utama, Asisten Instruktur, atau tarif khusus per institusi sekolah mitra.
                    </p>
                </div>

                <div class="step-box">
                    <div class="step-number">SOP 2</div>
                    <h6 class="fw-bold mb-1">Proses Pembuatan Batch Payroll Bulanan</h6>
                    <p class="small text-muted mb-2">Pada akhir periode (cut-off bulanan):</p>
                    <ol class="small text-secondary mb-0 ps-3">
                        <li>Buka menu <strong>Pencairan Payroll &gt; Buat Batch Baru</strong>.</li>
                        <li>Pilih rentang tanggal cut-off (misal: 01 s.d. 30 bulan berjalan).</li>
                        <li>Sistem otomatis mengumpulkan seluruh laporan mengajar berstatus <em>Disetujui</em> dan menghitung total honor per instruktur beserta rincian sesinya.</li>
                        <li>Periksa nominal dan klik <strong>Lock / Kunci Batch</strong> untuk memfinalkan pembayaran.</li>
                        <li>Ekspor data ke format Excel/PDF atau cetak <strong>Slip Gaji</strong> resmi per instruktur.</li>
                    </ol>
                </div>
            </div>

            <!-- 7. PRESENSI & REKAP PDF -->
            <div class="guide-card" id="section-absensi">
                <div class="d-flex align-items-center mb-3">
                    <div class="guide-header-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-dark">7. Presensi, Rekap Kehadiran &amp; Cetak PDF Publik</h4>
                        <small class="text-muted">Pengecekan bukti presensi dan tautan cetak form presensi tanpa login.</small>
                    </div>
                </div>

                <p class="small text-muted mb-3">
                    Sistem mendukung presensi digital berbasis QR Code, Geolocation GPS (radius meter ke lokasi sekolah), dan upload foto bukti mengajar di kelas.
                </p>

                <div class="callout-tip">
                    <div class="d-flex gap-2">
                        <i class="bi bi-printer-fill text-success fs-5"></i>
                        <div class="small">
                            <strong>Akses Cetak Presensi Terbuka (Public PDF):</strong> Tautan cetak form presensi fisik (<code>/ekstrakurikuler-session/{id}/print</code>) dapat diakses secara publik tanpa perlu login. Fitur ini dirancang agar PIC Sekolah atau Koordinator Lapangan dapat langsung mencetak lembar absensi fisik secara fleksibel dari browser mereka.
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
                        <h4 class="fw-bold mb-0 text-dark">8. Manajemen Tiket Bantuan &amp; Log Pergerakan Admin</h4>
                        <small class="text-muted">Penanganan kendala pengguna dan pengawasan keamanan sistem.</small>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-ticket-perforated text-warning me-1"></i> Tiket Bantuan (Helpdesk)</h6>
                            <p class="small text-muted mb-0">
                                Setiap keluhan atau permohonan kendala sistem dari instruktur akan masuk sebagai tiket dengan status <em>Open</em>. Admin dapat membalas tiket, mengunggah lampiran solusi, dan menandai status tiket menjadi <em>Closed</em> setelah kendala terselesaikan.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark"><i class="bi bi-shield-lock text-danger me-1"></i> Log Pergerakan Admin (Audit Log)</h6>
                            <p class="small text-muted mb-0">
                                Khusus Webmaster dan Admin Sistem: Seluruh aktivitas krusial seperti pembuatan program, pembatalan sesi, perubahan tarif payroll, dan hapus data tersimpan dalam log riwayat yang tidak dapat dimanipulasi untuk menjaga integritas data.
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
    const scrollPos = window.scrollY + 120;
    
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
