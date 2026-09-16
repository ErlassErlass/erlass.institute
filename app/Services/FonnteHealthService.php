<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Warning;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteHealthService
{
    protected const CACHE_ALERT_KEY = 'fonnte_disconnect_alert_sent';
    protected const CACHE_DISCONNECT_FLAG = 'fonnte_is_disconnected';
    protected const COOLDOWN_HOURS = 2;

    /**
     * Check device status directly against Fonnte API.
     */
    public function checkDeviceStatus(): array
    {
        $token = config('services.whatsapp.fonnte_token');

        if (empty($token)) {
            return [
                'success' => false,
                'connected' => false,
                'device_status' => 'not_configured',
                'device' => null,
                'reason' => 'Token Fonnte belum dikonfigurasi pada .env / config',
                'raw' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->timeout(10)->post('https://api.fonnte.com/device');

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'connected' => false,
                    'device_status' => 'http_error_' . $response->status(),
                    'device' => null,
                    'reason' => 'HTTP Error ' . $response->status() . ': ' . $response->body(),
                    'raw' => $response->json() ?? $response->body(),
                ];
            }

            $data = $response->json() ?? [];
            $deviceStatus = strtolower($data['device_status'] ?? 'unknown');
            $isConnected = ($deviceStatus === 'connect');

            return [
                'success' => true,
                'connected' => $isConnected,
                'device_status' => $deviceStatus,
                'device' => $data['device'] ?? null,
                'name' => $data['name'] ?? null,
                'quota' => $data['quota'] ?? 0,
                'package' => $data['package'] ?? null,
                'expired' => $data['expired'] ?? null,
                'reason' => $isConnected ? null : 'Perangkat tidak terhubung (status: ' . $deviceStatus . ')',
                'raw' => $data,
            ];
        } catch (\Exception $e) {
            Log::error('FonnteHealthService checkDeviceStatus exception: ' . $e->getMessage());
            return [
                'success' => false,
                'connected' => false,
                'device_status' => 'exception',
                'device' => null,
                'reason' => $e->getMessage(),
                'raw' => null,
            ];
        }
    }

    /**
     * Check status and trigger notification or auto-recovery.
     */
    public function checkAndHandle(bool $forceAlert = false): array
    {
        $status = $this->checkDeviceStatus();

        if ($status['success']) {
            if (!$status['connected']) {
                $this->notifyWebmasterDisconnected(
                    $status['reason'] ?? 'Perangkat WhatsApp terputus',
                    $status['device'] ?? null,
                    $forceAlert
                );
            } else {
                $this->notifyWebmasterReconnected($status['device'] ?? null);
            }
        }

        return $status;
    }

    /**
     * Trigger notification to Webmasters that Fonnte is disconnected.
     */
    public function notifyWebmasterDisconnected(string $reason, ?string $device = null, bool $force = false): void
    {
        $device = $device ?: '621282537012';

        // Set persistent disconnect flag in cache
        Cache::forever(self::CACHE_DISCONNECT_FLAG, true);

        // Anti-spam cooldown (2 jam)
        if (!$force && Cache::has(self::CACHE_ALERT_KEY)) {
            Log::warning("FonnteHealthService: Alert disconnect untuk device {$device} di-skip karena cooldown aktif.");
            return;
        }

        Cache::put(self::CACHE_ALERT_KEY, now()->toIso8601String(), now()->addHours(self::COOLDOWN_HOURS));

        Log::critical("FonnteHealthService: Device {$device} DISCONNECTED. Reason: {$reason}. Memproses notifikasi ke Webmaster...");

        // 1. In-App Notification untuk Webmaster & Admin Sistem
        try {
            Notification::create([
                'type' => 'gateway_alert',
                'target_roles' => 'webmaster,admin_sistem',
                'title' => '🚨 Perangkat WhatsApp Gateway (Fonnte) Terputus',
                'message' => "Perangkat WhatsApp Fonnte ({$device}) berstatus DISCONNECT. Alasan: {$reason}. Notifikasi sistem (pengingat jadwal & kirim laporan) terhenti sementara. Segera tautkan ulang WhatsApp.",
                'data' => [
                    'device' => $device,
                    'status' => 'disconnect',
                    'reason' => $reason,
                    'action_url' => 'https://md.fonnte.com/',
                    'detected_at' => now()->toDateTimeString(),
                ],
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('FonnteHealthService: Gagal membuat In-App Notification: ' . $e->getMessage());
        }

        // 2. Warning Merah (System Warning)
        try {
            $existingWarning = Warning::where('warning_type', 'gateway_disconnect')
                ->where('status', 'active')
                ->first();

            if (!$existingWarning) {
                Warning::create([
                    'warning_type' => 'gateway_disconnect',
                    'sourceable_type' => null,
                    'sourceable_id' => null,
                    'severity' => 'red',
                    'status' => 'active',
                    'notes' => "Perangkat WhatsApp Gateway Fonnte ({$device}) TERPUTUS (Disconnected). Alasan: {$reason}. Notifikasi WhatsApp sistem terhenti.",
                ]);
            }
        } catch (\Exception $e) {
            Log::error('FonnteHealthService: Gagal membuat System Warning: ' . $e->getMessage());
        }

        // 3. Activity Log
        try {
            ActivityLog::create([
                'user_id' => 1,
                'action' => 'fonnte_disconnect_alert',
                'description' => "Perangkat WhatsApp Fonnte ({$device}) terputus: {$reason}. Peringatan sistem dan notifikasi telah dibuat.",
                'subject_type' => null,
                'subject_id' => null,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => 'FonnteHealthService',
            ]);
        } catch (\Exception $e) {
            Log::error('FonnteHealthService: Gagal mencatat ActivityLog: ' . $e->getMessage());
        }
    }

    /**
     * Handle recovery when device connects back.
     */
    public function notifyWebmasterReconnected(?string $device = null): void
    {
        $device = $device ?: '621282537012';

        $wasDisconnected = Cache::get(self::CACHE_DISCONNECT_FLAG, false)
            || Warning::where('warning_type', 'gateway_disconnect')->where('status', 'active')->exists();

        if ($wasDisconnected) {
            Log::info("FonnteHealthService: Device {$device} RECONNECTED. Menyelesaikan peringatan aktif...");

            // 1. Selesaikan System Warning aktif
            Warning::where('warning_type', 'gateway_disconnect')
                ->where('status', 'active')
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'resolved_by' => 1,
                    'notes' => "Perangkat WhatsApp Fonnte ({$device}) telah berhasil terhubung kembali pada " . now()->toDateTimeString(),
                ]);

            // 2. In-App Recovery Notification
            Notification::create([
                'type' => 'gateway_alert',
                'target_roles' => 'webmaster,admin_sistem',
                'title' => '✅ WhatsApp Gateway (Fonnte) Tersambung Kembali',
                'message' => "Perangkat WhatsApp Fonnte ({$device}) telah berhasil terhubung kembali. Layanan pengiriman pesan WhatsApp telah pulih dan aktif.",
                'data' => [
                    'device' => $device,
                    'status' => 'connect',
                    'reconnected_at' => now()->toDateTimeString(),
                ],
                'is_read' => false,
            ]);

            // 3. Activity Log
            ActivityLog::create([
                'user_id' => 1,
                'action' => 'fonnte_reconnected',
                'description' => "Perangkat WhatsApp Fonnte ({$device}) kembali terhubung. Layanan notifikasi WhatsApp aktif kembali.",
                'subject_type' => null,
                'subject_id' => null,
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => 'FonnteHealthService',
            ]);

            // Bersihkan flag cache
            Cache::forget(self::CACHE_ALERT_KEY);
            Cache::forget(self::CACHE_DISCONNECT_FLAG);
        }
    }
}
