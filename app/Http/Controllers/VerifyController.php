<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class VerifyController extends Controller
{
    // Twilio WhatsApp sandbox number (pre-approved, no Meta verification needed)
    private const WHATSAPP_FROM = 'whatsapp:+14155238886';

    private function twilio(): Client
    {
        return new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    private function isDevMode(): bool
    {
        return empty(config('services.twilio.sid')) || empty(config('services.twilio.token'));
    }

    // Step 1 – generate OTP and send via WhatsApp sandbox
    public function send(Request $request)
    {
        $request->validate(['phone' => 'required|string|min:8']);

        $phone = preg_replace('/\s+/', '', $request->phone);
        if (!str_starts_with($phone, '+')) {
            $phone = '+222' . ltrim($phone, '0');
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        session([
            'otp_phone'      => $phone,
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(10)->timestamp,
        ]);

        if ($this->isDevMode()) {
            return response()->json(['success' => true, 'dev' => true]);
        }

        try {
            $this->twilio()->messages->create(
                'whatsapp:' . $phone,
                [
                    'from' => self::WHATSAPP_FROM,
                    'body' => "Votre code de vérification CNASS est : *{$otp}*\n\nCe code est valable 10 minutes.",
                ]
            );

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'envoyer le code WhatsApp : ' . $e->getMessage(),
            ], 422);
        }
    }

    // Step 2 – verify OTP against session
    public function check(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $phone     = session('otp_phone');
        $stored    = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (!$phone || !$stored) {
            return response()->json([
                'success' => false,
                'message' => 'Session expirée. Veuillez renvoyer le code.',
            ], 422);
        }

        if ($this->isDevMode()) {
            if ($request->code === '123456') {
                session()->forget(['otp_phone', 'otp_code', 'otp_expires_at']);
                session(['phone_verified' => true, 'verified_phone' => $phone]);
                return response()->json(['success' => true]);
            }
            return response()->json([
                'success' => false,
                'message' => '[Mode local] Code incorrect — utilisez 123456.',
            ], 422);
        }

        if ($expiresAt && now()->timestamp > $expiresAt) {
            session()->forget(['otp_phone', 'otp_code', 'otp_expires_at']);
            return response()->json([
                'success' => false,
                'message' => 'Code expiré. Veuillez renvoyer le code.',
            ], 422);
        }

        if ($request->code === $stored) {
            session()->forget(['otp_phone', 'otp_code', 'otp_expires_at']);
            session(['phone_verified' => true, 'verified_phone' => $phone]);
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Code incorrect. Veuillez réessayer.',
        ], 422);
    }
}
