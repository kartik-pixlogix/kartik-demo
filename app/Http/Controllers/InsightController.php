<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use Illuminate\View\View;

class InsightController extends Controller
{
    public function index(): View
    {
        return view('pages.insights-index', [
            'insights' => Insight::query()->published()->orderBy('position')->get(),
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
        ]);
    }
}
