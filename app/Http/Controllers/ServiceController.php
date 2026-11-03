<?php

namespace App\Http\Controllers;

use App\Models\CaseStudy;
use App\Support\Practices;
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
            'faqSchema' => $this->faqSchema($practice['faq'] ?? []),
        ]);
    }

    /**
     * schema.org FAQPage for the accordion.
     *
     * Only the questions actually on the page are described — marking up an
     * answer the visitor cannot see is exactly what the structured-data
     * guidelines prohibit, and is the sort of thing that earns a manual action.
     */
    private function faqSchema(array $faq): string
    {
        if ($faq === []) {
            return '{}';
        }

        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $pair) => [
                '@type' => 'Question',
                'name' => $pair[0],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $pair[1]],
            ], $faq),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}
