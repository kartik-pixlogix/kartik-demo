<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Prepended so the CSP nonce exists before any view renders, and so
        // the headers are attached to error responses too.
        $middleware->prepend(\App\Http\Middleware\SecurityHeaders::class);

        // Lets the visual regression harness pin the page's content to the
        // design's placeholders. Ignored outside local and testing — see the
        // class docblock for why the diff needs it.
        $middleware->prepend(\App\Http\Middleware\DesignPreviewMode::class);

        // Behind a load balancer or Cloudflare, Laravel must read the real
        // scheme from X-Forwarded-Proto or it will never send HSTS and will
        // generate http:// canonicals.
        $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_FOR
            | Request::HEADER_X_FORWARDED_HOST
            | Request::HEADER_X_FORWARDED_PORT
            | Request::HEADER_X_FORWARDED_PROTO
            | Request::HEADER_X_FORWARDED_AWS_ELB);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })
    /*
     * Vercel's filesystem is read-only apart from /tmp, so Laravel cannot
     * write compiled views, its cache or its logs to storage/. This points
     * them somewhere writable.
     *
     * /tmp does not survive between invocations, which is why sessions and
     * the cache are on the database driver and the log channel is stderr.
     * Nothing durable may be written here.
     *
     * Gated on Vercel's own environment variable, so it is a no-op locally,
     * in CI and on any conventional host. See docs/deploy-vercel.md.
     */
    ->booting(function (Application $app): void {
        if (! getenv('VERCEL')) {
            return;
        }

        $app->useStoragePath('/tmp/storage');

        foreach (['framework/views', 'framework/cache/data', 'framework/sessions', 'logs'] as $dir) {
            if (! is_dir($path = '/tmp/storage/'.$dir)) {
                @mkdir($path, 0755, true);
            }
        }
    })
    ->create();
