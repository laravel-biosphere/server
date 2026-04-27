<?php

namespace Anafro\Biosphere\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureFromBiosphereProxy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (request()->header('X-Biosphere-Token') !== env('BIOSPHERE_TOKEN')) {
            abort(403);
        }

        return $next($request);
    }
}
