<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

/*
 * Launch brief (23 November): the five original practices became six services
 * with new URLs (§7). The old URLs were live briefly, so each one redirects
 * permanently to its closest new service rather than 404ing.
 */
$legacyServices = [
    'offensive-security' => 'vulnerability-assessment-penetration-testing',
    'infrastructure-cloud' => 'network-design-architecture',
    'managed-security-services' => 'network-security-assessment',
    'governance-risk-compliance' => 'security-configuration-hardening',
    'data-privacy' => null, // no equivalent service: back to the list
];

foreach ($legacyServices as $old => $new) {
    Route::permanentRedirect("/services/{$old}", $new ? "/services/{$new}" : '/#services');
}

Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

/*
 * Industry detail pages and field notes are withdrawn for launch: the brief
 * lists industries as intended sectors with no page content (§4), and the
 * field notes carry placeholder dates and compliance claims (§8). Their URLs
 * redirect rather than 404. The controllers and the admin stay, so either can
 * come back by restoring these routes.
 */
Route::permanentRedirect('/industries/{slug}', '/#industries')->name('industries.show');
Route::redirect('/insights', '/')->name('insights.index');
Route::redirect('/insights/{slug}', '/')->name('insights.show');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::post('/posture-review', [ReviewController::class, 'store'])->name('review.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
 * Legal. Completed 24 November 2026 per the Change & Preserve Guide §3: no
 * placeholders. Still to be reviewed by a qualified professional before it is
 * treated as final legal advice.
 *
 * Responsible disclosure is not in BUILD.md's page list, but the approved
 * footer links to it and a security company is expected to publish one. Flagged
 * for the client; drop the route and the footer link together if not wanted.
 */
Route::get('/privacy', fn () => app(PageController::class)->legal(
    'privacy',
    'Privacy policy',
    '24 November 2026',
    'How UB Shield collects, uses and protects the information you send through the enquiry form, who can access it, and your rights under the Digital Personal Data Protection Act 2023.',
))->name('privacy');

Route::get('/terms', fn () => app(PageController::class)->legal(
    'terms',
    'Terms of use',
    '24 November 2026',
    'The terms governing your use of the UB Shield website, including acceptable use, intellectual property and the limits of the information published here.',
))->name('terms');

Route::get('/responsible-disclosure', fn () => app(PageController::class)->legal(
    'responsible-disclosure',
    'Responsible disclosure',
    '24 November 2026',
    'How to report a security vulnerability in the UB Shield website: scope, the reporting channel, responsible testing boundaries and what to expect from us.',
))->name('responsible-disclosure');
