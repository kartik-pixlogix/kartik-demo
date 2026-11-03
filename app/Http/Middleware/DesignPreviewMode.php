<?php

namespace App\Http\Middleware;

use App\Support\DesignPreview;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Turns on App\Support\DesignPreview for `?design-preview=1`.
 *
 * Local and testing only. In any other environment the parameter is ignored
 * entirely — it is not a hidden feature flag, it is how the visual regression
 * harness pins the page's content so the diff measures CSS and markup rather
 * than whatever the client last typed into the admin.
 */
class DesignPreviewMode
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->boolean('design-preview') && app()->environment(['local', 'testing'])) {
            DesignPreview::activate();
        }

        return $next($request);
    }
}
