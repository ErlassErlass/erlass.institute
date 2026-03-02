<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

$token = env('FONNTE_TOKEN');
$target = '082117071800';
$message = "Halo! Ini adalah pesan pengujian integrasi WhatsApp Fonnte dari sistem WEBAPPERLASS.\n\nJika pesan ini diterima, maka Fonnte Anda aktif dan berjalan dengan baik.";

echo "Menggunakan FONNTE_TOKEN: " . ($token ? substr($token, 0, 5) . '***' : 'TIDAK DITEMUKAN') . "\n";
echo "Mengirim ke: " . $target . "\n";

try {
    $response = Http::withHeaders([
        'Authorization' => $token,
    ])->post('https://api.fonnte.com/send', [
        'target' => $target,
        'message' => $message,
        'countryCode' => '62',
    ]);

    if ($response->successful()) {
        echo "BERHASIL!\n";
        echo "Response: " . $response->body() . "\n";
    } else {
        echo "GAGAL!\n";
        echo "Status: " . $response->status() . "\n";
        echo "Error: " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
}
