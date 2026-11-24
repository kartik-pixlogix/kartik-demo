<?php

namespace Tests\Feature;

use App\Support\Practices;
use Database\Seeders\CaseStudySeeder;
use Database\Seeders\InsightSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingSeeder::class, CaseStudySeeder::class, InsightSeeder::class]);
    }

    /**
     * Data providers run before the application boots, so the config
     * repository is not available yet — the file is read directly.
     *
     * Keyed by slug so a failure names the practice that broke.
     */
    public static function practices(): array
    {
        $slugs = array_keys(require __DIR__.'/../../config/practices.php');

        return array_combine($slugs, array_map(fn (string $slug) => [$slug], $slugs));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('practices')]
    public function test_each_service_page_renders(string $slug): void
    {
        $practice = Practices::find($slug);

        $this->get("/services/{$slug}")
            ->assertOk()
            ->assertSeeText($practice['nav']) // text, not HTML: the h1 holds the last word in a no-wrap span (F8)
            ->assertSeeText($practice['kicker'])
            ->assertSeeText($practice['lead']);
    }

    public function test_an_unknown_service_is_a_404(): void
    {
        $this->get('/services/free-pizza')->assertNotFound();
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('practices')]
    public function test_each_service_page_has_exactly_one_h1(string $slug): void
    {
        $html = $this->get("/services/{$slug}")->getContent();

        $this->assertSame(1, substr_count($html, '<h1'), 'a page must have exactly one h1');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('practices')]
    public function test_each_service_page_carries_service_schema(string $slug): void
    {
        $html = $this->get("/services/{$slug}")->getContent();

        preg_match('/<script type="application\/ld\+json"[^>]*>(.+?)<\/script>/s', $html, $m);
        $data = json_decode(html_entity_decode($m[1] ?? ''), true, flags: JSON_THROW_ON_ERROR);

        $service = collect($data['@graph'])->firstWhere('@type', 'Service');
        $this->assertNotNull($service, 'the page must carry Service structured data');
        $this->assertSame(Practices::find($slug)['nav'], $service['name']);

        // The FAQ was removed with the launch content; its schema must go too,
        // or the page would mark up answers the visitor cannot see.
        $this->assertNull(collect($data['@graph'])->firstWhere('@type', 'FAQPage'));
    }

    public function test_the_six_services_use_the_brief_urls(): void
    {
        // Launch brief §7, in catalogue order.
        $this->assertSame([
            'network-design-architecture',
            'vulnerability-assessment-penetration-testing',
            'network-security-assessment',
            'firewall-security-review',
            'security-configuration-hardening',
            'security-infrastructure-deployment',
        ], Practices::slugs());
    }

    public function test_the_old_service_urls_redirect_permanently(): void
    {
        $this->get('/services/offensive-security')
            ->assertStatus(301)
            ->assertRedirect('/services/vulnerability-assessment-penetration-testing');
        $this->get('/services/infrastructure-cloud')->assertStatus(301)->assertRedirect('/services/network-design-architecture');
        $this->get('/services/managed-security-services')->assertStatus(301)->assertRedirect('/services/network-security-assessment');
        $this->get('/services/governance-risk-compliance')->assertStatus(301)->assertRedirect('/services/security-configuration-hardening');
        $this->get('/services/data-privacy')->assertStatus(301);
    }

    public function test_testing_services_state_the_authorization_requirement(): void
    {
        // Launch brief §10: VAPT only with written authorization and an agreed scope.
        $this->get('/services/vulnerability-assessment-penetration-testing')
            ->assertSeeText('performed only with written authorization and a clearly agreed scope')
            ->assertSeeText('explicitly authorized by the client');
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('practices')]
    public function test_each_service_page_ends_with_the_closing_strip(string $slug): void
    {
        $html = $this->get("/services/{$slug}")->getContent();

        $this->assertStringContainsString('data-review-form', $html);
        $this->assertStringContainsString('id="contact"', $html);
    }

    public function test_the_homepage_service_cards_link_to_the_service_pages(): void
    {
        $html = $this->get('/')->getContent();

        foreach (Practices::slugs() as $slug) {
            $this->assertStringContainsString(route('services.show', $slug), $html);
        }
    }

    public function test_homepage_section_anchors_stay_fragments_on_the_homepage(): void
    {
        $html = $this->get('/')->getContent();

        // in-page scroll, not a reload
        $this->assertStringContainsString('href="#industries"', $html);
        $this->assertStringContainsString('href="#approach"', $html);
    }

    public function test_homepage_section_anchors_become_full_urls_elsewhere(): void
    {
        $html = $this->get('/services/network-security-assessment')->getContent();

        $this->assertStringContainsString(route('home').'#industries', $html);
        $this->assertStringContainsString(route('home').'#approach', $html);

        // #contact must stay local: the closing strip is on this page too
        $this->assertStringContainsString('href="#contact"', $html);
        $this->assertStringNotContainsString(route('home').'#contact', $html);
    }

    public function test_service_pages_do_not_ship_the_homepage_industry_previews(): void
    {
        $home = $this->get('/')->getContent();
        $service = $this->get('/services/network-security-assessment')->getContent();

        $this->assertStringContainsString('class="prev"', $home);
        $this->assertStringNotContainsString('class="prev"', $service);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('practices')]
    public function test_deliverables_are_conditional_on_the_agreed_scope(string $slug): void
    {
        // Change & Preserve Guide §3 / §7: deliverables subject to scope.
        $page = $this->get("/services/{$slug}");

        $page->assertSeeText('Possible deliverables')
            ->assertSeeText('Subject to the agreed scope');
        foreach (Practices::find($slug)['deliverables'] as $item) {
            $page->assertSeeText($item);
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('practices')]
    public function test_service_pages_make_no_unverified_claims(string $slug): void
    {
        // Launch brief §8 / §10: no placeholder figures, certifications,
        // compliance programmes, 24/7 SOC or response-time promises.
        $text = strip_tags($this->get("/services/{$slug}")->getContent());

        foreach (['[XX]', '24/7', 'SOC 2', 'ISO 27001', 'CERT-In', 'DPDPA', 'HIPAA', 'PCI DSS', 'posture review', 'one business day', 'Forty-five minutes'] as $claim) {
            $this->assertStringNotContainsString($claim, $text, "{$slug} must not claim: {$claim}");
        }
    }
}
