<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class VerifyController extends Controller
{
    private const MAX_ATTEMPTS    = 5;
    private const OTP_TTL_MINUTES = 15;
    private const CHANNEL         = 'whatsapp';

    private function twilio(): Client
    {
        return new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    private function verifyServiceSid(): ?string
    {
        $sid = (string) config('services.twilio.verify_sid');
        return $sid !== '' ? $sid : null;
    }

    private function isDevMode(): bool
    {
        return empty(config('services.twilio.sid'))
            || empty(config('services.twilio.token'))
            || empty(config('services.twilio.verify_sid'));
    }

    /**
     * Step 1 — ask Twilio Verify to deliver an OTP via WhatsApp.
     */
    public function send(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'regex:/^[0-9 +\-]{8,20}$/'],
        ]);

        $phone = $this->normalizePhone($request->input('phone'));

        $request->session()->put([
            'otp_phone'      => $phone,
            'otp_expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES)->timestamp,
            'otp_attempts'   => 0,
        ]);

        if ($this->isDevMode()) {
            Log::info('OTP send (dev mode)', ['phone' => $phone]);
            return response()->json(['success' => true, 'dev' => true]);
        }

        try {
            $verification = $this->twilio()
                ->verify->v2
                ->services($this->verifyServiceSid())
                ->verifications
                ->create($phone, self::CHANNEL);

            Log::info('Twilio Verify send', [
                'phone'   => $phone,
                'sid'     => $verification->sid,
                'status'  => $verification->status,
                'channel' => $verification->channel,
            ]);

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::warning('Twilio Verify send failed', [
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
     * Step 2 — check the user-supplied code against Twilio Verify.
     */
    public function check(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        $phone     = $request->session()->get('otp_phone');
        $expiresAt = $request->session()->get('otp_expires_at');
        $attempts  = (int) $request->session()->get('otp_attempts', 0);

        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Session expirée. Veuillez renvoyer le code.',
            ], 422);
        }

        if ($attempts >= self::MAX_ATTEMPTS) {
            $request->session()->forget(['otp_phone', 'otp_expires_at', 'otp_attempts']);
            return response()->json([
                'success' => false,
                'message' => 'Trop de tentatives. Veuillez renvoyer un nouveau code.',
            ], 429);
        }

        if ($expiresAt && now()->timestamp > (int) $expiresAt) {
            $request->session()->forget(['otp_phone', 'otp_expires_at', 'otp_attempts']);
            return response()->json([
                'success' => false,
                'message' => 'Code expiré. Veuillez renvoyer le code.',
            ], 422);
        }

        // Dev mode: any 6-digit value works as long as it equals 123456.
        if ($this->isDevMode()) {
            if ($request->input('code') === '123456') {
                $this->markVerified($request, $phone);
                return response()->json(['success' => true]);
            }
            $request->session()->put('otp_attempts', $attempts + 1);
            return response()->json([
                'success' => false,
                'message' => '[Mode local] Code incorrect — utilisez 123456.',
                'attempts_remaining' => max(0, self::MAX_ATTEMPTS - ($attempts + 1)),
            ], 422);
        }

        try {
            $check = $this->twilio()
                ->verify->v2
                ->services($this->verifyServiceSid())
                ->verificationChecks
                ->create([
                    'to'   => $phone,
                    'code' => (string) $request->input('code'),
                ]);
        } catch (\Throwable $e) {
            // Twilio returns 404 once the verification has expired or already approved/canceled.
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'not found') || str_contains($msg, 'expired')) {
                $request->session()->forget(['otp_phone', 'otp_expires_at', 'otp_attempts']);
                return response()->json([
                    'success' => false,
                    'message' => 'Code expiré ou invalide. Veuillez renvoyer le code.',
                ], 422);
            }
            Log::warning('Twilio Verify check failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur de vérification. Veuillez réessayer.',
            ], 422);
        }

        if ($check->status === 'approved') {
            $this->markVerified($request, $phone);
            return response()->json(['success' => true]);
        }

        $request->session()->put('otp_attempts', $attempts + 1);
        $remaining = max(0, self::MAX_ATTEMPTS - ($attempts + 1));
        return response()->json([
            'success'              => false,
            'message'              => 'Code incorrect. Veuillez réessayer.',
            'attempts_remaining'   => $remaining,
        ], 422);
    }

    private function markVerified(Request $request, string $phone): void
    {
        $request->session()->forget(['otp_phone', 'otp_expires_at', 'otp_attempts']);
        $request->session()->regenerate();
        $request->session()->put([
            'phone_verified' => true,
            'verified_phone' => $phone,
        ]);
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
