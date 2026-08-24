@extends('layouts.app')

@section('title', 'Panduan & FAQ 101 — Erlass Institute')

@push('styles')
<style>
    /* ─── Page-level tokens ─── */
    :root {
        --help-navy:  #0f172a;
        --help-slate: #334155;
        --help-muted: #64748b;
        --help-line:  #e2e8f0;
        --help-bg:    #f8fafc;
        --help-blue:  #2563eb;
        --help-blue-light: #eff6ff;
        --help-amber: #b45309;
        --help-amber-light: #fffbeb;
        --help-green: #15803d;
        --help-green-light: #f0fdf4;
        --help-red:   #b91c1c;
        --help-red-light: #fef2f2;
        --help-radius: 12px;
    }

    /* ─── Hero ─── */
    .help-hero {
        background: var(--help-navy);
        color: #fff;
        padding: 3rem 2rem 2.75rem;
        border-radius: var(--help-radius);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .help-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 60% 80% at 100% 0%, rgba(37,99,235,.28) 0%, transparent 70%),
            radial-gradient(ellipse 40% 50% at -10% 120%, rgba(14,165,233,.18) 0%, transparent 60%);
        pointer-events: none;
    }
    .help-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #93c5fd;
        margin-bottom: 1rem;
    }
    .help-hero h1 {
        font-size: clamp(1.75rem, 3vw, 2.25rem);
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -.02em;
        margin-bottom: .75rem;
    }
    .help-hero p {
        color: #cbd5e1;
        max-width: 56ch;
        margin-bottom: 1.75rem;
        line-height: 1.7;
        font-size: .95rem;
    }

    /* ─── Search ─── */
    .help-search-wrap {
        position: relative;
        max-width: 480px;
    }
    .help-search-wrap .bi-search {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
        pointer-events: none;
    }
    #faqSearchInput {
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 8px;
        color: #fff;
        padding: .75rem 1rem .75rem 2.75rem;
        font-size: .9rem;
        width: 100%;
        transition: background .2s, border-color .2s;
    }
    #faqSearchInput::placeholder { color: #94a3b8; }
    #faqSearchInput:focus {
        outline: none;
        background: rgba(255,255,255,.15);
        border-color: rgba(255,255,255,.4);
    }

    /* ─── Tabs ─── */
    .help-tab-nav {
        display: flex;
        gap: .5rem;
        border-bottom: 2px solid var(--help-line);
        margin-bottom: 2rem;
    }
    .help-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1.25rem;
        font-size: .875rem;
        font-weight: 600;
        color: var(--help-muted);
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        border-radius: 6px 6px 0 0;
        transition: color .15s, border-color .15s;
    }
    .help-tab-btn:hover { color: var(--help-blue); }
    .help-tab-btn.active {
        color: var(--help-blue);
        border-bottom-color: var(--help-blue);
        background: var(--help-blue-light);
    }
    .help-tab-content { display: none; }
    .help-tab-content.active { display: block; }

    /* ─── Section heading ─── */
    .section-label {
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--help-muted);
        margin-bottom: .5rem;
    }
    .section-heading {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--help-navy);
        letter-spacing: -.02em;
        margin-bottom: 1.5rem;
    }

    /* ─── 2-path cards ─── */
    .path-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 768px) { .path-grid { grid-template-columns: 1fr; } }

    .path-card {
        border: 1px solid var(--help-line);
        border-radius: var(--help-radius);
        padding: 1.75rem;
        background: #fff;
        box-shadow: 0 1px 3px rgba(15,23,42,.06), 0 4px 12px rgba(15,23,42,.04);
        transition: box-shadow .2s;
    }
    .path-card:hover {
        box-shadow: 0 4px 20px rgba(15,23,42,.10);
    }
    .path-card--blue { border-top: 3px solid var(--help-blue); }
    .path-card--amber { border-top: 3px solid #d97706; }

    .path-badge {
        display: inline-flex;
        align-items: center;
        gap: .375rem;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: .3rem .75rem;
        border-radius: 999px;
        margin-bottom: 1rem;
    }
    .path-badge--blue { background: var(--help-blue-light); color: var(--help-blue); }
    .path-badge--amber { background: var(--help-amber-light); color: var(--help-amber); }

    .path-card h3 {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--help-navy);
        letter-spacing: -.01em;
        margin-bottom: .5rem;
    }
    .path-card p.desc {
        font-size: .875rem;
        color: var(--help-slate);
        line-height: 1.65;
        margin-bottom: 1.25rem;
    }

    /* Steps list */
    .steps-list {
        list-style: none;
        padding: 0;
        margin: 0;
        counter-reset: step;
    }
    .steps-list li {
        counter-increment: step;
        position: relative;
        padding: .65rem 0 .65rem 2.25rem;
        font-size: .875rem;
        color: var(--help-slate);
        line-height: 1.6;
        border-bottom: 1px solid var(--help-line);
        display: block;
        text-align: left;
    }
    .steps-list li:last-child { border-bottom: none; }
    .steps-list li::before {
        content: counter(step);
        position: absolute;
        left: 0;
        top: .65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        font-size: .72rem;
        font-weight: 700;
        line-height: 1;
    }
    .path-card--blue .steps-list li::before {
        background: var(--help-blue-light);
        color: var(--help-blue);
    }
    .path-card--amber .steps-list li::before {
        background: var(--help-amber-light);
        color: var(--help-amber);
    }
    .steps-list li strong { color: var(--help-navy); font-weight: 600; }

    .path-tip {
        display: flex;
        align-items: flex-start;
        gap: .625rem;
        font-size: .8rem;
        border-radius: 8px;
        padding: .75rem 1rem;
        margin-top: 1.25rem;
        line-height: 1.55;
    }
    .path-tip--blue { background: var(--help-blue-light); color: var(--help-blue); }
    .path-tip--amber { background: var(--help-amber-light); color: var(--help-amber); }

    /* ─── Components grid ─── */
    .comp-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 2rem;
    }
    @media (max-width: 992px) { .comp-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .comp-grid { grid-template-columns: 1fr; } }

    .comp-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        padding: 1.25rem;
        background: #fff;
        border: 1px solid var(--help-line);
        border-radius: var(--help-radius);
        box-shadow: 0 1px 3px rgba(15,23,42,.04);
    }
    .comp-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .comp-item h6 {
        font-size: .875rem;
        font-weight: 700;
        color: var(--help-navy);
        letter-spacing: -.01em;
        margin-bottom: .25rem;
    }
    .comp-item p {
        font-size: .8rem;
        color: var(--help-muted);
        line-height: 1.55;
        margin: 0;
    }
    .comp-optional {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        background: #f1f5f9;
        color: var(--help-muted);
        padding: .15rem .5rem;
        border-radius: 999px;
        margin-left: .4rem;
        vertical-align: middle;
    }
    .comp-required {
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        background: #fef2f2;
        color: #b91c1c;
        padding: .15rem .5rem;
        border-radius: 999px;
        margin-left: .4rem;
        vertical-align: middle;
    }

    /* ─── Deadline banner ─── */
    .deadline-banner {
        display: flex;
        gap: 1.25rem;
        align-items: flex-start;
        background: var(--help-red-light);
        border: 1px solid #fecaca;
        border-radius: var(--help-radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .deadline-banner i { color: var(--help-red); font-size: 1.5rem; flex-shrink: 0; margin-top: .1rem; }
    .deadline-banner h6 { font-size: .95rem; font-weight: 700; color: #7f1d1d; margin-bottom: .35rem; }
    .deadline-banner p { font-size: .875rem; color: #991b1b; margin: 0; line-height: 1.6; }

    /* ─── FAQ ─── */
    .faq-list { display: flex; flex-direction: column; gap: .75rem; }
    .faq-item {
        background: #fff;
        border: 1px solid var(--help-line);
        border-radius: var(--help-radius);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15,23,42,.04);
        transition: box-shadow .2s;
    }
    .faq-item.hidden { display: none; }
    .faq-item:hover { box-shadow: 0 3px 12px rgba(15,23,42,.08); }

    .faq-question {
        display: flex;
        align-items: center;
        gap: .875rem;
        padding: 1.1rem 1.5rem;
        cursor: pointer;
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        font-size: .9rem;
        font-weight: 600;
        color: var(--help-navy);
        line-height: 1.4;
        transition: background .15s;
        user-select: none;
    }
    .faq-question:hover { background: var(--help-bg); }
    .faq-question[aria-expanded="true"] { background: var(--help-blue-light); color: var(--help-blue); }
    .faq-question .faq-chevron {
        margin-left: auto;
        flex-shrink: 0;
        color: var(--help-muted);
        font-size: .85rem;
        transition: transform .2s cubic-bezier(.4,0,.2,1), color .15s;
    }
    .faq-question[aria-expanded="true"] .faq-chevron {
        transform: rotate(180deg);
        color: var(--help-blue);
    }
    .faq-question .faq-q-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--help-bg);
        font-size: .75rem;
        font-weight: 700;
        color: var(--help-muted);
        border: 1px solid var(--help-line);
        transition: background .15s, color .15s;
    }
    .faq-question[aria-expanded="true"] .faq-q-num {
        background: var(--help-blue);
        color: #fff;
        border-color: var(--help-blue);
    }
    .faq-answer {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows .28s cubic-bezier(.4,0,.2,1);
    }
    .faq-answer.open { grid-template-rows: 1fr; }
    .faq-answer__overflow { overflow: hidden; }
    .faq-answer__inner {
        padding: 0 1.5rem 1.25rem 4.25rem;
        font-size: .875rem;
        color: var(--help-slate);
        line-height: 1.7;
    }
    .faq-answer__inner strong { color: var(--help-navy); }

    /* ─── Empty search state ─── */
    .faq-empty {
        display: none;
        padding: 3rem 1rem;
        text-align: center;
        color: var(--help-muted);
        font-size: .9rem;
    }
    .faq-empty i { font-size: 2rem; display: block; margin-bottom: .75rem; opacity: .4; }

    /* ─── Inline code ─── */
    code {
        background: #f1f5f9;
        border-radius: 4px;
        padding: .1em .35em;
        font-size: .85em;
        color: #1e40af;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" style="max-width: 1100px;">

    {{-- ═══ HERO ═══ --}}
    <div class="help-hero">
        <div class="help-hero__eyebrow">
            <i class="bi bi-book-half"></i>
            Panduan Resmi & FAQ
        </div>
        <h1>Cara Membuat Laporan Mengajar</h1>
        <p>Panduan langkah demi langkah untuk seluruh instruktur Erlass Institute — jalur Rutin via Agenda Sesi, jalur Ad-Hoc, detail komponen wajib laporan, dan jawaban atas pertanyaan yang paling sering diajukan.</p>

        <div class="help-search-wrap">
            <i class="bi bi-search"></i>
            <input type="text" id="faqSearchInput" placeholder="Cari di FAQ (misal: GPS, terlambat, foto, gaji...)" autocomplete="off" aria-label="Cari FAQ">
        </div>
    </div>

    {{-- ═══ TAB NAV ═══ --}}
    <div class="help-tab-nav" role="tablist">
        <button class="help-tab-btn active" role="tab" aria-selected="true" aria-controls="tab-panduan" id="btn-panduan" onclick="switchTab('panduan', this)">
            <i class="bi bi-list-check"></i> Panduan 101
        </button>
        <button class="help-tab-btn" role="tab" aria-selected="false" aria-controls="tab-honor" id="btn-honor" onclick="switchTab('honor', this)">
            <i class="bi bi-cash-coin"></i> Kompensasi & Honor
        </button>
        <button class="help-tab-btn" role="tab" aria-selected="false" aria-controls="tab-tiket" id="btn-tiket" onclick="switchTab('tiket', this)">
            <i class="bi bi-headset"></i> Tiket Bantuan &amp; Support
        </button>
        <button class="help-tab-btn" role="tab" aria-selected="false" aria-controls="tab-faq" id="btn-faq" onclick="switchTab('faq', this)">
            <i class="bi bi-question-circle"></i> Tanya Jawab (FAQ)
        </button>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB 1 — PANDUAN 101                        --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="help-tab-content active" id="tab-panduan" role="tabpanel" aria-labelledby="btn-panduan">

        {{-- Section 1: 2 Jalur --}}
        <p class="section-label"><i class="bi bi-signpost-split me-1"></i>Langkah 1 dari 3 — Pilih Jalur</p>
        <h2 class="section-heading">2 Jalur Pembuatan Laporan Mengajar</h2>

        <div class="path-grid">

            {{-- Jalur Rutin --}}
            <div class="path-card path-card--blue">
                <div class="path-badge path-badge--blue"><i class="bi bi-calendar-check"></i> Jalur 1 — Utama</div>
                <h3>Sesi Rutin (Jadwal Sesi &amp; Laporan)</h3>
                <p class="desc">Untuk seluruh kegiatan yang <strong>sesuai jadwal rutin mingguan</strong> yang sudah terdaftar di Agenda Sesi.</p>

                <ol class="steps-list">
                    <li>Buka menu <strong>Jadwal Sesi &amp; Laporan</strong> di sidebar kiri atau tombol <strong>Buat Laporan</strong> pada tabel Jadwal Hari Ini di Dashboard.</li>
                    <li>Temukan Sesi Pertemuan hari ini, lalu klik tombol <strong>"Detail Sesi"</strong>.</li>
                    <li>Saat tiba di sekolah (<strong>SEBELUM kelas dimulai</strong>), tekan tombol <strong>"📌 Check-in Hadir (GPS &amp; Camera)"</strong>. <span class="badge bg-danger text-white rounded-pill px-2 py-0.5" style="font-size: 0.68rem; font-weight: 600;">Wajib Sebelum Mengajar</span> <small class="text-muted d-block mt-0.5">(Tombol aktif mulai 10 menit sebelum jam mulai sesi)</small></li>
                    <li>Setelah selesai kegiatan mengajar, barulah klik tombol <strong>"Buat Laporan &amp; Absensi"</strong> (Batas submit: H+1).</li>
                    <li>Isi seluruh komponen wajib (materi, kehadiran siswa, foto kelas), lalu klik <strong>Simpan Laporan</strong>.</li>
                </ol>

                <div class="path-tip path-tip--blue">
                    <i class="bi bi-lightbulb-fill flex-shrink-0 mt-1"></i>
                    <span><strong>Tips Kedisiplinan:</strong> Jangan menunda check-in sampai kelas selesai mengajar agar jam kedatangan Anda tidak terhitung terlambat oleh sistem.</span>
                </div>
            </div>

            {{-- Jalur Ad-Hoc --}}
            <div class="path-card path-card--amber">
                <div class="path-badge path-badge--amber"><i class="bi bi-lightning-charge"></i> Jalur 2 — Khusus</div>
                <h3>Sesi Ad-Hoc / Pengganti</h3>
                <p class="desc">Untuk kegiatan mengajar <strong>di luar jadwal rutin</strong> — kelas pengganti, sesi tambahan, atau kegiatan insidental.</p>

                <ol class="steps-list">
                    <li>Buka menu <strong>Laporan Ad-Hoc / Pengganti</strong> di sidebar navigasi.</li>
                    <li>Pada kolom kategori, pilih jenis <strong>"Ad-Hoc / Sesi Pengganti"</strong>.</li>
                    <li>Pilih Sekolah, Rombel, dan tanggal pelaksanaan sesi <strong>secara manual</strong>.</li>
                    <li>Isi seluruh detail materi pembelajaran, absensi kehadiran siswa, dan foto dokumentasi kegiatan.</li>
                    <li>Tekan tombol <strong>Simpan Laporan</strong> untuk mengirim data.</li>
                </ol>

                <div class="path-tip path-tip--amber">
                    <i class="bi bi-exclamation-triangle-fill flex-shrink-0 mt-1"></i>
                    <span>Gunakan hanya saat sesi <strong>tidak ditemukan</strong> di Agenda Sesi Rutin.</span>
                </div>
            </div>

        </div>

        {{-- Section 2: Komponen Wajib --}}
        <p class="section-label"><i class="bi bi-check-all me-1"></i>Langkah 2 dari 3 — Isi Form</p>
        <h2 class="section-heading">Komponen Wajib Pengisian Laporan</h2>

        <div class="comp-grid">
            <div class="comp-item">
                <div class="comp-icon" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-geo-alt-fill"></i></div>
                <div>
                    <h6>GPS Check-in &amp; Live Geotag (Saat Tiba di Sekolah)</h6>
                    <p>Wajib dilakukan <strong>saat baru tiba di sekolah sebelum mengajar</strong> (Radius: &le; 500 m). Sistem mencatat jam kedatangan fisik Anda. Jangan tunda sampai kelas selesai agar tidak terkena penalti keterlambatan.</p>
                </div>
            </div>
            <div class="comp-item">
                <div class="comp-icon" style="background:#f0fdf4;color:#15803d;"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <h6>Absensi &amp; Kapasitas Siswa (Maks. 30 Siswa)</h6>
                    <p>Tandai Hadir / Alpha per siswa. Kapasitas maksimal adalah <strong>30 siswa per rombel</strong>. Jika rombel memiliki <strong>≥ 24 siswa</strong>, wajib didampingi 1 orang Asisten Instruktur.</p>
                </div>
            </div>
            <div class="comp-item">
                <div class="comp-icon" style="background:#fffbeb;color:#b45309;"><i class="bi bi-journal-code"></i></div>
                <div>
                    <h6>Topik &amp; Materi Pengajaran</h6>
                    <p>Tuliskan modul, topik utama, dan ringkasan materi yang telah disampaikan kepada siswa hari ini.</p>
                </div>
            </div>
            <div class="comp-item">
                <div class="comp-icon" style="background:#f0f9ff;color:#0284c7;"><i class="bi bi-camera-fill"></i></div>
                <div>
                    <h6>Foto Kegiatan Kelas</h6>
                    <p>Foto dokumentasi interaksi belajar mengajar, siswa beraktivitas di depan modul/komputer sebagai bukti pengajaran.</p>
                </div>
            </div>
            <div class="comp-item">
                <div class="comp-icon" style="background:#ecfdf5;color:#059669;"><i class="bi bi-card-checklist"></i></div>
                <div>
                    <h6>Foto Fisik Absensi Bertandatangan</h6>
                    <p>Foto lembar kertas presensi manual yang telah diparaf/ditandatangani oleh guru pendamping sekolah mitra.</p>
                </div>
            </div>
            <div class="comp-item">
                <div class="comp-icon" style="background:#fdf4ff;color:#7e22ce;"><i class="bi bi-file-earmark-zip-fill"></i></div>
                <div>
                    <h6>File Project <span class="comp-required">Wajib</span></h6>
                    <p>Unggah file karya siswa (.sb3 Scratch, Micro:bit, Python, PDF) di akhir setiap pertemuan sebagai bukti hasil kegiatan belajar.</p>
                </div>
            </div>
            <div class="comp-item">
                <div class="comp-icon" style="background:#fff1f2;color:#be123c;"><i class="bi bi-chat-quote-fill"></i></div>
                <div>
                    <h6>Refleksi &amp; Kendala</h6>
                    <p>Catat evaluasi pemahaman siswa &amp; kendala peralatan (misal: "Servo 360° tidak merespons").</p>
                </div>
            </div>
        </div>

        {{-- Section 3: Deadline --}}
        <p class="section-label"><i class="bi bi-clock me-1"></i>Langkah 3 dari 3 — Kirim Tepat Waktu</p>
        <h2 class="section-heading">Batas Waktu H+1</h2>

        <div class="deadline-banner">
            <i class="bi bi-exclamation-octagon-fill"></i>
            <div>
                <h6>Laporan wajib di-submit maksimal 24 jam (H+1) setelah kelas selesai.</h6>
                <p>Jika melewati batas H+1, laporan akan otomatis berlabel <strong>Terlambat (H+X)</strong>. Pengisian susulan memerlukan persetujuan khusus dari Admin melalui fitur <strong>Request Laporan Susulan</strong>.</p>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- SECTION: Kompensasi & Honor                --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="help-tab-content" id="tab-honor" role="tabpanel" aria-labelledby="btn-honor">

        <h2 class="section-heading">💰 Kompensasi & Honor Mengajar</h2>
        <p class="text-muted mb-4" style="font-size:.9rem;">Informasi resmi mengenai besaran honorarium per sesi mengajar, formula transportasi, dan ketentuan khusus sesuai <strong>Keputusan Direksi No. 536/EPI/V/2025</strong>.</p>

        {{-- Tabel Skala Siswa --}}
        <h5 class="fw-bold text-dark mb-2" style="font-size:.95rem;"><i class="bi bi-people-fill me-2 text-primary"></i>Skala Honorarium Berdasarkan Jumlah Siswa Hadir</h5>
        <p class="text-muted mb-3" style="font-size:.85rem;">Honorarium dihitung berdasarkan <strong>jumlah siswa yang HADIR</strong> pada sesi tersebut, bukan total siswa terdaftar di rombel.</p>

        <div class="table-responsive rounded border bg-white mb-4" style="font-size:.88rem;">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Jumlah Siswa Hadir</th>
                        <th class="text-center">Honorarium / Sesi</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-3 fw-bold text-dark">≥ 15 Orang Siswa</td>
                        <td class="text-center fw-bold text-success">Rp 150.000</td>
                        <td class="text-center"><span class="badge bg-success">Penuh (Standar)</span></td>
                    </tr>
                    <tr>
                        <td class="ps-3 fw-semibold text-dark">12 – 14 Orang Siswa</td>
                        <td class="text-center fw-semibold text-primary">Rp 115.000</td>
                        <td class="text-center"><span class="badge bg-info text-dark">Berjalan</span></td>
                    </tr>
                    <tr>
                        <td class="ps-3 fw-semibold text-dark">10 – 11 Orang Siswa</td>
                        <td class="text-center fw-semibold text-dark">Rp 100.000</td>
                        <td class="text-center"><span class="badge bg-secondary">Berjalan</span></td>
                    </tr>
                    <tr>
                        <td class="ps-3 text-dark">8 – 9 Orang Siswa</td>
                        <td class="text-center text-dark">Rp 75.000</td>
                        <td class="text-center"><span class="badge bg-warning text-dark">Disesuaikan</span></td>
                    </tr>
                    <tr class="table-danger bg-opacity-10">
                        <td class="ps-3 text-danger fw-bold">&lt; 8 Orang Siswa</td>
                        <td class="text-center text-danger fw-bold">Rp 0</td>
                        <td class="text-center"><span class="badge bg-danger">HOLD (Ditunda)</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Transportasi & Ketentuan Khusus --}}
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="p-3 bg-white rounded border h-100" style="font-size:.88rem;">
                    <div class="fw-bold text-primary mb-2"><i class="bi bi-truck me-1"></i> Formula Transportasi</div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted">
                        <li>
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            <strong>Jarak ≥ 10 KM dari Pejaten (2x PP Bensin):</strong><br>
                            <code class="text-dark bg-light px-2 py-1 rounded d-inline-block mt-1">(Jarak KM × Rp 350 × 2) + Rp 7.500</code>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            <strong>Jarak &lt; 10 KM / Guru Internal / Sesi Kantor Erlass:</strong><br>
                            Uang transport = <strong class="text-dark">Rp 0</strong>.
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-white rounded border h-100" style="font-size:.88rem;">
                    <div class="fw-bold text-primary mb-2"><i class="bi bi-person-plus-fill me-1"></i> Ketentuan Rombel &amp; Asisten</div>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted">
                        <li>
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            <strong>Kapasitas Maksimal Rombel:</strong><br>
                            Maksimal <strong class="text-dark">30 Siswa</strong> per rombel ekstrakurikuler.
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            <strong>Wajib Asisten Instruktur (≥ 24 Siswa):</strong><br>
                            Jika jumlah siswa terdaftar / hadir <strong class="text-dark">≥ 24 orang</strong>, rombel <strong>wajib didampingi 1 Asisten Instruktur</strong>.
                        </li>
                        <li>
                            <i class="bi bi-cash-stack text-primary me-1"></i>
                            <strong>Honor Asisten Instruktur:</strong><br>
                            <strong class="text-dark">Rp 100.000</strong> / sesi (berlaku untuk rombel ≥ 24 siswa).
                        </li>
                        <li>
                            <i class="bi bi-info-circle-fill text-warning me-1"></i>
                            Jika data absensi belum tersedia, engine menggunakan <em>jumlah siswa terdaftar rombel</em> sebagai fallback.
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="p-3 rounded border-start border-primary border-3 bg-light" style="font-size:.85rem;">
            <i class="bi bi-info-circle me-1 text-primary"></i>
            Ketentuan di atas <strong>aktif dan berlaku otomatis</strong> pada engine perhitungan payroll sesuai Keputusan Direksi No. 536/EPI/V/2025. Pertanyaan lebih lanjut silakan hubungi Admin.
        </div>

    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB 3 — TIKET BANTUAN & SUPPORT            --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="help-tab-content" id="tab-tiket" role="tabpanel" aria-labelledby="btn-tiket">

        <h2 class="section-heading">🎧 Layanan Tiket Bantuan &amp; Pengaduan</h2>
        <p class="text-muted mb-4" style="font-size:.9rem;">Jika Anda mengalami kendala operasional mengajar, masalah sistem/aplikasi, atau ketidaksesuaian rekap honor, gunakan sistem <strong>Tiket Bantuan Terpadu</strong> untuk tindak lanjut cepat dan terdokumentasi.</p>

        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="p-3 bg-white rounded border h-100 shadow-xs">
                    <div class="d-flex align-items-center gap-2 mb-2 text-primary fw-bold">
                        <i class="bi bi-calendar-range fs-5"></i>
                        <span>1. Jadwal &amp; Honor</span>
                    </div>
                    <p class="text-muted small mb-0">
                        Untuk pelaporan kesalahan jadwal sesi, sesi yang belum muncul di dashboard, selisih perhitungan jam mengajar, uang bensin/transport, atau pembaruan nomor rekening bank.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white rounded border h-100 shadow-xs">
                    <div class="d-flex align-items-center gap-2 mb-2 text-danger fw-bold">
                        <i class="bi bi-laptop fs-5"></i>
                        <span>2. Teknis &amp; Error Sistem</span>
                    </div>
                    <p class="text-muted small mb-0">
                        Untuk kendala GPS di luar radius, kamera HP gagal terbuka, upload file project error, sesi terkunci, atau ketidaksengajaan memulai sesi yang perlu di-reset.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white rounded border h-100 shadow-xs">
                    <div class="d-flex align-items-center gap-2 mb-2 text-warning fw-bold">
                        <i class="bi bi-chat-square-dots fs-5"></i>
                        <span>3. Keluhan Lain / Lapangan</span>
                    </div>
                    <p class="text-muted small mb-0">
                        Untuk pelaporan perlengkapan robotik/modul yang rusak, kendala fasilitas proyektor/WiFi di sekolah, koordinasi PIC sekolah, atau izin ketidakhadiran mendesak.
                    </p>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary bg-opacity-10 border-start border-4 border-primary mb-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1"><i class="bi bi-ticket-perforated text-primary me-2"></i>Butuh Tindak Lanjut dari Tim Manajemen?</h5>
                    <p class="text-secondary small mb-0">Buat tiket pengaduan baru dan pantau balasan pesan langsung dari tim Admin melalui percakapan terstruktur.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-primary fw-semibold px-3 py-2">
                        <i class="bi bi-list-task me-1"></i> Daftar Tiket Saya
                    </a>
                    <a href="{{ route('tickets.create') }}" class="btn btn-primary fw-bold px-4 py-2">
                        <i class="bi bi-plus-circle me-1"></i> Buat Tiket Baru
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- TAB 4 — FAQ                                --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div class="help-tab-content" id="tab-faq" role="tabpanel" aria-labelledby="btn-faq">

        <h2 class="section-heading">Pertanyaan yang Sering Diajukan</h2>

        <div class="faq-list" id="faqList">

            @php
            $faqs = [
                [
                    'q' => 'Kapan waktu yang tepat untuk melakukan Check-in GPS? Sebelum atau sesudah kelas?',
                    'a' => '<strong>WAJIB SEBELUM KELAS DIMULAI (Saat Baru Tiba di Sekolah)</strong>.<br>Check-in bertujuan mencatat <em>jam kedatangan fisik</em> Anda di lokasi sekolah. <strong class="text-danger">Jangan melakukan check-in setelah kelas selesai atau bersamaan saat mengisi laporan</strong>, karena sistem akan membaca Anda terlambat hadir selama durasi kelas (misal terlambat 60–90 menit) dan otomatis memicu status penalti denda keterlambatan.',
                ],
                [
                    'q' => 'Kapan saya harus menggunakan Jalur Rutin vs Jalur Ad-Hoc?',
                    'a' => 'Gunakan <strong>Jalur Rutin</strong> (via menu <em>Jadwal Sesi &amp; Laporan</em> atau tombol langsung di Dashboard) untuk seluruh pertemuan yang sudah memiliki jadwal mingguan resmi. Gunakan <strong>Jalur Ad-Hoc</strong> (menu <em>Laporan Ad-Hoc / Pengganti</em>) hanya jika Anda mengajar kelas pengganti atau kegiatan insidental yang jadwalnya belum terdaftar di Agenda Sesi Rutin.',
                ],
                [
                    'q' => 'Mengapa tombol "Check-in Hadir" nonaktif / berstatus "Check-in dibuka HH:ii WIB"?',
                    'a' => 'Sistem menerapkan kebijakan jendela waktu check-in yang aman: tombol check-in baru akan <strong>aktif otomatis mulai 10 menit sebelum jam mulai sesi terjadwal</strong>. Contoh: jika kelas Anda dijadwalkan pukul 14:00, tombol check-in akan dapat diklik mulai pukul 13:50 WIB.',
                ],
                [
                    'q' => 'Bagaimana cara kerja Check-in GPS & Stempel Geotag Kamera?',
                    'a' => 'Saat tiba di sekolah (H - 10 menit), buka detail Sesi Ekstrakurikuler dan tekan tombol <strong>"📌 Check-in Hadir (GPS &amp; Camera)"</strong>. Sistem membuka kamera HP secara langsung dan mengambil titik koordinat GPS presisi. Sistem otomatis mencetak stempel visual permanen (Nama Sekolah, Pertemuan, Jam WIB, Koordinat GPS) di atas foto dan mengompres ukurannya menjadi ~150KB dalam hitungan milidetik.',
                ],
                [
                    'q' => 'Mengapa foto check-in wajib menggunakan kamera HP langsung dan tidak bisa dari galeri?',
                    'a' => 'Untuk menjamin keaslian dan integritas kehadiran instruktur di sekolah (*Quality Control Policy*), sistem mengunci input ke kamera HP (*Live Camera Only*). Foto dari galeri album tidak diizinkan untuk mencegah pemalsuan kehadiran.',
                ],
                [
                    'q' => 'Mengapa status check-in saya bernilai "Diluar Radius (Warning)"?',
                    'a' => 'Status <strong>Diluar Radius</strong> terjadi jika posisi GPS perangkat Anda berjarak lebih dari 500 meter dari titik koordinat sekolah yang terdaftar. Pastikan Anda sudah berada di area lingkungan sekolah sebelum menekan tombol check-in.',
                ],
                [
                    'q' => 'Bagaimana jika sekolah tiba-tiba libur atau ada ujian/kegiatan mendadak?',
                    'a' => 'Segera hubungi Admin atau buat tiket bantuan di menu <strong>Tiket Bantuan</strong>. Admin akan menggunakan fitur <strong>Libur / Reschedule Sesi</strong> untuk menunda sesi dan menentukan tanggal pertemuan pengganti yang disepakati dengan pihak sekolah.',
                ],
                [
                    'q' => 'Mengapa laporan saya berlabel "Terlambat (H+4)" padahal saya datang tepat waktu?',
                    'a' => 'Sistem memisahkan dua KPI berbeda: <strong>Kedisiplinan Kehadiran di Sekolah (Check-in)</strong> dan <strong>Ketepatan Submit Laporan (H+1)</strong>. Anda bisa datang tepat waktu di hari Jumat, namun jika laporan baru diisi 4 hari kemudian (Selasa), laporan tersebut tercatat sebagai susulan <em>Terlambat H+4</em> — keduanya independen.',
                ],
                [
                    'q' => 'Berapa toleransi keterlambatan check-in sebelum dikenakan denda?',
                    'a' => 'Toleransi keterlambatan adalah <strong>14 menit</strong> (status <em>Warning</em>, tanpa denda). Jika check-in dilakukan ≥ 15 menit setelah jam mulai jadwal, sistem otomatis menetapkan status <em>Penalty</em> dengan pemotongan <strong>Rp 25.000</strong> pada payroll bulan berjalan.',
                ],
                [
                    'q' => 'Apa yang harus dilakukan jika GPS di HP tidak terdeteksi?',
                    'a' => '1. Pastikan fitur <strong>Location / GPS</strong> di HP sudah aktif (Mode Akurasi Tinggi).<br>2. Pastikan peramban (Chrome / Safari) sudah diberi izin mengakses lokasi.<br>3. Muat ulang halaman (<em>refresh</em>) dan coba tekan tombol Check-in kembali.',
                ],
                [
                    'q' => 'Bagaimana cara melaporkan jika ada ketidaksesuaian data jadwal atau slip honor?',
                    'a' => 'Buka menu <strong>Tiket Bantuan</strong> (`/tickets`) di sidebar, klik <strong>Buat Tiket Baru</strong>, pilih kategori <strong>Jadwal / Honor</strong>, lalu sertakan penjelasan dan sesi terkait. Tim Keuangan / Akademik akan meninjau dan merespons tiket Anda.',
                ],
                [
                    'q' => 'Apakah pihak sekolah mitra bisa mencetak lembar presensi resmi tanpa login?',
                    'a' => 'Ya. Pihak sekolah (Kepala Sekolah / Guru Pendamping) dapat membuka portal publik di <code>https://erlass.institute/rekap-pertemuan-ekskul</code> dan langsung mengklik tombol <strong>[PDF]</strong> pada pertemuan yang bersangkutan untuk mengunduh lembar presensi resmi berformat A4 portrait.',
                ],
                [
                    'q' => 'Kapan form laporan terkunci dan tidak bisa diisi lagi?',
                    'a' => 'Form laporan mengajar terkunci otomatis setelah melewati batas <strong>H+1 (24 jam)</strong>. Jika sudah terkunci, Anda perlu mengajukan <strong>Request Laporan Susulan</strong> kepada Admin untuk membuka aksesnya kembali.',
                ],
                [
                    'q' => 'Bagaimana cara menambah siswa baru yang belum ada di daftar absensi?',
                    'a' => 'Di dalam form absensi sesi, tersedia tombol <strong>"+ Tambah Siswa Ad-Hoc"</strong>. Isi nama lengkap dan data dasar siswa, sistem akan langsung mendaftarkan siswa tersebut ke dalam rombel dan mencatat kehadirannya di sesi ini.',
                ],
                [
                    'q' => 'Berapa batas maksimal siswa per rombel dan kapan wajib menggunakan asisten?',
                    'a' => 'Setiap rombel ekstrakurikuler memiliki kapasitas maksimal <strong>30 orang siswa</strong>. Apabila jumlah siswa dalam satu rombel mencapai <strong>24 orang atau lebih (≥ 24 siswa)</strong>, maka kegiatan mengajar <strong>wajib didampingi oleh 1 orang Asisten Instruktur</strong> guna menjaga efektivitas kelas, bimbingan teknis, dan keamanan belajar.',
                ],
            ];
            @endphp

            @foreach($faqs as $i => $faq)
            <div class="faq-item" data-keywords="{{ strtolower(strip_tags($faq['q'].' '.$faq['a'])) }}">
                <button
                    class="faq-question"
                    aria-expanded="false"
                    aria-controls="faq-answer-{{ $i }}"
                    onclick="toggleFaq(this)"
                >
                    <span class="faq-q-num">{{ $i + 1 }}</span>
                    <span>{{ $faq['q'] }}</span>
                    <i class="bi bi-chevron-down faq-chevron"></i>
                </button>
                <div class="faq-answer" id="faq-answer-{{ $i }}" role="region">
                    <div class="faq-answer__overflow">
                        <div class="faq-answer__inner">{!! $faq['a'] !!}</div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="faq-empty" id="faqEmpty">
            <i class="bi bi-search"></i>
            Tidak ada hasil untuk kata kunci tersebut.
        </div>

    </div>

