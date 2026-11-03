<?php

namespace App\Providers;

use App\Models\CaseStudy;
use App\Models\Insight;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // The homepage sections are anonymous components invoked with no arguments,
        // so their data is bound here rather than threaded through every caller.
        View::composer('components.sections.cases', function ($view) {
            $view->with('caseStudies', CaseStudy::query()->published()->orderBy('position')->get());
        });

        View::composer('components.sections.insights', function ($view) {
            $view->with('insights', Insight::query()->published()->orderBy('position')->get());
        });
    }
}
