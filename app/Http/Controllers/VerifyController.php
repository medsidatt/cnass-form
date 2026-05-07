<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class VerifyController extends Controller
{
    private const MAX_ATTEMPTS    = 5;
    private const OTP_TTL_MINUTES = 15;

    private function twilio(): Client
    {
        return new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    private function whatsappFrom(): string
    {
        return (string) config('services.twilio.whatsapp_from');
    }

    private function isDevMode(): bool
    {
        return empty(config('services.twilio.sid')) || empty(config('services.twilio.token'));
    }

    /**
     * Step 1 — generate OTP and send via WhatsApp.
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[0-9 +\-]{8,20}$/'],
        ]);

        $phone = $this->normalizePhone($request->input('phone'));

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put([
            'otp_phone'      => $phone,
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES)->timestamp,
            'otp_attempts'   => 0,
        ]);

        if ($this->isDevMode()) {
            Log::info('OTP sent (dev mode)', ['phone' => $phone]);
            return response()->json(['success' => true, 'dev' => true]);
        }

        try {
            $this->twilio()->messages->create(
                'whatsapp:' . $phone,
                [
                    'from' => $this->whatsappFrom(),
                    'body' => "Votre code de vérification CNASS est : *{$otp}*\n\nCe code est valable " . self::OTP_TTL_MINUTES . " minutes.",
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::warning('Twilio WhatsApp send failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'envoyer le code WhatsApp pour le moment. Veuillez réessayer dans quelques instants.',
            ], 422);
        }
    }

    /**
     * Step 2 — verify OTP code.
     */
    public function check(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $phone     = $request->session()->get('otp_phone');
        $stored    = $request->session()->get('otp_code');
        $expiresAt = $request->session()->get('otp_expires_at');
        $attempts  = (int) $request->session()->get('otp_attempts', 0);

        if (!$phone || !$stored) {
            return response()->json([
                'success' => false,
                'message' => 'Session expirée. Veuillez renvoyer le code.',
            ], 422);
        }

        if ($attempts >= self::MAX_ATTEMPTS) {
            $request->session()->forget(['otp_phone', 'otp_code', 'otp_expires_at', 'otp_attempts']);
            return response()->json([
                'success' => false,
                'message' => 'Trop de tentatives. Veuillez renvoyer un nouveau code.',
            ], 429);
        }

        if ($expiresAt && now()->timestamp > (int) $expiresAt) {
            $request->session()->forget(['otp_phone', 'otp_code', 'otp_expires_at', 'otp_attempts']);
            return response()->json([
                'success' => false,
                'message' => 'Code expiré. Veuillez renvoyer le code.',
            ], 422);
        }

        $expected = $this->isDevMode() ? '123456' : (string) $stored;

        if (!hash_equals($expected, (string) $request->input('code'))) {
            $request->session()->put('otp_attempts', $attempts + 1);
            $remaining = max(0, self::MAX_ATTEMPTS - ($attempts + 1));
            $msg = $this->isDevMode()
                ? '[Mode local] Code incorrect — utilisez 123456.'
                : 'Code incorrect. Veuillez réessayer.';
            return response()->json([
                'success'              => false,
                'message'              => $msg,
                'attempts_remaining'   => $remaining,
            ], 422);
        }

        // Success: rotate the session ID to mitigate fixation, then mark verified.
        $request->session()->forget(['otp_phone', 'otp_code', 'otp_expires_at', 'otp_attempts']);
        $request->session()->regenerate();
        $request->session()->put([
            'phone_verified' => true,
            'verified_phone' => $phone,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Normalize a Mauritanian phone number to E.164 (+222XXXXXXXX).
     */
    private function normalizePhone(string $raw): string
    {
        $phone = preg_replace('/[\s\-]+/', '', $raw);
        if (!str_starts_with($phone, '+')) {
            $phone = '+222' . ltrim($phone, '0');
        }
        return $phone;
    }
}
