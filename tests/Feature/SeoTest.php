<?php

namespace Tests\Feature;

use App\Models\Setting;
use Database\Seeders\CaseStudySeeder;
use Database\Seeders\InsightSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SettingSeeder::class, CaseStudySeeder::class, InsightSeeder::class]);
    }

    public static function pages(): array
    {
        return [
            'home' => ['/'],
            'service' => ['/services/vulnerability-assessment-penetration-testing'],
            'contact' => ['/contact'],
            'about' => ['/about'],
            'privacy' => ['/privacy'],
        ];
    }

    #[DataProvider('pages')]
    public function test_each_page_has_the_full_meta_set(string $url): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<title>.{10,}<\/title>/', $html);
        $this->assertMatchesRegularExpression('/<meta name="description" content="[^"]{40,}"/', $html);
        $this->assertStringContainsString('<link rel="canonical"', $html);

        foreach (['og:title', 'og:description', 'og:url', 'og:image', 'og:type', 'og:site_name'] as $property) {
            $this->assertStringContainsString('property="'.$property.'"', $html, "missing {$property} on {$url}");
        }

        foreach (['twitter:card', 'twitter:title', 'twitter:image'] as $name) {
            $this->assertStringContainsString('name="'.$name.'"', $html, "missing {$name} on {$url}");
        }
    }

    #[DataProvider('pages')]
    public function test_each_page_has_exactly_one_h1_and_a_canonical_to_itself(string $url): void
    {
        $html = $this->get($url)->getContent();

        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertStringContainsString('<link rel="canonical" href="'.url($url === '/' ? '/' : $url).'"', $html);
    }

    #[DataProvider('pages')]
    public function test_each_page_emits_valid_json_ld_with_organization_and_website(string $url): void
    {
        $html = $this->get($url)->getContent();

        preg_match('/<script type="application\/ld\+json"[^>]*>(.+?)<\/script>/s', $html, $m);
        $this->assertNotEmpty($m[1] ?? '', "no JSON-LD on {$url}");

        $data = json_decode(html_entity_decode($m[1]), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('https://schema.org', $data['@context']);
        $types = array_column($data['@graph'], '@type');
        $this->assertContains('Organization', $types);
        $this->assertContains('WebSite', $types);
    }

    public function test_the_service_page_describes_the_service(): void
    {
        $data = $this->graph('/services/vulnerability-assessment-penetration-testing');
        $types = array_column($data['@graph'], '@type');

        $this->assertContains('Service', $types);
        $this->assertContains('BreadcrumbList', $types);
        // the FAQ went with the launch content, so its schema must not stay
        $this->assertNotContains('FAQPage', $types);
    }

    public function test_the_organization_describes_the_launch_positioning(): void
    {
        $org = collect($this->graph('/')['@graph'])->firstWhere('@type', 'Organization');

        $this->assertSame(config('site.description'), $org['description']);
        $this->assertStringNotContainsString('SOC', json_encode($org));
        $this->assertContains('Rajkot', array_column($org['areaServed'], 'name'));
    }

    public function test_structured_data_never_publishes_a_placeholder_as_fact(): void
    {
        $data = $this->graph('/');
        $org = collect($data['@graph'])->firstWhere('@type', 'Organization');

        // contact details are still "[email address]" / "[phone number]"
        $this->assertArrayNotHasKey('contactPoint', $org, 'placeholders must not be published as contact data');
        $this->assertArrayNotHasKey('streetAddress', $org['address']);
        // a bracketed placeholder such as "[email address]" (JSON arrays are fine)
        $this->assertDoesNotMatchRegularExpression('/\[[A-Za-z]/', json_encode($org));
    }

    public function test_structured_data_publishes_contact_details_once_they_are_real(): void
    {
        Setting::query()->where('key', 'contact_email')->update(['value' => 'hello@ubshield.in']);
        Setting::query()->where('key', 'contact_phone')->update(['value' => '+91 79 1234 5678']);
        \Illuminate\Support\Facades\Cache::forget(Setting::CACHE_KEY);

        $org = collect($this->graph('/')['@graph'])->firstWhere('@type', 'Organization');

        $this->assertSame('hello@ubshield.in', $org['contactPoint']['email']);
        $this->assertSame('+91 79 1234 5678', $org['contactPoint']['telephone']);
    }

    public function test_the_sitemap_lists_every_public_page(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        $xml = $response->getContent();
        $this->assertStringContainsString('<urlset', $xml);

        foreach (array_merge(
            [route('home'), route('contact'), route('about'), route('privacy')],
            array_map(fn ($s) => route('services.show', $s), array_keys(require __DIR__.'/../../config/practices.php')),
        ) as $url) {
            $this->assertStringContainsString("<loc>{$url}</loc>", $xml, "{$url} missing from the sitemap");
        }

        // valid XML, not just a string that looks like it
        $this->assertNotFalse(simplexml_load_string($xml));
    }

    public function test_robots_txt_keeps_crawlers_out_of_the_admin(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /admin', $robots);
        $this->assertStringContainsString('Sitemap:', $robots);
    }

    private function graph(string $url): array
    {
        preg_match('/<script type="application\/ld\+json"[^>]*>(.+?)<\/script>/s', $this->get($url)->getContent(), $m);

        return json_decode(html_entity_decode($m[1]), true, flags: JSON_THROW_ON_ERROR);
    }
}
