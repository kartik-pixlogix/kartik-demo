<?php

namespace App\Http\Controllers;

use App\Support\Practices;
use App\Support\Seo;
use Illuminate\View\View;

/** The pages that are one of a kind: contact, about and the legal set. */
class PageController extends Controller
{
    public function contact(): View
    {
        return view('pages.contact', [
            'schema' => Seo::graph(
                [
                    '@type' => 'ContactPage',
                    'url' => route('contact'),
                    'about' => ['@id' => url('/').'#organization'],
                ],
                Seo::breadcrumbs([
                    ['UB Shield', route('home')],
                    ['Contact us', null],
                ]),
            ),
        ]);
    }

    public function about(): View
    {
        return view('pages.about', [
            'practices' => Practices::all()->map(fn (array $p, string $s) => $p + ['slug' => $s]),
            'schema' => Seo::graph(
                [
                    '@type' => 'AboutPage',
                    'url' => route('about'),
                    'mainEntity' => ['@id' => url('/').'#organization'],
                ],
                Seo::breadcrumbs([
                    ['UB Shield', route('home')],
                    ['About', null],
                ]),
            ),
        ]);
    }

    public function legal(string $view, string $title, string $updated, string $description): View
    {
        return view("pages.legal.{$view}", [
            'pageTitle' => $title,
            'lastUpdated' => $updated,
            'description' => $description,
            'schema' => Seo::graph(Seo::breadcrumbs([
                ['UB Shield', route('home')],
                [$title, null],
            ])),
        ]);
    }
}
