<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Structured data for the site.
 *
 * Every page emits Organization and WebSite; pages add Service, Article,
 * FAQPage or BreadcrumbList on top. One <script type="application/ld+json">
 * per page carrying an @graph, rather than several scripts, because each one
 * needs a CSP nonce and a single graph lets the entities reference each other.
 *
 * NOTHING UNCONFIRMED IS ASSERTED HERE. Organization carries no telephone,
 * no street address and no certification, because those are still placeholders
 * (docs/placeholders.md). Publishing "[email address]" as structured data, or
 * claiming a certification the client has not confirmed, would be worse than
 * omitting the property.
 */
class Seo
{
    public static function organization(): array
    {
        $graph = [
            '@type' => 'Organization',
            '@id' => url('/').'#organization',
            'name' => 'UB Shield',
            'url' => url('/'),
            /*
             * The icon rather than the bare mark: search results and knowledge
             * panels put this on white, and the mark's left facet is silver —
             * it disappears on a light background. icon-512 is the same mark
             * on its Deep Navy plate, so it reads anywhere, and 512px clears
             * Google's 112px minimum with room to spare.
             */
            'logo' => [
                '@type' => 'ImageObject',
                'url' => url('/images/brand/icon-512.png'),
                'width' => 512,
                'height' => 512,
            ],
            'description' => config('site.description'),
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => 'Ahmedabad',
                'addressRegion' => 'Gujarat',
                'addressCountry' => 'IN',
            ],
            // Launch brief §1: Ahmedabad and Rajkot are the initial service locations.
            'areaServed' => [
                ['@type' => 'City', 'name' => 'Ahmedabad'],
                ['@type' => 'City', 'name' => 'Rajkot'],
                ['@type' => 'State', 'name' => 'Gujarat'],
            ],
        ];

        // Only publish contact details that are real.
        $email = setting('contact_email');
        $phone = setting('contact_phone');
        $street = setting('contact_address');

        $contact = array_filter([
            '@type' => 'ContactPoint',
            'contactType' => 'sales',
            'email' => is_placeholder($email) ? null : $email,
            'telephone' => is_placeholder($phone) ? null : $phone,
            'areaServed' => 'IN',
        ]);

        if (isset($contact['email']) || isset($contact['telephone'])) {
            $graph['contactPoint'] = $contact;
        }

        if (! is_placeholder($street)) {
            $graph['address']['streetAddress'] = $street;
        }

        return $graph;
    }

    public static function website(): array
    {
        return [
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'url' => url('/'),
            'name' => 'UB Shield',
            'publisher' => ['@id' => url('/').'#organization'],
            'inLanguage' => 'en-IN',
        ];
    }

    /** @param list<array{0:string,1:?string}> $crumbs [label, url] */
    public static function breadcrumbs(array $crumbs): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_map(fn (int $i, array $c) => array_filter([
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $c[0],
                'item' => $c[1] ?? null,
            ]), array_keys($crumbs), $crumbs)),
        ];
    }

    public static function service(array $practice): array
    {
        return [
            '@type' => 'Service',
            'name' => $practice['nav'],
            'serviceType' => $practice['kicker'],
            'description' => $practice['lead'],
            'provider' => ['@id' => url('/').'#organization'],
            'areaServed' => ['@type' => 'State', 'name' => 'Gujarat'],
            'url' => route('services.show', $practice['slug']),
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => $practice['nav'],
                'itemListElement' => array_map(fn (string $item) => [
                    '@type' => 'Offer',
                    'itemOffered' => ['@type' => 'Service', 'name' => $item],
                ], $practice['included'] ?? []),
            ],
        ];
    }

    /** @param list<array{0:string,1:string}> $faq */
    public static function faq(array $faq): array
    {
        return [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $pair) => [
                '@type' => 'Question',
                'name' => $pair[0],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $pair[1]],
            ], $faq),
        ];
    }

    public static function article(\App\Models\Insight $insight): array
    {
        return array_filter([
            '@type' => 'Article',
            'headline' => Str::limit($insight->title, 110, ''),
            'description' => $insight->excerpt,
            'url' => route('insights.show', $insight->slug),
            // date_label is the design's "[DD MMM 2026]" placeholder, so the
            // machine-readable date comes from published_at instead.
            'datePublished' => $insight->published_at?->toIso8601String(),
            'dateModified' => $insight->updated_at?->toIso8601String(),
            'author' => ['@id' => url('/').'#organization'],
            'publisher' => ['@id' => url('/').'#organization'],
            'articleSection' => $insight->category,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('insights.show', $insight->slug)],
        ]);
    }

    /** Wraps page-specific entities with the two every page carries. */
    public static function graph(array ...$entities): string
    {
        $graph = array_merge([self::organization(), self::website()], array_filter($entities));

        return json_encode(
            ['@context' => 'https://schema.org', '@graph' => $graph],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
    }
}