</div>

<script>
    /* ─── Tab switching ─── */
    function switchTab(id, btn) {
        document.querySelectorAll('.help-tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.help-tab-btn').forEach(el => {
            el.classList.remove('active');
            el.setAttribute('aria-selected', 'false');
        });
        document.getElementById('tab-' + id).classList.add('active');
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');
    }

    /* ─── FAQ accordion ─── */
    function toggleFaq(btn) {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        // Close all
        document.querySelectorAll('.faq-question').forEach(b => {
            b.setAttribute('aria-expanded', 'false');
            document.getElementById(b.getAttribute('aria-controls')).classList.remove('open');
        });
        // Open clicked (if was closed)
        if (!expanded) {
            btn.setAttribute('aria-expanded', 'true');
            document.getElementById(btn.getAttribute('aria-controls')).classList.add('open');
        }
    }

    /* ─── Search filter ─── */
    document.getElementById('faqSearchInput').addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        const items = document.querySelectorAll('.faq-item');
        let visible = 0;

        items.forEach(item => {
            const match = !q || item.dataset.keywords.includes(q);
            item.classList.toggle('hidden', !match);
            if (match) visible++;
        });

        document.getElementById('faqEmpty').style.display = (visible === 0 && q) ? 'block' : 'none';

        // Auto-switch to FAQ tab when searching
        if (q) {
            const faqBtn = document.getElementById('btn-faq');
            if (!faqBtn.classList.contains('active')) switchTab('faq', faqBtn);
        }
    });
</script>
@endsection
