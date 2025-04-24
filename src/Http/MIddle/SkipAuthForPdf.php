<?php

namespace wildcats1369\Filametrics\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class SkipAuthForPdf
{
    public function handle(Request $request, Closure $next): Response
    {
        // Match a PDF route like: filament/filametrics-sites/123/pdf
        if (Str::is('filament/filametrics-sites/*/pdf', $request->path())) {
            return $next($request);
        }

        // Run default auth middleware if not skipping
        return app(Middleware::class)->handle($request, $next);
    }
}
