<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Support\Practices;
use App\Support\Seo;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ServiceController extends Controller
{
    public function show(string $slug): View
    {
        $practice = Practices::find($slug);

        if ($practice === null) {
            throw new NotFoundHttpException;
        }

        return view('pages.service', [
            'practice' => $practice,
            'caseStudy' => CaseStudy::query()
                ->published()
                ->where('slug', $practice['case'] ?? '')
                ->first(),
            'schema' => Seo::graph(
                Seo::service($practice),
                // Only the questions actually rendered on the page are marked
                // up. Describing an answer the visitor cannot see is what earns
                // a manual action.
                Seo::faq($practice['faq'] ?? []),
                Seo::breadcrumbs([
                    ['UB Shield', route('home')],
                    ['Services', route('home').'#services'],
                    [$practice['nav'], null],
                ]),
            ),
        ]);
    }
}
