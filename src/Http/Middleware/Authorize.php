<?php

namespace Vlinde\Bugster\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Vlinde\Bugster\LaravelBugster;

class Authorize
{
    public function handle(Request $request, Closure $next): Response
    {
        return app(LaravelBugster::class)->authorize($request)
            ? $next($request)
            : abort(403);
    }
}
