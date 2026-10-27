<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class LocaleUrl
{
    /** Атрибут запроса, в который контроллер кладёт показываемую запись (HasPublicPage). */
    public const ATTRIBUTE = 'localizable';

    /**
     * URL of the current page in the given locale.
     * Falls back to the home page when the current route is not localized
     * or the current record has no translation in that locale.
     */
    public static function to(string $locale, bool $withQuery = true): string
    {
        $model = request()->attributes->get(self::ATTRIBUTE);

        if ($model !== null) {
            $url = $model->localizedUrl($locale) ?? route('home', ['locale' => $locale]);
        } else {
            $route = Route::current();

            if (! ($route?->getName() && in_array('locale', $route->parameterNames(), true))) {
                return route('home', ['locale' => $locale]);
            }

            $url = route($route->getName(), array_merge(array_intersect_key($route->parameters(), array_flip($route->parameterNames())), ['locale' => $locale]));
        }

        $query = request()->getQueryString();

        return $withQuery && $query ? $url.'?'.$query : $url;
    }

    /**
     * hreflang => URL (без query) только для языков, где страница существует, + x-default.
     *
     * @return array<string, string>
     */
    public static function alternates(): array
    {
        $model = request()->attributes->get(self::ATTRIBUTE);
        $result = [];

        foreach (array_keys(config('locales.supported')) as $locale) {
            if ($model === null || $model->localizedUrl($locale) !== null) {
                $result[$locale] = self::to($locale, false);
            }
        }

        $default = config('locales.default');
        if (isset($result[$default])) {
            $result['x-default'] = $result[$default];
        }

        return $result;
    }
}
