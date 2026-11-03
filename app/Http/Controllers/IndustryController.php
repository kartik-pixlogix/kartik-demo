<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Support\Industries;
use App\Support\Practices;
use App\Support\Seo;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndustryController extends Controller
{
    public function show(string $slug): View
    {
        $industry = Industries::find($slug);

        if ($industry === null) {
            throw new NotFoundHttpException;
        }

        return view('pages.industry', [
            'industry' => $industry,
            'practices' => collect($industry['practices'] ?? [])
                ->map(fn (string $s) => Practices::find($s))
                ->filter()
                ->values(),
            'caseStudy' => CaseStudy::query()
                ->published()
                ->where('slug', $industry['case'] ?? '')
                ->first(),
            'schema' => Seo::graph(
                Seo::breadcrumbs([
                    ['UB Shield', route('home')],
                    ['Industries', route('home').'#industries'],
                    [$industry['nav'], null],
                ]),
            ),
        ]);
    }
}
