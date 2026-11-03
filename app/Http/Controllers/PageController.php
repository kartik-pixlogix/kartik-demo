<?php

namespace App\Http\Controllers;

use App\Support\Industries;
use App\Support\Practices;
use Illuminate\View\View;

/** The pages that are one of a kind: contact, about and the legal set. */
class PageController extends Controller
{
    public function contact(): View
    {
        return view('pages.contact');
    }

    public function about(): View
    {
        return view('pages.about', [
            'practices' => Practices::all()->map(fn (array $p, string $s) => $p + ['slug' => $s]),
            'industries' => Industries::all(),
        ]);
    }

    public function legal(string $view, string $title, string $updated): View
    {
        return view("pages.legal.{$view}", [
            'pageTitle' => $title,
            'lastUpdated' => $updated,
        ]);
    }
}
