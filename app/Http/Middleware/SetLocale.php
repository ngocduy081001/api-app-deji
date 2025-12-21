<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session, cookie, or use default
        $locale = Session::get('locale', config('app.locale'));

        // Check if locale is in query string
        if ($request->has('locale')) {
            $locale = $request->get('locale');
            Session::put('locale', $locale);
        }

        // Validate locale
        $availableLocales = config('app.available_locales', ['vi', 'en']);
        if (!in_array($locale, $availableLocales)) {
            $locale = config('app.locale', 'vi');
        }

        // Set application locale
        App::setLocale($locale);

        return $next($request);
    }
}
