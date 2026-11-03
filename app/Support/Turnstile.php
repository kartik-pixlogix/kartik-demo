<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cloudflare Turnstile, behind an env flag and off by default.
 *
 * Fails OPEN on a network or provider error: a Cloudflare outage should not
 * take the lead form down with it. The honeypot and the minimum fill time are
 * always on and do not depend on a third party, so this is a third layer
 * rather than the only one.
 */
class Turnstile
{
    public static function enabled(): bool
    {
        return (bool) config('review.turnstile.enabled')
            && filled(config('review.turnstile.secret_key'));
    }

    public static function passes(?string $token, ?string $ip = null): bool
    {
        if (! self::enabled()) {
            return true;
        }

        if (blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(config('review.turnstile.verify_url'), array_filter([
                    'secret' => config('review.turnstile.secret_key'),
                    'response' => $token,
                    'remoteip' => $ip,
                ]));
        } catch (\Throwable $e) {
            Log::warning('Turnstile verification could not be reached, allowing the submission.', [
                'exception' => $e->getMessage(),
            ]);

            return true;
        }

        if ($response->failed()) {
            Log::warning('Turnstile verification returned an error, allowing the submission.', [
                'status' => $response->status(),
            ]);

            return true;
        }

        return (bool) $response->json('success', false);
    }
}
