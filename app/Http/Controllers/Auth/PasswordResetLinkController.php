<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request via WhatsApp (Pilihan A).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'no_telephone' => ['required', 'string', 'min:9', 'max:20'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'no_telephone.required' => 'Nomor WhatsApp terdaftar wajib diisi.',
            'no_telephone.min' => 'Nomor WhatsApp minimal 9 digit.',
        ]);

        // Bersihkan karakter non-digit dari input nomor telepon
        $cleanInputPhone = preg_replace('/[^0-9]/', '', $request->no_telephone);
        $input08 = preg_replace('/^62/', '0', $cleanInputPhone);
        $input62 = preg_replace('/^0/', '62', $cleanInputPhone);

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Ambil dan bersihkan nomor telepon user dari database
        $userPhone = preg_replace('/[^0-9]/', '', $user?->no_telephone ?? '');

        // Validasi kecocokan nomor telepon (Pilihan A: Wajib cocok dengan database)
        $isMatched = $user && ! empty($userPhone) && (
            $userPhone === $cleanInputPhone ||
            $userPhone === $input08 ||
            $userPhone === $input62
        );

        if (! $isMatched) {
            return back()->withInput($request->only('email', 'no_telephone'))
                ->withErrors([
                    'email' => 'Kombinasi alamat email dan nomor WhatsApp tidak cocok dengan data akun terdaftar.',
                ]);
        }

        // Generate token reset password bawaan Laravel
        $token = Password::broker()->createToken($user);
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // Format nomor WhatsApp target ke format 628...
        $targetWa = preg_replace('/^0/', '62', $userPhone);
        if (! str_starts_with($targetWa, '62')) {
            $targetWa = '62' . ltrim($targetWa, '0');
        }

        $tokenFonnte = config('services.whatsapp.fonnte_token');
        if (! $tokenFonnte) {
            Log::error('Password Reset WhatsApp: Token Fonnte tidak dikonfigurasi di config services.whatsapp.fonnte_token.');
            return back()->withInput($request->only('email', 'no_telephone'))
                ->withErrors(['email' => 'Layanan pengiriman WhatsApp sedang tidak tersedia. Silakan hubungi admin.']);
        }

        $appName = config('app.name', 'Erlass Ekskul');
        $expireMinutes = config('auth.passwords.users.expire', 60);
        $displayName = ! empty($user->nama_lengkap) ? $user->nama_lengkap : 'Bapak/Ibu';

        $message = "Halo *{$displayName}*,\n\n"
            . "Kami menerima permintaan reset kata sandi untuk akun Anda di *{$appName}*.\n\n"
            . "Silakan klik tautan di bawah ini untuk membuat kata sandi baru Anda:\n"
            . "🔗 {$resetUrl}\n\n"
            . "⏱️ *Catatan Keamanan:*\n"
            . "• Tautan ini berlaku selama *{$expireMinutes} menit*.\n"
            . "• Jika Anda tidak meminta reset kata sandi, abaikan pesan ini. Akun Anda tetap aman.\n\n"
            . "Salam hormat,\n"
            . "*Tim {$appName}*";

        try {
            $response = Http::timeout(12)->withHeaders([
                'Authorization' => $tokenFonnte,
            ])->post('https://api.fonnte.com/send', [
                'target'      => $targetWa,
                'message'     => $message,
                'countryCode' => '62',
            ]);

            $resBody = $response->json();

            if ($response->successful() && ($resBody['status'] ?? false)) {
                Log::info("Password reset WhatsApp link successfully sent to {$targetWa} for user {$user->email}");

                $maskedPhone = substr($targetWa, 0, 4) . '****' . substr($targetWa, -4);
                return back()->with('status', "Tautan reset kata sandi telah berhasil dikirim ke nomor WhatsApp terdaftar Anda ({$maskedPhone}). Silakan periksa pesan masuk WhatsApp Anda.");
            }

            $reason = $resBody['reason'] ?? ($resBody['message'] ?? $response->body());
            Log::error("Password reset WhatsApp failed for {$targetWa}. HTTP {$response->status()}: {$reason}");

            return back()->withInput($request->only('email', 'no_telephone'))
                ->withErrors(['no_telephone' => 'Gagal mengirim pesan WhatsApp ke nomor Anda. Pastikan nomor aktif di WhatsApp atau hubungi admin.']);

        } catch (\Exception $e) {
            Log::error("Password reset WhatsApp exception for {$targetWa}: " . $e->getMessage());

            return back()->withInput($request->only('email', 'no_telephone'))
                ->withErrors(['no_telephone' => 'Terjadi gangguan koneksi ke gateway WhatsApp. Silakan coba lagi beberapa saat lagi.']);
        }
    }
}
