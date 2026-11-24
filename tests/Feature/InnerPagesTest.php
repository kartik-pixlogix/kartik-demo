<?php

namespace Tests\Feature;

use Database\Seeders\CaseStudySeeder;
use Database\Seeders\InsightSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InnerPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingSeeder::class, CaseStudySeeder::class, InsightSeeder::class]);
    }

    public static function standalonePages(): array
    {
        return [
            'contact' => ['/contact'],
            'about' => ['/about'],
            'privacy' => ['/privacy'],
            'terms' => ['/terms'],
            'responsible disclosure' => ['/responsible-disclosure'],
        ];
    }

    // ---- withdrawn for launch ------------------------------------------------

    public function test_industry_pages_redirect_to_the_homepage_list(): void
    {
        // Launch brief §4: industries are intended sectors with no page content.
        foreach (['banking-finance', 'healthcare', 'saas-it', 'manufacturing', 'education', 'e-commerce'] as $slug) {
            $this->get("/industries/{$slug}")->assertStatus(301)->assertRedirect('/#industries');
        }
    }

    public function test_field_notes_redirect_to_the_homepage(): void
    {
        $this->get('/insights')->assertRedirect('/');
        $this->get('/insights/dpdpa-2023-consent-flows')->assertRedirect('/');
    }

    public function test_the_sitemap_lists_only_live_pages(): void
    {
        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString(route('services.show', 'network-design-architecture'), $xml);
        $this->assertStringNotContainsString('/industries/', $xml);
        $this->assertStringNotContainsString('/insights', $xml);
        $this->assertStringNotContainsString('/services/offensive-security', $xml);
    }

    // ---- standalone pages ---------------------------------------------------

    #[DataProvider('standalonePages')]
    public function test_each_standalone_page_renders_with_one_h1(string $url): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'), "{$url} must have exactly one h1");
    }

    public function test_the_legal_pages_are_complete_with_no_placeholders(): void
    {
        // Change & Preserve Guide §3: complete before publication, remove
        // placeholder text and [legal review] notes.
        foreach (['/privacy', '/terms', '/responsible-disclosure'] as $url) {
            $text = strip_tags($this->get($url)->getContent());

            $this->assertStringNotContainsString('Legal review required', $text);
            $this->assertDoesNotMatchRegularExpression('/\\[[A-Za-z][^\\]]*\\]/', $text, "{$url} still has a [placeholder]");
            $this->assertStringContainsString('24 November 2026', $text);
        }
    }

    public function test_the_privacy_policy_covers_what_the_guide_requires(): void
    {
        $page = $this->get('/privacy');

        // what is collected, why, who can access it, how to contact us
        $page->assertSeeText('What we collect')
            ->assertSeeText('Why we use it')
            ->assertSeeText('Who can access it')
            ->assertSeeText('How to contact us about your data')
            ->assertSeeText('brief requirement');
    }

    public function test_responsible_disclosure_sets_boundaries_without_inviting_testing(): void
    {
        $text = html_entity_decode(strip_tags($this->get('/responsible-disclosure')->getContent()));

        $this->assertStringContainsString('It is not an invitation to test our systems', $text);
        $this->assertStringContainsString('Responsible testing boundaries', $text);
        $this->assertStringContainsString('How to report', $text);
        $this->assertStringContainsString('does not currently offer payment or rewards', $text);
        // no response-time commitment (guide §3)
        $this->assertDoesNotMatchRegularExpression('/within \\d+|business day|hours of/i', $text);
    }

    public function test_the_legal_pages_cross_link_to_each_other(): void
    {
        $html = $this->get('/privacy')->getContent();

        $this->assertStringContainsString(route('terms'), $html);
        $this->assertStringContainsString(route('responsible-disclosure'), $html);
    }

    public function test_about_carries_the_launch_positioning_without_credentials(): void
    {
        $page = $this->get('/about');

        // Launch brief §1: newly launching, small core team, partner support,
        // Ahmedabad and Rajkot.
        $page->assertSeeText('small core team')
            ->assertSeeText('technical partners')
            ->assertSeeText('Ahmedabad and Rajkot, Gujarat, India');

        // §8 / §10: no credential statuses until they can be substantiated.
        $text = strip_tags($page->getContent());
        foreach (['ISO/IEC 27001', 'SOC 2', 'CERT-In', '[status]', '24/7'] as $claim) {
            $this->assertStringNotContainsString($claim, $text);
        }
    }

    public function test_contact_makes_no_response_time_or_free_review_promise(): void
    {
        $text = html_entity_decode(strip_tags($this->get('/contact')->getContent()));

        $this->assertStringContainsString('Let’s discuss your network & security requirements', $text);
        foreach (['one business day', 'Forty-five minutes', 'posture review', '[email address]', '[phone number]'] as $claim) {
            $this->assertStringNotContainsString($claim, $text);
        }
    }

    // ---- navigation ---------------------------------------------------------

    public function test_every_internal_link_on_every_page_resolves(): void
    {
        $pages = array_merge(
            ['/'],
            array_map(fn ($s) => "/services/{$s}", array_keys(require __DIR__.'/../../config/practices.php')),
            ['/contact', '/about', '/privacy', '/terms', '/responsible-disclosure'],
        );

        $checked = [];

        foreach ($pages as $page) {
            $html = $this->get($page)->assertOk()->getContent();

            // route() emits absolute URLs, so match both those and bare paths
            preg_match_all('/href="([^"]+)"/', $html, $matches);

            $hrefs = collect($matches[1])
                ->map(fn (string $h) => str_starts_with($h, config('app.url'))
                    ? (string) str(substr($h, strlen(config('app.url'))))->before('#')->before('?')
                    : $h)
                ->filter(fn (string $h) => str_starts_with($h, '/'))
                ->map(fn (string $h) => (string) str($h)->before('#')->before('?'))
                ->unique();

            foreach ($hrefs as $href) {
                // assets are served by the web server, not the router
                if (preg_match('#^/(build|fonts|images|storage)/#', $href)) {
                    continue;
                }
                if (isset($checked[$href])) {
                    continue;
                }
                $checked[$href] = true;

                $status = $this->get($href)->getStatusCode();
                $this->assertSame(200, $status, "{$href}, linked from {$page}, returned {$status}");
            }
        }

        $this->assertGreaterThan(10, count($checked), 'expected the crawl to find the inner pages');
    }

    public function test_no_page_still_points_at_a_dead_prototype_anchor(): void
    {
        // #contact is legitimate — the closing strip is on every page.
        // #top is the hero. Anything else would scroll nowhere off the homepage.
        foreach (['/services/vulnerability-assessment-penetration-testing', '/about', '/contact', '/privacy'] as $url) {
            $html = $this->get($url)->getContent();

            preg_match_all('/href="#([a-z-]+)"/', $html, $matches);

            foreach (array_unique($matches[1]) as $fragment) {
                $this->assertStringContainsString(
                    'id="'.$fragment.'"',
                    $html,
                    "{$url} links to #{$fragment} but has no element with that id"
                );
            }
        }
    }
}
