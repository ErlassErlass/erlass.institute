<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppChannel
{
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
        $to = $notifiable->routeNotificationFor('whatsapp') ?? $notifiable->phone_number ?? $notifiable->no_wa ?? null;

        if (! $to) {
            Log::warning('WhatsApp Notification: No phone number found for user ' . $notifiable->id);
            return;
        }

        // Format phone number (E.164 or local standard)
        // For now, we assume simple cleaning or existing format
        // In production, use a library like libphonenumber or custom logic
        
        $provider = config('services.whatsapp.provider', 'log'); // Default to log

        if ($provider === 'log') {
            Log::info("WhatsApp Message to {$to}: \n{$message}");
            return;
        }

        if ($provider === 'fonnte') {
             $this->sendFonnte($to, $message);
        }
        
        // Add other providers here (Twilio, etc.)
    }

    protected function sendFonnte($target, $message)
    {
        $token = config('services.whatsapp.fonnte_token');
        
        if (!$token) {
            Log::error('WhatsApp Notification: Fonnte token not configured.');
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->failed()) {
                 Log::error('WhatsApp Notification (Fonnte) Failed: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Notification (Fonnte) Exception: ' . $e->getMessage());
        }
    }
}
