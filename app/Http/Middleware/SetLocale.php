<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        abort_unless(
            is_string($locale) && array_key_exists($locale, config('locales.supported')),
            404
        );

        App::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // Controllers should not receive {locale} as an argument.
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
