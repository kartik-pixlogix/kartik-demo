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
            ->assertSee($practice['title'], escape: false)
            ->assertSee($practice['lead'], escape: false);
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
    public function test_each_service_page_carries_valid_faqpage_schema(string $slug): void
    {
        $html = $this->get("/services/{$slug}")->getContent();

        // The script now carries a CSP nonce, so the tag is no longer bare.
        preg_match('/<script type="application\/ld\+json"[^>]*>(.+?)<\/script>/s', $html, $m);
        $this->assertNotEmpty($m[1] ?? '', 'no JSON-LD on the page');

        $data = json_decode(html_entity_decode($m[1]), true, flags: JSON_THROW_ON_ERROR);

        // Phase 4 merged the per-page entities into one @graph alongside
        // Organization and WebSite, so FAQPage is now a member of it.
        $this->assertSame('https://schema.org', $data['@context']);

        $schema = collect($data['@graph'])->firstWhere('@type', 'FAQPage');
        $this->assertNotNull($schema, 'the page must carry FAQPage structured data');
        $this->assertNotEmpty($schema['mainEntity']);

        // Google's structured-data rules: every marked-up answer must be
        // visible on the page. Marking up content the visitor cannot see is
        // what earns a manual action.
        foreach ($schema['mainEntity'] as $entity) {
            $this->assertSame('Question', $entity['@type']);
            $this->assertSame('Answer', $entity['acceptedAnswer']['@type']);
            $this->assertStringContainsString(
                e($entity['acceptedAnswer']['text']),
                $html,
                'every answer in the schema must also be rendered on the page'
            );
        }
    }

    public function test_the_faq_works_without_javascript(): void
    {
        $html = $this->get('/services/offensive-security')->getContent();

        // <details>/<summary> opens with no script at all
        $this->assertStringContainsString('<details', $html);
        $this->assertStringContainsString('<summary>', $html);
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
        $this->assertStringContainsString('href="#compliance"', $html);
    }

    public function test_homepage_section_anchors_become_full_urls_elsewhere(): void
    {
        $html = $this->get('/services/offensive-security')->getContent();

        $this->assertStringContainsString(route('home').'#industries', $html);
        $this->assertStringContainsString(route('home').'#compliance', $html);

        // #contact must stay local: the closing strip is on this page too
        $this->assertStringContainsString('href="#contact"', $html);
        $this->assertStringNotContainsString(route('home').'#contact', $html);
    }

    public function test_service_pages_do_not_ship_the_homepage_industry_previews(): void
    {
        $home = $this->get('/')->getContent();
        $service = $this->get('/services/offensive-security')->getContent();

        $this->assertStringContainsString('class="prev"', $home);
        $this->assertStringNotContainsString('class="prev"', $service);
    }

    public function test_the_copy_keeps_unconfirmed_claims_in_brackets(): void
    {
        // Durations, SLAs and the CERT-In window are claims about UB Shield that
        // the client has not confirmed. They must stay bracketed, like the
        // prototype's own [XX] figures.
        $vapt = $this->get('/services/offensive-security')->getContent();
        $this->assertStringContainsString('[Typical durations to confirm.]', $vapt);

        $mss = $this->get('/services/managed-security-services')->getContent();
        $this->assertStringContainsString('[Six-hour window to confirm.]', $mss);
        $this->assertStringContainsString('[Target times to confirm with the client.]', $mss);
    }
}
