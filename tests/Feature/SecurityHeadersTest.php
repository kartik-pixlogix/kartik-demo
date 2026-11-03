<?php

namespace Tests\Feature;

use Database\Seeders\CaseStudySeeder;
use Database\Seeders\InsightSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingSeeder::class, CaseStudySeeder::class, InsightSeeder::class]);
    }

    private function csp(string $url = '/'): string
    {
        return $this->get($url)->headers->get('Content-Security-Policy') ?? '';
    }

    public function test_every_required_header_is_present(): void
    {
        $response = $this->get('/');

        foreach ([
            'Content-Security-Policy',
            'X-Content-Type-Options',
            'Referrer-Policy',
            'Permissions-Policy',
            'X-Frame-Options',
            'Cross-Origin-Opener-Policy',
        ] as $header) {
            $this->assertNotNull($response->headers->get($header), "{$header} is missing");
        }

        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
    }

    public function test_the_script_policy_is_nonce_based_with_no_unsafe_directives(): void
    {
        $csp = $this->csp();

        preg_match("/script-src ([^;]+)/", $csp, $m);
        $scriptSrc = $m[1] ?? '';

        $this->assertStringContainsString("'nonce-", $scriptSrc, 'script-src must be nonce-based');
        $this->assertStringNotContainsString("'unsafe-inline'", $scriptSrc);
        $this->assertStringNotContainsString("'unsafe-eval'", $scriptSrc);
        $this->assertStringNotContainsString('*', $scriptSrc);
    }

    public function test_inline_style_blocks_are_blocked_even_though_attributes_are_allowed(): void
    {
        $csp = $this->csp();

        // The prototype's 58 inline style ATTRIBUTES are the design and must
        // keep working; inline <style> ELEMENTS have no reason to exist.
        $this->assertStringContainsString("style-src-elem 'self'", $csp);
        $this->assertStringNotContainsString("style-src-elem 'self' 'unsafe-inline'", $csp);
    }

    public function test_framing_and_object_embedding_are_denied(): void
    {
        $csp = $this->csp();

        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
        $this->assertStringContainsString("object-src 'none'", $csp);
        $this->assertStringContainsString("base-uri 'self'", $csp);
        $this->assertStringContainsString("form-action 'self'", $csp);
    }

    public function test_the_nonce_is_unique_per_request_and_matches_the_markup(): void
    {
        $first = $this->get('/');
        $second = $this->get('/');

        preg_match("/'nonce-([^']+)'/", $first->headers->get('Content-Security-Policy'), $a);
        preg_match("/'nonce-([^']+)'/", $second->headers->get('Content-Security-Policy'), $b);

        $this->assertNotEmpty($a[1]);
        $this->assertNotSame($a[1], $b[1], 'a nonce must not be reused across requests');

        // every script tag in that response carries that request's nonce
        $html = $first->getContent();
        preg_match_all('/<script\b(?![^>]*\bnonce=)[^>]*>/', $html, $missing);
        $this->assertSame([], $missing[0], 'every script tag must carry the nonce');
        $this->assertStringContainsString('nonce="'.$a[1].'"', $html);
    }

    public function test_the_admin_gets_its_own_policy_because_livewire_needs_eval(): void
    {
        $csp = $this->csp('/admin/login');

        $this->assertStringContainsString("'unsafe-eval'", $csp, 'Livewire and Alpine need it');
        // …but the public site must never inherit that
        $this->assertStringNotContainsString("'unsafe-eval'", $this->csp('/'));
    }

    public function test_hsts_is_only_sent_over_tls(): void
    {
        // Sending HSTS from a plain-HTTP local environment would pin a
        // developer's browser to https://localhost.
        $this->assertNull($this->get('/')->headers->get('Strict-Transport-Security'));
    }

    public function test_permissions_policy_denies_the_powerful_features(): void
    {
        $policy = $this->get('/')->headers->get('Permissions-Policy');

        foreach (['camera', 'microphone', 'geolocation', 'payment', 'usb'] as $feature) {
            $this->assertStringContainsString("{$feature}=()", $policy);
        }
    }

    public function test_headers_are_present_on_errors_too(): void
    {
        $response = $this->get('/services/does-not-exist');

        $response->assertNotFound();
        $this->assertNotNull($response->headers->get('Content-Security-Policy'));
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
    }
}
