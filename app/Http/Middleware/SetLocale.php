<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->cookie('lang');

        if ($locale && in_array($locale, ['es', 'en'])) {
            \Illuminate\Support\Facades\App::setLocale($locale);
        } else {
            \Illuminate\Support\Facades\App::setLocale(config('app.locale', 'en'));
        }

        return $next($request);
    }
}