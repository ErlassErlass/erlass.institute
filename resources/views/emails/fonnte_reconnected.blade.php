<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemberitahuan Fonnte Tersambung Kembali</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f6f8; color: #333333; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #e5e7eb; }
        .header { background: #059669; color: #ffffff; padding: 24px; text-align: center; }
        .header h2 { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { margin: 6px 0 0 0; opacity: 0.9; font-size: 13px; }
        .content { padding: 24px; line-height: 1.6; }
        .success-box { background: #ECFDF5; border-left: 4px solid #059669; padding: 14px 16px; border-radius: 6px; margin: 16px 0; }
        .success-box strong { color: #065F46; display: block; margin-bottom: 4px; font-size: 14px; }
        .success-box p { margin: 0; font-size: 13px; color: #047857; }
        .details-table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        .details-table td { padding: 10px 12px; border-bottom: 1px solid #f0f2f5; font-size: 13px; }
        .details-table td:first-child { color: #6B7280; width: 35%; font-weight: 600; }
        .details-table td:last-child { color: #111827; font-weight: 500; }
        .footer { background: #F9FAFB; padding: 16px 24px; text-align: center; font-size: 12px; color: #9CA3AF; border-top: 1px solid #E5E7EB; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>✅ GATEWAY WHATSAPP PULIH &amp; TERSAMBUNG</h2>
            <p>Erlass Institute WhatsApp Notification System</p>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $webmaster->nama_lengkap }}</strong>,</p>
            <p>Kabar baik, koneksi <strong>Fonnte WhatsApp Gateway</strong> telah berhasil <strong>TERSAMBUNG KEMBALI (*CONNECTED*)</strong>.</p>
            
            <div class="success-box">
                <strong>Status Layanan Pulih:</strong>
                <p>Seluruh antrean pesan notifikasi WhatsApp otomatis dari sistem kini dapat terkirim secara normal dan lancar.</p>
            </div>

            <table class="details-table">
                <tr>
                    <td>Nomor Perangkat</td>
                    <td><strong>{{ $device }}</strong></td>
                </tr>
                <tr>
                    <td>Waktu Pulih</td>
                    <td>{{ $time }}</td>
                </tr>
                <tr>
                    <td>Status Koneksi</td>
                    <td><span style="color: #059669; font-weight: 700;">🟢 CONNECTED</span></td>
                </tr>
            </table>

            <p style="font-size: 13px; color: #6B7280; margin-top: 20px;">
                Peringatan sistem pada Dashboard Admin terkait gateway WhatsApp telah otomatis ditandai selesai (*resolved*).
            </p>
        </div>
        <div class="footer">
            Email ini dibuat otomatis oleh Sistem Monitoring Erlass Institute &bull; Dikirimkan ke Administrator / Webmaster.
        </div>
    </div>
</body>
</html>
