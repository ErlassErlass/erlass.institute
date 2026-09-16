<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Fonnte Disconnected</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f8; color: #333333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; }
        .header { background: #DC2626; color: #ffffff; padding: 24px; text-align: center; }
        .header h2 { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { margin: 6px 0 0 0; opacity: 0.9; font-size: 13px; }
        .content { padding: 24px; line-height: 1.6; }
        .alert-box { background: #FEF2F2; border-left: 4px solid #DC2626; padding: 14px 16px; border-radius: 6px; margin: 16px 0; }
        .alert-box strong { color: #991B1B; display: block; margin-bottom: 4px; font-size: 14px; }
        .alert-box p { margin: 0; font-size: 13px; color: #7F1D1D; }
        .details-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .details-table td { padding: 10px 12px; border-bottom: 1px solid #f0f2f5; font-size: 13px; }
        .details-table td:first-child { color: #6B7280; width: 35%; font-weight: 600; }
        .details-table td:last-child { color: #111827; font-weight: 500; }
        .btn-wrapper { text-align: center; margin: 28px 0 12px 0; }
        .btn { display: inline-block; background: #2563EB; color: #ffffff !important; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; box-shadow: 0 2px 4px rgba(37,99,235,0.25); }
        .footer { background: #F9FAFB; padding: 16px 24px; text-align: center; font-size: 12px; color: #9CA3AF; border-top: 1px solid #E5E7EB; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🚨 PERINGATAN SISTEM: GATEWAY WA TERPUTUS</h2>
            <p>Erlass Institute WhatsApp Notification System</p>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $webmaster->nama_lengkap }}</strong>,</p>
            <p>Sistem mendeteksi bahwa koneksi <strong>Fonnte WhatsApp Gateway</strong> saat ini berstatus <strong>DISCONNECT</strong> (Terputus).</p>
            
            <div class="alert-box">
                <strong>Dampak Operasional:</strong>
                <p>Seluruh notifikasi WhatsApp otomatis (pengingat jadwal H-1, pengingat laporan harian jam 18:00 WIB, pengiriman slip laporan instruktur, dan broadcast) <strong>TIDAK DAPAT TERKIRIM</strong> sampai WhatsApp ditautkan kembali.</p>
            </div>

            <table class="details-table">
                <tr>
                    <td>Nomor Perangkat</td>
                    <td><strong>{{ $device }}</strong></td>
                </tr>
                <tr>
                    <td>Waktu Terdeteksi</td>
                    <td>{{ $time }}</td>
                </tr>
                <tr>
                    <td>Alasan Kegagalan</td>
                    <td style="color: #DC2626;"><code>{{ $reason }}</code></td>
                </tr>
            </table>

            <h4 style="margin-top: 24px; margin-bottom: 8px; color: #111827;">Langkah Penanganan Cepat:</h4>
            <ol style="font-size: 13px; color: #4B5563; padding-left: 20px; line-height: 1.8;">
                <li>Buka dashboard resmi Fonnte di <a href="https://md.fonnte.com/" style="color: #2563EB;">https://md.fonnte.com/</a></li>
                <li>Masuk ke menu <strong>Device</strong> lalu klik <strong>Connect / Scan QR</strong>.</li>
                <li>Buka aplikasi WhatsApp di HP Admin &gt; <strong>Perangkat Tertaut (*Linked Devices*)</strong> &gt; <strong>Tautkan Perangkat</strong>.</li>
                <li>Scan QR code di layar hingga status berubah menjadi <strong>Connected</strong>.</li>
            </ol>

            <div class="btn-wrapper">
                <a href="https://md.fonnte.com/" class="btn" target="_blank">Buka Dashboard Fonnte &rarr;</a>
            </div>
        </div>
        <div class="footer">
            Email ini dibuat otomatis oleh Sistem Monitoring Erlass Institute &bull; Dikirimkan ke Administrator / Webmaster.
        </div>
    </div>
</body>
</html>
