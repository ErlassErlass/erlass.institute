@extends('layouts.app')

@section('title', 'Pusat Bantuan & Panduan FAQ 101')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Hero Section -->
    <div class="card bg-primary text-white border-0 shadow-sm mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7);">
        <div class="card-body p-4 p-lg-5 position-relative" style="z-index: 2;">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-white bg-opacity-20 text-white px-3 py-1.5 rounded-pill fw-semibold mb-2">
                        <i class="bi bi-journal-bookmark-fill me-1"></i> Official Guide & Standard Operating Procedure
                    </span>
                    <h1 class="h2 fw-bold mb-2">Pusat Bantuan & Panduan FAQ 101</h1>
                    <p class="mb-4 text-white-50" style="max-width: 650px;">
                        Panduan resmi tata cara pembuatan laporan mengajar (Jalur Rutin vs Ad-Hoc), Check-in GPS real-time, detail komponen wajib laporan, serta rincian aturan Payroll.
                    </p>

                    <!-- Live Search Input -->
                    <div class="input-group input-group-lg shadow-sm" style="max-width: 550px;">
                        <span class="input-group-text bg-white border-0 text-primary">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="faqSearchInput" class="form-control border-0 fs-6" placeholder="Ketik kata kunci (misal: Rutin, Ad-hoc, GPS, Foto, Gaji)..." onkeyup="filterFaq()">
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block text-end opacity-75">
                    <i class="bi bi-patch-question-fill" style="font-size: 8rem; line-height: 1;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-pills nav-fill bg-white p-2 rounded-3 shadow-sm border border-light-subtle mb-4" id="helpTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold py-2.5" id="howto-tab" data-bs-toggle="tab" data-bs-target="#howto-content" type="button" role="tab">
                <i class="bi bi-book-half me-1"></i> 📘 Panduan 101: Cara Membuat Laporan
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold py-2.5" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq-content" type="button" role="tab">
                <i class="bi bi-question-circle-fill me-1"></i> ❓ Tanya Jawab (FAQ)
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="helpTabContent">

        <!-- TAB 1: PANDUAN 101 (CARA MEMBUAT LAPORAN ME NGAJAR) -->
        <div class="tab-pane fade show active" id="howto-content" role="tabpanel">

            <!-- Section A: 2 Jalur Pembuatan Laporan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-signpost-split text-primary fs-4"></i> 2 Jalur Pembuatan Laporan Mengajar
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        
                        <!-- Jalur 1: Sesi Rutin -->
                        <div class="col-lg-6">
                            <div class="p-4 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 h-100">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-primary px-3 py-1.5 rounded-pill fw-bold">JALUR 1 (UTAMA)</span>
                                    <h6 class="fw-bold mb-0 text-dark fs-5">📅 Sesi Rutin (Agenda Kegiatan)</h6>
                                </div>
                                <p class="text-secondary small mb-3">
                                    Gunakan jalur ini untuk seluruh kegiatan mengajar yang <strong>sesuai dengan jadwal rutin mingguan</strong> yang terdaftar di Agenda Kegiatan.
                                </p>
                                <div class="bg-white p-3 rounded-3 border border-primary-subtle shadow-sm mb-3">
                                    <h6 class="fw-bold small text-primary mb-2"><i class="bi bi-list-check me-1"></i>Langkah Pembuatan:</h6>
                                    <ol class="small text-dark ps-3 mb-0 lh-lg">
                                        <li>Buka menu <strong>Agenda Kegiatan / Penjadwalan Sesi</strong> di sidebar kiri.</li>
                                        <li>Cari Sesi Pertemuan sekolah Anda, lalu klik tombol <strong>"Detail Sesi"</strong>.</li>
                                        <li>Saat tiba di sekolah, tekan <strong>"📌 Check-in Hadir (GPS & Camera)"</strong> untuk merekam lokasi & selfie.</li>
                                        <li>Setelah mengajar selesai, tekan tombol <strong>"Buat Laporan & Absensi"</strong>.</li>
                                    </ol>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-primary small fw-semibold">
                                    <i class="bi bi-info-circle-fill"></i> Rekomendasi: Otomatis terhubung dengan presensi siswa & rombel.
                                </div>
                            </div>
                        </div>

                        <!-- Jalur 2: Sesi Ad-Hoc -->
                        <div class="col-lg-6">
                            <div class="p-4 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 h-100">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold">JALUR 2 (KHUSUS)</span>
                                    <h6 class="fw-bold mb-0 text-dark fs-5">⚡ Sesi Ad-Hoc / Pengganti</h6>
                                </div>
                                <p class="text-secondary small mb-3">
                                    Gunakan jalur ini jika mengajar <strong>di luar jadwal rutin</strong> (sesi tambahan, kelas pengganti, atau kegiatan insidental).
                                </p>
                                <div class="bg-white p-3 rounded-3 border border-warning-subtle shadow-sm mb-3">
                                    <h6 class="fw-bold small text-warning text-darken mb-2"><i class="bi bi-list-check me-1"></i>Langkah Pembuatan:</h6>
                                    <ol class="small text-dark ps-3 mb-0 lh-lg">
                                        <li>Buka menu <strong>Buat Laporan Mengajar</strong> di sidebar kiri.</li>
                                        <li>Pada opsi kategori, pilih <strong>"Ad-Hoc / Sesi Pengganti"</strong>.</li>
                                        <li>Pilih nama Sekolah, Rombel, dan tanggal pelaksanaan secara manual.</li>
                                        <li>Isi seluruh detail materi, absensi, dan foto kegiatan lalu tekan <strong>Simpan Laporan</strong>.</li>
                                    </ol>
                                </div>
                                <div class="d-flex align-items-center gap-2 text-warning text-darken small fw-semibold">
                                    <i class="bi bi-exclamation-triangle-fill"></i> Digunakan hanya saat jadwal tidak ditemukan di Agenda Sesi Rutin.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Section B: Detail Komponen Wajib Pengisian Laporan -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-check-all text-success fs-4"></i> Detail Komponen Wajib Pengisian Form Laporan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        
                        <!-- Check-in GPS -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border border-light-subtle h-100">
                                <div class="bg-primary text-white rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-geo-alt-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">1. GPS Check-in & Live Selfie</h6>
                                    <p class="small text-muted mb-0">
                                        Diambil langsung dari kamera HP di area sekolah ($\le 500$m). Merekam koordinat presisi dan waktu kedatangan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Absensi Siswa -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border border-light-subtle h-100">
                                <div class="bg-success text-white rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-check-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">2. Absensi Siswa & Ad-Hoc</h6>
                                    <p class="small text-muted mb-0">
                                        Tandai siswa Hadir / Alpha. Siswa baru yang belum ada di list bisa langsung ditambah secara instan di form absensi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Materi & Topik -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border border-light-subtle h-100">
                                <div class="bg-warning text-dark rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-journal-code"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">3. Topik & Materi Pengajaran</h6>
                                    <p class="small text-muted mb-0">
                                        Tuliskan modul, topik utama, serta ringkasan materi pembelajaran yang telah disampaikan kepada siswa.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Foto Kegiatan & Bukti -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border border-light-subtle h-100">
                                <div class="bg-info text-white rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-camera-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">4. Foto Suasana & Absensi</h6>
                                    <p class="small text-muted mb-0">
                                        Unggah foto aktivitas pembelajaran siswa di kelas dan foto lembar bukti absensi fisik yang telah ditandatangani.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- File Project -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border border-light-subtle h-100">
                                <div class="bg-purple text-white rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #6f42c1;">
                                    <i class="bi bi-file-earmark-zip-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">5. File Project (Optional)</h6>
                                    <p class="small text-muted mb-0">
                                        Unggah file project koding (.sb3 Scratch / Micro:bit / Python / PDF) hasil karya siswa jika tersedia.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Refleksi & Evaluasi -->
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3 border border-light-subtle h-100">
                                <div class="bg-danger text-white rounded-circle p-2 flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-chat-quote-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">6. Refleksi Capaian & Kendala</h6>
                                    <p class="small text-muted mb-0">
                                        Catat evaluasi pemahaman siswa serta kendala peralatan/perangkat di kelas (misal: "Servo 360 tidak bergetar").
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Section C: Batas Waktu H+1 & Konsekuensi Keterlambatan -->
            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 d-flex align-items-start gap-3">
                <i class="bi bi-exclamation-octagon-fill fs-2 flex-shrink-0 mt-1"></i>
                <div>
                    <h6 class="fw-bold mb-1">Batas Waktu Pengisian Laporan Mengajar (Tenggat H+1)</h6>
                    <p class="mb-0 small lh-base">
                        Sesuai standar operasional Erlass Institute, seluruh Laporan Mengajar <strong>WAJIB di-submit maksimal 24 jam (H+1)</strong> setelah kelas selesai. Jika pengisian dilakukan melewati H+1, form akan terkunci dan laporan akan ditandai sebagai <strong>Terlambat (H+X)</strong> yang memerlukan persetujuan Request Laporan Susulan oleh Admin.
                    </p>
                </div>
            </div>

        </div>

        <!-- TAB 2: INTERACTIVE FAQ -->
        <div class="tab-pane fade" id="faq-content" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-question-circle text-primary"></i> Pertanyaan Yang Sering Diajukan
                    </h5>

                    <div class="accordion accordion-flush" id="faqAccordion">

                        <!-- FAQ 1 -->
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    📅 1. Kapan saya harus menggunakan Jalur Rutin vs Jalur Ad-Hoc?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small lh-base">
                                    Gunakan <strong>Jalur Rutin (Agenda Kegiatan)</strong> untuk seluruh pertemuan yang sudah memiliki jadwal mingguan resmi. Gunakan <strong>Jalur Ad-Hoc</strong> hanya jika Anda mengajar kelas pengganti atau sesi tambahan yang jadwal resminya belum terdaftar di Agenda Kegiatan.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    📍 2. Bagaimana cara kerja Check-in GPS Real-Time?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small lh-base">
                                    Saat Anda tiba di lokasi sekolah, buka detail Sesi Ekstrakurikuler dan tekan tombol <strong>"📌 Check-in Hadir di Sekolah"</strong>. Sistem akan mengambil koordinat GPS perangkat HP Anda dan meminta foto kamera langsung. Jika Anda berada dalam radius $\le 500$ meter dari sekolah, status check-in Anda akan otomatis terverifikasi 🟢.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                    ⚠️ 3. Mengapa status Check-in saya bernilai "Diluar Radius (Warning)"?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small lh-base">
                                    Status <strong>Diluar Radius</strong> terjadi jika Anda menekan tombol check-in ketika posisi GPS perangkat Anda berjarak lebih dari 500 meter dari titik koordinat sekolah yang terdaftar. Pastikan Anda sudah berada di area lingkungan sekolah sebelum menekan tombol check-in.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                    📝 4. Mengapa laporan saya berlabel "Terlambat (H+4)" padahal waktu datang saya Tepat Waktu?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small lh-base">
                                    Sistem memisahkan antara <strong>Kedisiplinan Kehadiran di Sekolah (Check-in)</strong> dan <strong>Ketepatan Submit Laporan (KPI H+1)</strong>. Jika Anda datang jam 13:00 tepat waktu di hari Jumat, namun baru mengisi dan menekan tombol simpan laporan pada hari Selasa (4 hari kemudian), maka laporan tersebut tercatat sebagai laporan susulan (Terlambat H+4).
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
                                    ⏱️ 5. Berapa batas waktu keterlambatan check-in sebelum dikenakan denda?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small lh-base">
                                    Toleransi keterlambatan check-in adalah <strong>14 menit</strong> (berstatus <em>Warning</em> tanpa denda). Jika check-in dilakukan $\ge 15$ menit dari jam mulai jadwal, sistem secara otomatis menetapkan status <em>Penalty</em> dengan pemotongan denda keterlambatan sebesar Rp 25.000 pada payroll bulanan.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item faq-item">
                            <h2 class="accordion-header" id="headingSix">
                                <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix">
                                    📱 6. Apa yang harus dilakukan jika GPS di HP saya tidak terdeteksi?
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-secondary small lh-base">
                                    1. Pastikan fitur <strong>Location / GPS</strong> di HP Anda sudah dalam posisi aktif (ON).<br>
                                    2. Pastikan peramban (Chrome/Safari) telah diizinkan untuk mengakses lokasi.<br>
                                    3. Muat ulang (refresh) halaman dan coba tekan tombol Check-in kembali.
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    function filterFaq() {
        let input = document.getElementById('faqSearchInput').value.toLowerCase();
        let items = document.querySelectorAll('.faq-item');
        
        // Switch to FAQ tab if searching
        if (input.trim() !== '') {
            let faqTab = new bootstrap.Tab(document.getElementById('faq-tab'));
            faqTab.show();
        }

        items.forEach(function(item) {
            let text = item.innerText.toLowerCase();
            if (text.includes(input)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    }
</script>

<style>
    .card-hover {
        transition: all 0.25s ease;
    }
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endsection
