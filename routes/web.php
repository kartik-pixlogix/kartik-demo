<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/industries/{slug}', [IndustryController::class, 'show'])->name('industries.show');

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [InsightController::class, 'show'])->name('insights.show');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::post('/posture-review', [ReviewController::class, 'store'])->name('review.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

/*
 * Legal. The text is placeholder, marked [legal review] throughout, and must be
 * replaced by the client's counsel before launch — see docs/placeholders.md.
 *
 * Responsible disclosure is not in BUILD.md's page list, but the approved
 * footer links to it and a security company is expected to publish one. Flagged
 * for the client; drop the route and the footer link together if not wanted.
 */
Route::get('/privacy', fn () => app(PageController::class)->legal(
    'privacy',
    'Privacy policy',
    '[date]',
    'How UB Shield collects, uses and protects personal data submitted through this website, and the rights you have under the Digital Personal Data Protection Act 2023.',
))->name('privacy');

Route::get('/terms', fn () => app(PageController::class)->legal(
    'terms',
    'Terms of use',
    '[date]',
    'The terms governing your use of the UB Shield website, including acceptable use, intellectual property and the limits of the information published here.',
))->name('terms');

Route::get('/responsible-disclosure', fn () => app(PageController::class)->legal(
    'responsible-disclosure',
    'Responsible disclosure',
    '[date]',
    'How to report a security vulnerability in a UB Shield system: what is in scope, how to reach us, and what you can expect from us in return.',
))->name('responsible-disclosure');
