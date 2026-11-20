<?php

namespace App\Http\Controllers;

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
            'others' => Practices::except($slug)->values(),
            'schema' => Seo::graph(
                Seo::service($practice),
                Seo::breadcrumbs([
                    ['UB Shield', route('home')],
                    ['Services', route('home').'#services'],
                    [$practice['nav'], null],
                ]),
            ),
        ]);
    }
}
