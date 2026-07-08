<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat - Erlass Institute</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            color: #334155;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .verify-card {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            width: 100%;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .verify-header {
            background: linear-gradient(135deg, #3b82f6 0%, #06b6d4 100%);
            padding: 30px;
            text-align: center;
            color: #ffffff;
        }
        .verify-body {
            padding: 30px;
        }
        .status-badge {
            background-color: #10b981;
            color: #ffffff;
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 1.1rem;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .info-row {
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #64748b;
            font-weight: 500;
            font-size: 0.9rem;
        }
        .info-value {
            color: #0f172a;
            font-weight: bold;
            font-size: 1.05rem;
        }
    </style>
</head>
<body>

    <div class="verify-card">
        <!-- Header -->
        <div class="verify-header">
            <img src="{{ asset('images/logo-erlass.png') }}" alt="Logo Erlass" class="mb-3 bg-white p-2 rounded-circle border" style="height: 60px; width: auto;">
            <h4 class="mb-1 fw-bold">Erlass Institute</h4>
            <p class="mb-0 text-white-50 small">Sistem Verifikasi Sertifikat Kelulusan Publik</p>
        </div>
        
        <!-- Body -->
        <div class="verify-body text-center">
            <!-- Status Badge -->
            <div class="status-badge">
                <i class="bi bi-patch-check-fill"></i> Sertifikat Valid & Asli
            </div>
            
            <p class="text-muted small mb-4">
                Sertifikat dengan kode di bawah ini terdaftar secara resmi di database pembelajaran Erlass Institute dan dinyatakan sah.
            </p>
            
            <!-- Details -->
            <div class="text-start border rounded-4 p-3 bg-light">
                <div class="info-row row">
                    <div class="col-sm-4 info-label">Kode Sertifikat</div>
                    <div class="col-sm-8 info-value font-monospace text-primary">{{ $certificate->certificate_code }}</div>
                </div>
                <div class="info-row row">
                    <div class="col-sm-4 info-label">Nama Lengkap</div>
                    <div class="col-sm-8 info-value">{{ $certificate->siswa->nama_lengkap }}</div>
                </div>
                <div class="info-row row">
                    <div class="col-sm-4 info-label">NISN</div>
                    <div class="col-sm-8 info-value">{{ $certificate->siswa->nisn }}</div>
                </div>
                <div class="info-row row">
                    <div class="col-sm-4 info-label">Program Ekskul</div>
                    <div class="col-sm-8 info-value">{{ $certificate->ekstrakurikuler->kategori_program }}</div>
                </div>
                <div class="info-row row">
                    <div class="col-sm-4 info-label">Mitra Sekolah</div>
                    <div class="col-sm-8 info-value">{{ $certificate->ekstrakurikuler->sekolah->namasekolah }}</div>
                </div>
                <div class="info-row row">
                    <div class="col-sm-4 info-label">Tanggal Terbit</div>
                    <div class="col-sm-8 info-value">{{ $certificate->issued_at->translatedFormat('d F Y') }}</div>
                </div>
                @if($score)
                <div class="info-row row">
                    <div class="col-sm-4 info-label">Predikat Kelulusan</div>
                    <div class="col-sm-8 info-value">
                        <span class="text-success">{{ $score->getKeterangan() }} ({{ $score->getPredikat() }})</span>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="mt-4 pt-2 text-center">
                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4">
                    <i class="bi bi-house-door me-1"></i> Beranda Erlass
                </a>
            </div>
        </div>
    </div>

</body>
</html>
