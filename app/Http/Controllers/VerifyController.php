<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class VerifyController extends Controller
{
    private function twilio(): Client
    {
        return new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
    }

    private function isDevMode(): bool
    {
        return empty(config('services.twilio.sid')) || empty(config('services.twilio.verify_sid'));
    }

    // Step 1 – send OTP via chosen channel (whatsapp or sms)
    public function send(Request $request)
    {
        $request->validate([
            'phone'   => 'required|string|min:8',
            'channel' => 'in:whatsapp,sms',
        ]);

        $phone = preg_replace('/\s+/', '', $request->phone);
        if (!str_starts_with($phone, '+')) {
            $phone = '+222' . ltrim($phone, '0');
        }

        $channel = $request->input('channel', 'sms');
        session(['otp_phone' => $phone]);

        if ($this->isDevMode()) {
            return response()->json(['success' => true, 'dev' => true, 'channel' => $channel]);
        }

        try {
            $this->twilio()
                ->verify->v2
                ->services(config('services.twilio.verify_sid'))
                ->verifications
                ->create($phone, $channel);

            return response()->json(['success' => true, 'channel' => $channel]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'envoyer le code : ' . $e->getMessage(),
            ], 422);
        }
    }

    // Step 2 – check OTP
    public function check(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $phone = session('otp_phone');
        if (!$phone) {
            return response()->json([
                'success' => false,
                'message' => 'Session expirée. Veuillez renvoyer le code.',
            ], 422);
        }

        if ($this->isDevMode()) {
            if ($request->code === '123456') {
                session()->forget('otp_phone');
                session(['phone_verified' => true, 'verified_phone' => $phone]);
                return response()->json(['success' => true]);
            }
            return response()->json([
                'success' => false,
                'message' => '[Mode local] Code incorrect — utilisez 123456.',
            ], 422);
        }

        try {
            $result = $this->twilio()
                ->verify->v2
                ->services(config('services.twilio.verify_sid'))
                ->verificationChecks
                ->create(['to' => $phone, 'code' => $request->code]);

            if ($result->status === 'approved') {
                session()->forget('otp_phone');
                session(['phone_verified' => true, 'verified_phone' => $phone]);
                return response()->json(['success' => true]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Code incorrect. Veuillez réessayer.',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de vérification : ' . $e->getMessage(),
            ], 422);
        }
    }
}
