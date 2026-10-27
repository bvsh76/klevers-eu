<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class LocaleUrl
{
    /**
     * URL of the current page in the given locale.
     * Falls back to the home page when the current route is not localized.
     */
    public static function to(string $locale, bool $withQuery = true): string
    {
        $route = Route::current();

        if ($route?->getName() && in_array('locale', $route->parameterNames(), true)) {
            $url = route($route->getName(), array_merge(array_intersect_key($route->parameters(), array_flip($route->parameterNames())), ['locale' => $locale]));
            $query = request()->getQueryString();

            return $withQuery && $query ? $url.'?'.$query : $url;
        }

        return route('home', ['locale' => $locale]);
    }
}
