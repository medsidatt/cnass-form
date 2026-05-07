<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS scheme for generated URLs in production.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureRateLimiters();
    }

    private function configureRateLimiters(): void
    {
        // OTP send: tuned for real users who often need to resend
        // (slow WhatsApp delivery, mistyped number, etc.) while still
        // blocking automated abuse. Limits are layered:
        //   - short window keeps the "Renvoyer" button from being spammed
        //   - hour/day windows prevent sustained abuse against one number
        //   - IP windows allow several employees sharing a network
        RateLimiter::for('otp-send', function (Request $request) {
            $phone = preg_replace('/\D+/', '', (string) $request->input('phone', '')) ?: 'unknown';
            return [
                Limit::perMinute(3)->by('phone:'.$phone),
                Limit::perHour(20)->by('phone:'.$phone),
                Limit::perDay(60)->by('phone:'.$phone),
                Limit::perMinute(30)->by('ip:'.$request->ip()),
                Limit::perHour(200)->by('ip:'.$request->ip()),
            ];
        });

        // OTP check: tight cap on attempts to prevent 6-digit brute force.
        // The per-session attempts counter in VerifyController is the
        // primary defense; this rate limit is a secondary safety net.
        RateLimiter::for('otp-check', function (Request $request) {
            $key = $request->session()->get('otp_phone') ?? $request->ip();
            return [
                Limit::perMinute(8)->by('check:'.$key),
                Limit::perHour(40)->by('check:'.$key),
            ];
        });

        // Form submission: prevents replay/abuse from a single session.
        RateLimiter::for('submit', function (Request $request) {
            return Limit::perMinute(6)->by(
                $request->session()->get('verified_phone') ?? $request->ip()
            );
        });
    }
}
