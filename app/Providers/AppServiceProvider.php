<?php

namespace App\Providers;

use App\Models\CaseStudy;
use App\Models\Insight;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->enforcePasswordPolicy();

        // Canonical URLs, OG tags and the sitemap must all be https in
        // production, even if the TLS terminates at a proxy.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // The homepage sections are anonymous components invoked with no arguments,
        // so their data is bound here rather than threaded through every caller.
        View::composer('components.sections.cases', function ($view) {
            $view->with('caseStudies', CaseStudy::query()->published()->orderBy('position')->get());
        });

        View::composer('components.sections.insights', function ($view) {
            $view->with('insights', Insight::query()->published()->orderBy('position')->get());
        });
    }

    /**
     * Password policy for the admin (BUILD.md Phase 4).
     *
     * Length first, because length beats composition rules — and the
     * compromised-password check matters more than either. NIST SP 800-63B
     * says the same: check against known-breached passwords rather than
     * forcing a symbol nobody remembers.
     *
     * uncompromised() calls the Have I Been Pwned range API, which is
     * k-anonymous — only the first five characters of the SHA-1 hash leave the
     * server. It is skipped outside production so tests and local setup never
     * depend on a network call.
     */
    private function enforcePasswordPolicy(): void
    {
        Password::defaults(function () {
            $rule = Password::min(12)->mixedCase()->numbers();

            return $this->app->isProduction()
                ? $rule->uncompromised()
                : $rule;
        });
    }
}
