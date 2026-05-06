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

    // Step 1 – send OTP via WhatsApp
    public function send(Request $request)
    {
        $request->validate(['phone' => 'required|string|min:8']);

        // Normalise to E.164
        $phone = preg_replace('/\s+/', '', $request->phone);
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        try {
            $this->twilio()
                ->verify->v2
                ->services(config('services.twilio.verify_sid'))
                ->verifications
                ->create($phone, 'whatsapp');

            session(['otp_phone' => $phone]);

            return response()->json(['success' => true]);
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
