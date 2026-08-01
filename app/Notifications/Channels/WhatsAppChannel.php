<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppChannel
{
    /**
     * Panjang pesan maksimum yang aman dikirim ke Fonnte.
     * Melebihi ini meningkatkan risiko pesan dipotong atau dianggap spam.
     */
    const MAX_MESSAGE_LENGTH = 4000;

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);
        $raw = $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone_number ?? $notifiable->no_wa ?? null;

        if (! $raw) {
            Log::warning('WhatsApp Notification: No phone number found for notifiable ID ' . ($notifiable->id ?? 'unknown'));
            return;
        }

        // Normalisasi nomor ke format internasional (62xxx)
        $to = $this->normalizePhone($raw);

        if (! $to) {
            Log::warning("WhatsApp Notification: Nomor tidak valid atau tidak dapat dinormalisasi: {$raw}");
            return;
        }

        $provider = config('services.whatsapp.provider', 'log');

        if ($provider === 'log') {
            Log::info("WhatsApp Message to {$to}: \n{$message}");
            return;
        }

        if ($provider === 'fonnte') {
            $this->sendFonnte($to, $message);
        }

        // Add other providers here (Twilio, WaBlas, etc.)
    }

    /**
     * Normalisasi nomor telepon Indonesia ke format 62xxx.
     * Menghapus karakter non-digit, lalu konversi:
     *   08xxx  → 628xxx
     *   628xxx → tetap
     *   +628xx → 628xx
     *
     * @param  string  $phone
     * @return string|null  null jika nomor tidak valid
     */
    protected function normalizePhone(string $phone): ?string
    {
        return \App\Services\PhoneNumberService::normalize($phone);
    }

    /**
     * Kirim pesan via Fonnte API.
     * Menerapkan micro-delay acak untuk mengurangi risiko rate-limit / ban.
     *
     * @param  string  $target  Nomor dalam format 628xxx
     * @param  string  $message
     * @return bool
     */
    protected function sendFonnte(string $target, string $message): bool
    {
        $token = config('services.whatsapp.fonnte_token');

        if (! $token) {
            Log::error('WhatsApp (Fonnte): Token tidak dikonfigurasi.');
            return false;
        }

        // Potong pesan jika terlalu panjang (mengurangi risiko spam filter)
        if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
            Log::warning("WhatsApp (Fonnte): Pesan ke {$target} dipotong dari " . mb_strlen($message) . ' karakter.');
            $message = mb_substr($message, 0, self::MAX_MESSAGE_LENGTH - 3) . '...';
        }

        // Micro-delay acak 1–3 detik untuk menghindari burst sending
        usleep(random_int(1_000_000, 3_000_000));

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $target,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                Log::info("WhatsApp (Fonnte): Pesan terkirim ke {$target}.");
                return true;
            }

            Log::error("WhatsApp (Fonnte): Gagal kirim ke {$target}. HTTP {$response->status()}. Response: " . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error("WhatsApp (Fonnte): Exception saat kirim ke {$target}: " . $e->getMessage());
            return false;
        }
    }
}
