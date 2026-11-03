<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Support\Industries;
use App\Support\Practices;
use Illuminate\Http\Response;

/**
 * Generated sitemap. Built from the router and the database rather than
 * maintained by hand, so a new service or article cannot be forgotten.
 *
 * The legal pages are included but given a low priority; /contact is higher
 * because it is the conversion page.
 */
class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'monthly'],
            ['loc' => route('contact'), 'priority' => '0.9', 'changefreq' => 'yearly'],
            ['loc' => route('about'), 'priority' => '0.7', 'changefreq' => 'yearly'],
            ['loc' => route('insights.index'), 'priority' => '0.7', 'changefreq' => 'weekly'],
        ];

        foreach (Practices::slugs() as $slug) {
            $urls[] = ['loc' => route('services.show', $slug), 'priority' => '0.9', 'changefreq' => 'monthly'];
        }

        foreach (Industries::slugs() as $slug) {
            $urls[] = ['loc' => route('industries.show', $slug), 'priority' => '0.8', 'changefreq' => 'monthly'];
        }

        foreach (Insight::query()->published()->orderByDesc('published_at')->get() as $insight) {
            $urls[] = [
                'loc' => route('insights.show', $insight->slug),
                'priority' => '0.6',
                'changefreq' => 'yearly',
                'lastmod' => $insight->updated_at?->toAtomString(),
            ];
        }

        foreach (['privacy', 'terms', 'responsible-disclosure'] as $name) {
            $urls[] = ['loc' => route($name), 'priority' => '0.2', 'changefreq' => 'yearly'];
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
