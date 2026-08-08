<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locales = config('app.available_locales', ['en']);
        $locale = $request->session()->get('locale')
            ?? $request->cookie('locale')
            ?? substr((string) $request->header('Accept-Language'), 0, 2);

        if (! in_array($locale, $locales, true)) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
