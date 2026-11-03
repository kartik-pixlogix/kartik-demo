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
 * Since Phase 2 the statistics, credentials, case files and field notes come
 * from the database, so these run against the seeded content — which is itself
 * asserted to reproduce the prototype's placeholders exactly.
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

    public function test_it_renders_every_section_in_the_prototype_order(): void
    {
        $html = $this->html();

        $markers = [
            'header class="hdr"',
            'nav class="mmenu"',
            'section class="hero" id="top"',
            'section class="trust"',
            'section class="sec svc-sec" id="services"',
            'section class="stm"',
            'class="wrap proc"',
            'section class="soc ink-sec" id="soc"',
            'section class="sec" id="industries"',
            'section class="sec" id="compliance"',
            'section class="sec" id="about"',
            'section class="sec" id="insights"',
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
    }

    public function test_it_emits_the_shared_svg_defs_exactly_once(): void
    {
        $html = $this->html();

        foreach (['gSl', 'gBl', 'dots', 'sh', 'embc'] as $id) {
            $this->assertSame(1, substr_count($html, 'id="'.$id.'"'), "def {$id} should appear exactly once");
        }

        $this->assertSame(8, substr_count($html, 'href="#sh"'));
        $this->assertSame(18, substr_count($html, 'url(#dots)'));
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

    public function test_it_keeps_every_placeholder_exactly_as_drawn(): void
    {
        $html = $this->html();

        // nothing may be invented before the client confirms it — docs/inventory.md §15
        $this->assertSame(6, substr_count($html, '[XX]'));
        $this->assertSame(16, substr_count($html, 'Client logo'));
        $this->assertSame(2, substr_count($html, '[email address]'));
        $this->assertSame(2, substr_count($html, '[phone number]'));

        foreach ([
            '[status]',
            '[if applicable]',
            '[Street address]',
            '[Case study: BFSI client]',
            '[Case study: SaaS client]',
            '[to confirm]',
            '[DD MMM 2026]',
        ] as $placeholder) {
            $this->assertStringContainsString($placeholder, $html);
        }
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
