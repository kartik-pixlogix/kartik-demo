<?php

namespace Tests\Feature;

use App\Models\Insight;
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

    public static function industries(): array
    {
        $slugs = array_keys(require __DIR__.'/../../config/industries.php');

        return array_combine($slugs, array_map(fn (string $s) => [$s], $slugs));
    }

    public static function standalonePages(): array
    {
        return [
            'insights index' => ['/insights'],
            'contact' => ['/contact'],
            'about' => ['/about'],
            'privacy' => ['/privacy'],
            'terms' => ['/terms'],
            'responsible disclosure' => ['/responsible-disclosure'],
        ];
    }

    // ---- industries --------------------------------------------------------

    #[DataProvider('industries')]
    public function test_each_industry_page_renders(string $slug): void
    {
        $industry = require __DIR__.'/../../config/industries.php';

        $this->get("/industries/{$slug}")
            ->assertOk()
            ->assertSeeText($industry[$slug]['title']) // text, not HTML: the h1 holds the last word in a no-wrap span (F8)
            ->assertSee($industry[$slug]['lead'], escape: false);
    }

    public function test_an_unknown_industry_is_a_404(): void
    {
        $this->get('/industries/atlantis')->assertNotFound();
    }

    #[DataProvider('industries')]
    public function test_each_industry_links_to_the_practices_it_names(string $slug): void
    {
        $industries = require __DIR__.'/../../config/industries.php';
        $html = $this->get("/industries/{$slug}")->getContent();

        foreach ($industries[$slug]['practices'] as $practice) {
            $this->assertStringContainsString(route('services.show', $practice), $html);
        }
    }

    public function test_the_homepage_industry_rows_link_to_the_industry_pages(): void
    {
        $html = $this->get('/')->getContent();

        foreach (array_keys(require __DIR__.'/../../config/industries.php') as $slug) {
            $this->assertStringContainsString(route('industries.show', $slug), $html);
        }
    }

    // ---- insights ----------------------------------------------------------

    public function test_the_insights_index_lists_published_notes(): void
    {
        $html = $this->get('/insights')->getContent();

        foreach (Insight::all() as $insight) {
            $this->assertStringContainsString(route('insights.show', $insight->slug), $html);
            $this->assertStringContainsString(e($insight->title), $html);
        }
    }

    public function test_an_unpublished_note_is_hidden_from_the_index_and_404s(): void
    {
        $draft = Insight::create([
            'title' => 'Draft note',
            'slug' => 'draft-note',
            'category' => 'Advisory',
            'published_at' => null,
        ]);

        $this->assertStringNotContainsString('Draft note', $this->get('/insights')->getContent());
        $this->get("/insights/{$draft->slug}")->assertNotFound();
    }

    public function test_a_future_dated_note_is_not_published_yet(): void
    {
        $scheduled = Insight::create([
            'title' => 'Scheduled note',
            'slug' => 'scheduled-note',
            'category' => 'Guide',
            'published_at' => now()->addWeek(),
        ]);

        $this->get("/insights/{$scheduled->slug}")->assertNotFound();
    }

    public function test_an_article_renders_its_body(): void
    {
        $insight = Insight::query()->where('slug', 'dpdpa-2023-consent-flows')->sole();

        $this->get("/insights/{$insight->slug}")
            ->assertOk()
            ->assertSee($insight->title, escape: false)
            ->assertSee('What a compliant record looks like', escape: false);
    }

    public function test_an_article_links_back_to_the_index_and_to_siblings(): void
    {
        $html = $this->get('/insights/dpdpa-2023-consent-flows')->getContent();

        $this->assertStringContainsString(route('insights.index'), $html);
        $this->assertStringContainsString(route('insights.show', 'sebi-cscrf-control-by-control'), $html);
    }

    // ---- standalone pages ---------------------------------------------------

    #[DataProvider('standalonePages')]
    public function test_each_standalone_page_renders_with_one_h1(string $url): void
    {
        $html = $this->get($url)->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, '<h1'), "{$url} must have exactly one h1");
    }

    public function test_the_legal_pages_are_marked_for_review(): void
    {
        foreach (['/privacy', '/terms', '/responsible-disclosure'] as $url) {
            $html = $this->get($url)->getContent();

            $this->assertStringContainsString('Legal review required', $html);
            $this->assertStringContainsString('[legal review]', $html);
        }
    }

    public function test_the_legal_pages_cross_link_to_each_other(): void
    {
        $html = $this->get('/privacy')->getContent();

        $this->assertStringContainsString(route('terms'), $html);
        $this->assertStringContainsString(route('responsible-disclosure'), $html);
    }

    public function test_about_shows_credential_statuses_from_settings_without_inventing_any(): void
    {
        $html = $this->get('/about')->getContent();

        // the statuses are placeholders until the client confirms them
        $this->assertStringContainsString('[status]', $html);
        $this->assertStringContainsString('[if applicable]', $html);
        $this->assertStringNotContainsString('ISO/IEC 27001 certified', $html);
    }

    // ---- navigation ---------------------------------------------------------

    public function test_every_internal_link_on_every_page_resolves(): void
    {
        $pages = array_merge(
            ['/'],
            array_map(fn ($s) => "/services/{$s}", array_keys(require __DIR__.'/../../config/practices.php')),
            array_map(fn ($s) => "/industries/{$s}", array_keys(require __DIR__.'/../../config/industries.php')),
            ['/insights', '/insights/dpdpa-2023-consent-flows', '/contact', '/about', '/privacy', '/terms', '/responsible-disclosure'],
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

        $this->assertGreaterThan(15, count($checked), 'expected the crawl to find the inner pages');
    }

    public function test_no_page_still_points_at_a_dead_prototype_anchor(): void
    {
        // #contact is legitimate — the closing strip is on every page.
        // #top is the hero. Anything else would scroll nowhere off the homepage.
        foreach (['/services/offensive-security', '/industries/healthcare', '/about', '/contact', '/privacy'] as $url) {
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
