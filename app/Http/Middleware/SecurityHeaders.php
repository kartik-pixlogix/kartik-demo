<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers for every response.
 *
 * This is a security company's own site, so it should pass the audit it would
 * run on a client. Target: A+ on securityheaders.com and a clean Mozilla
 * Observatory run.
 *
 * ON THE CONTENT SECURITY POLICY
 *
 * script-src is nonce-based with no 'unsafe-inline' and no 'unsafe-eval', which
 * is the strict part BUILD.md asks for and the part that actually stops XSS.
 * The site has no inline <script> blocks, no on* handlers and no javascript:
 * URLs, so nothing had to be loosened to achieve it — the only inline script is
 * the JSON-LD, which carries the nonce.
 *
 * style-src is the deliberate exception. The approved prototype carries 58
 * inline `style` attributes — animation delays, blip positions, per-instance
 * sizes — and those values are the design; rewriting them into classes would
 * mean editing the source of truth. So:
 *
 *   style-src-elem 'self'          <- no inline <style> blocks, no foreign CSS
 *   style-src 'self' 'unsafe-inline' <- inline ATTRIBUTES only
 *
 * The fallback ordering matters: browsers that support style-src-elem use it
 * for <style>/<link> and fall back to style-src for attributes, so modern
 * browsers get the stricter rule and older ones still render. An attacker who
 * can inject a style attribute already has HTML injection, so the residual risk
 * is far lower than it would be for scripts.
 *
 * Filament gets a looser policy: Livewire and Alpine need inline scripts and
 * eval, and /admin is authenticated and not the public attack surface.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        // One nonce per request, shared with Vite so its tags carry it too.
        $nonce = Str::random(32);
        Vite::useCspNonce($nonce);
        $request->attributes->set('csp_nonce', $nonce);

        $response = $next($request);

        foreach ($this->headers($request, $nonce) as $name => $value) {
            $response->headers->set($name, $value);
        }

        // Nothing here should be cached by a shared proxy with its nonce.
        if (! $response->headers->has('Cache-Control')) {
            $response->headers->set('Cache-Control', 'no-store, private');
        }

        return $response;
    }

    /** @return array<string, string> */
    private function headers(Request $request, string $nonce): array
    {
        $headers = [
            'Content-Security-Policy' => $this->csp($request, $nonce),
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => $this->permissionsPolicy(),
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'X-Frame-Options' => 'DENY',
            'X-Permitted-Cross-Domain-Policies' => 'none',
        ];

        // HSTS is only meaningful over TLS, and sending it from a plain-HTTP
        // local environment would pin developers' browsers to https://localhost.
        if ($request->secure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains; preload';
        }

        return $headers;
    }

    private function csp(Request $request, string $nonce): string
    {
        $self = "'self'";

        $policy = [
            'default-src' => [$self],
            'base-uri' => [$self],
            'object-src' => ["'none'"],
            'frame-ancestors' => ["'none'"],
            'form-action' => [$self],
            'img-src' => [$self, 'data:'],
            'font-src' => [$self],
            'connect-src' => [$self],
            'manifest-src' => [$self],
            'media-src' => ["'none'"],
            'worker-src' => [$self],
            'frame-src' => ["'none'"],
        ];

        if ($this->isAdmin($request)) {
            // Livewire and Alpine evaluate expressions at runtime and inject
            // inline scripts and styles. Scoped to the authenticated panel.
            $policy['script-src'] = [$self, "'unsafe-inline'", "'unsafe-eval'"];
            $policy['style-src'] = [$self, "'unsafe-inline'"];
            $policy['img-src'][] = 'blob:';
            $policy['connect-src'][] = 'blob:';
            $policy['worker-src'][] = 'blob:';
        } else {
            $policy['script-src'] = [$self, "'nonce-{$nonce}'"];
            // see the class docblock: elem is strict, attributes are not
            $policy['style-src-elem'] = [$self];
            $policy['style-src'] = [$self, "'unsafe-inline'"];
        }

        if (config('review.turnstile.enabled')) {
            $policy['script-src'][] = 'https://challenges.cloudflare.com';
            $policy['frame-src'] = ['https://challenges.cloudflare.com'];
        }

        if ($request->secure()) {
            $policy['upgrade-insecure-requests'] = [];
        }

        return collect($policy)
            ->map(fn (array $values, string $directive) => trim($directive.' '.implode(' ', $values)))
            ->implode('; ');
    }

    /**
     * Deny every powerful feature the site does not use. Written out rather
     * than left to defaults so a future addition is a deliberate edit.
     */
    private function permissionsPolicy(): string
    {
        $denied = [
            'accelerometer', 'ambient-light-sensor', 'autoplay', 'battery', 'camera',
            'display-capture', 'document-domain', 'encrypted-media', 'fullscreen',
            'geolocation', 'gyroscope', 'magnetometer', 'microphone', 'midi',
            'payment', 'picture-in-picture', 'publickey-credentials-get',
            'screen-wake-lock', 'usb', 'xr-spatial-tracking', 'interest-cohort',
        ];

        return collect($denied)->map(fn (string $f) => "{$f}=()")->implode(', ');
    }

    private function isAdmin(Request $request): bool
    {
        return $request->is('admin') || $request->is('admin/*')
            || $request->is('livewire/*') || $request->is('filament/*');
    }
}
