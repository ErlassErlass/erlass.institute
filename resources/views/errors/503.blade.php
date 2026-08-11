<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Pemeliharaan - Erlass Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #2d3436;
        }
        .cute-rotate {
            animation: rotate 6s linear infinite;
        }
        .cute-float {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }
        .brand-badge {
            background: linear-gradient(135deg, #0984e3, #6c5ce7);
            color: white;
            padding: 6px 18px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 100vh; padding: 20px;">
        <div class="text-center" style="max-width: 600px;">
            <div class="mb-3">
                <span class="brand-badge shadow-sm">
                    <i class="fas fa-graduation-cap me-2"></i>Erlass Institute
                </span>
            </div>

            <h1 style="font-size: 7rem; font-weight: 800; color: #0984e3; text-shadow: 4px 4px 0px #74b9ff; margin-bottom: 0;">
                5<span style="display:inline-block; transform: rotate(-8deg);">0</span>3
            </h1>
            
            <div class="mb-4 cute-float">
                <!-- Cute Maintenance Robot with Gear SVG -->
                <svg width="180" height="180" viewBox="0 0 24 24" fill="none" stroke="#0984e3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="10" rx="3" ry="3" fill="#74b9ff" stroke="#0984e3" fill-opacity="0.25"></rect>
                    <circle cx="12" cy="5" r="2.5" fill="#0984e3"></circle>
                    <path d="M12 7.5v3.5"></path>
                    <line x1="8" y1="16" x2="8.01" y2="16" stroke-width="3"></line>
                    <line x1="16" y1="16" x2="16.01" y2="16" stroke-width="3"></line>
                    <path d="M9 19c1 1 5 1 6 0"></path>
                    <!-- Gear -->
                    <g class="cute-rotate" transform-origin="20 4">
                        <circle cx="20" cy="4" r="2" fill="#fdcb6e" stroke="#e17055" stroke-width="1"></circle>
                        <path d="M20 1v1M20 7v1M17 4h1M22 4h1" stroke="#e17055" stroke-width="1"></path>
                    </g>
                </svg>
            </div>

            <h2 class="h3 font-weight-bold mb-3" style="color: #2d3436;">Sistem Sedang Pemeliharaan Berkala</h2>
            <p class="text-muted mb-4 lead" style="font-size: 1.1rem; line-height: 1.6;">
                Kami sedang memperbarui sistem Erlass Institute agar layanan menjadi lebih cepat, aman, dan nyaman untuk Anda. Mohon tunggu beberapa saat ya! 🛠️✨
            </p>
            
            <div class="d-flex justify-content-center gap-2 mt-4">
                <button onclick="window.location.reload()" class="btn btn-lg rounded-pill px-4 shadow-sm text-white" style="background: linear-gradient(135deg, #0984e3, #6c5ce7); border: none; font-weight: 600;">
                    <i class="fas fa-sync-alt me-2"></i> Coba Refresh Halaman
                </button>
            </div>

            <p class="text-muted small mt-4">
                <i class="far fa-clock me-1"></i> Halaman ini akan otomatis mencoba menghubungkan kembali secara berkala.
            </p>
        </div>
    </div>

    <script>
        // Refresh page automatically every 60 seconds during maintenance
        setTimeout(function() {
            window.location.reload();
        }, 60000);
    </script>
</body>
</html>
