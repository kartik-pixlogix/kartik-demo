<?php

namespace Tests\Feature;

use Database\Seeders\CaseStudySeeder;
use Database\Seeders\InsightSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Structural guards for the homepage.
 *
 * Pixel fidelity is covered by the Playwright harness in tests/visual/; these
 * assertions catch the things that would silently break the port server-side —
 * a section component failing to render, the shared SVG defs going missing, or
 * a placeholder being "helpfully" filled in.
 *
 * Since the launch brief (23 November) the homepage carries the approved
 * launch content and no placeholders or unverified claims; these run against
 * the seeded settings, which still hold placeholders the page must not print.
 */
class HomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingSeeder::class, CaseStudySeeder::class, InsightSeeder::class]);
    }

    private function html(): string
    {
        $response = $this->get('/');
        $response->assertOk();

        return $response->getContent();
    }

    public function test_it_renders_the_homepage(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_it_renders_the_launch_sections_in_order(): void
    {
        $html = $this->html();

        // Launch brief (23 November): hero, services, about, approach,
        // industries, contact. Trust, SOC, frameworks, case files and field
        // notes are hidden until there is verified content behind them.
        $markers = [
            'header class="hdr"',
            'nav class="mmenu"',
            'section class="hero" id="top"',
            'section class="sec svc-sec" id="services"',
            'section class="stm" id="about-ub-shield"',
            'section class="sec" id="approach"',
            'section class="sec" id="industries"',
            'section class="strip" id="contact"',
            'footer class="foot"',
        ];

        $last = -1;
        foreach ($markers as $marker) {
            $at = strpos($html, $marker);
            $this->assertNotFalse($at, "section marker missing: {$marker}");
            $this->assertGreaterThan($last, $at, "section out of order: {$marker}");
            $last = $at;
        }

        foreach (['class="trust"', 'id="soc"', 'id="compliance"', 'id="insights"', 'class="stats'] as $hidden) {
            $this->assertStringNotContainsString($hidden, $html, "hidden for launch: {$hidden}");
        }
    }

    public function test_it_emits_the_shared_svg_defs_exactly_once(): void
    {
        $html = $this->html();

        foreach (['gSl', 'gBl', 'dots', 'sh', 'embc'] as $id) {
            $this->assertSame(1, substr_count($html, 'id="'.$id.'"'), "def {$id} should appear exactly once");
        }

        $this->assertGreaterThan(0, substr_count($html, 'href="#sh"'));
        $this->assertGreaterThan(0, substr_count($html, 'url(#dots)'));
    }

    public function test_it_renders_the_shield_emblem_three_times_scanning_in_two(): void
    {
        $html = $this->html();

        /*
         * The emblem is the client's own mark, not the prototype's drawing of
         * it — docs/prototype-fixes.md A7. The placement is asserted rather
         * than just the filename because the four numbers are duplicated in
         * shield-emblem.blade.php and brand-defs.blade.php and have to agree,
         * or the scan beam slides off the artwork.
         *
         * 4 = the three emblems (hero, closing strip, footer) plus the copy
         * inside the #embm mask that holds the beam to the silhouette.
         */
        $this->assertSame(4, substr_count($html, 'x="8.7" y="25.3" width="222.6" height="229.4"'));

        // only the hero and closing-strip emblems carry the scan beam
        $this->assertSame(2, substr_count($html, 'mask="url(#embm)"'));

        /*
         * The hand-drawn facets are gone from the rendered page. The one
         * remaining copy is the #embc clipPath in <x-svg-defs />, which is now
         * unused but stays because that file is generated from the prototype.
         */
        $this->assertSame(
            1,
            substr_count($html, 'M112 10 24 40v106c0 50 38 88 88 120Z'),
            'only the unused #embc def should still carry the prototype outline',
        );

        // and the header lockup uses the same mark
        $this->assertStringContainsString('<img class="m" src="/images/brand/ub-shield.webp"', $html);
    }

    public function test_it_carries_the_launch_brief_content(): void
    {
        $html = $this->get('/');

        $html->assertSeeText('Cybersecurity • Network Solutions • IT Infrastructure')
            ->assertSeeText('Secure')->assertSeeText('Tomorrow.')
            ->assertSeeText('Explore our services')
            ->assertSeeText('Talk to our team')
            ->assertSeeText('Ahmedabad and Rajkot, Gujarat, India');

        foreach (config('practices') as $service) {
            $html->assertSeeText($service['nav']);
        }
        foreach (config('site.industries') as [$name]) {
            $html->assertSeeText($name);
        }
        foreach (config('site.approach') as [$step]) {
            $html->assertSeeText($step);
        }
    }

    public function test_it_shows_no_placeholders_or_unverified_claims(): void
    {
        // Launch brief §8: no placeholder metrics, sample logos, credentials,
        // 24/7 SOC, compliance services, case studies or contact placeholders.
        // The seeded settings still hold "[email address]" etc.; the page must
        // leave them out rather than print them.
        $text = strip_tags($this->html());

        foreach ([
            '[XX]', '[email address]', '[phone number]', '[Street address]', '[status]',
            '[if applicable]', '[Case study', '[DD MMM 2026]', 'Client logo',
            '24/7', 'SOC 2', 'ISO/IEC 27001', 'CERT-In', 'DPDPA', 'HIPAA', 'PCI DSS',
            'posture review', 'Forty-five minutes', 'one business day', 'Tested.',
        ] as $claim) {
            $this->assertStringNotContainsString($claim, $text, "must not appear: {$claim}");
        }
    }

    public function test_confirmed_contact_details_appear_once_set(): void
    {
        \App\Models\Setting::query()->where('key', 'contact_email')->update(['value' => 'hello@ubshield.example']);
        \Illuminate\Support\Facades\Cache::flush();

        $this->get('/')->assertSee('mailto:hello@ubshield.example', escape: false);
    }

    public function test_it_self_hosts_the_four_v7_fonts_and_fetches_none_from_a_cdn(): void
    {
        $manifest = public_path('build/manifest.json');
        if (! file_exists($manifest)) {
            $this->markTestSkipped('run `npm run build` first');
        }

        $entry = json_decode(file_get_contents($manifest), true)['resources/css/app.css']['file'];
        $css = file_get_contents(public_path('build/'.$entry));

        foreach ([
            'sofia-sans-extra-condensed-var.woff2',
            'instrument-sans-var.woff2',
            'big-shoulders-display-400.woff2',
            'michroma-400.woff2',
        ] as $font) {
            $this->assertStringContainsString($font, $css);
        }

        $this->assertStringNotContainsString('fonts.googleapis.com', $css);
        $this->assertStringNotContainsString('fonts.bunny.net', $css);
    }
}
