<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Support\Seo;
use Illuminate\View\View;

class InsightController extends Controller
{
    public function index(): View
    {
        return view('pages.insights-index', [
            'insights' => Insight::query()->published()->orderBy('position')->get(),
            'schema' => Seo::graph(Seo::breadcrumbs([
                ['UB Shield', route('home')],
                ['Field notes', null],
            ])),
        ]);
    }

    public function show(string $slug): View
    {
        $insight = Insight::query()->published()->where('slug', $slug)->firstOrFail();

        return view('pages.insight', [
            'insight' => $insight,
            'more' => Insight::query()
                ->published()
                ->whereKeyNot($insight->getKey())
                ->orderBy('position')
                ->take(3)
                ->get(),
            'schema' => Seo::graph(
                Seo::article($insight),
                Seo::breadcrumbs([
                    ['UB Shield', route('home')],
                    ['Field notes', route('insights.index')],
                    [$insight->title, null],
                ]),
            ),
        ]);
    }
}
