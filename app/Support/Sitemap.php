<?php

namespace App\Support;

use App\Models\Page;
use App\Models\Segment;
use App\Models\Service;

class Sitemap
{
    /** @return array<int, array{loc: string, alternates: array<string, string>}> */
    public static function urls(): array
    {
        $locales = array_keys(config('locales.supported'));
        $default = config('locales.default');
        $sets = [];

        // Главная — всегда на всех языках.
        $sets[] = collect($locales)->mapWithKeys(fn ($l) => [$l => route('home', ['locale' => $l])])->all();

        $items = Page::published()->where(fn ($q) => $q->whereNull('key')->orWhere('key', '!=', 'home'))->ordered()->get()
            ->concat(Segment::published()->ordered()->get())
            ->concat(Service::published()->ordered()->get());

        foreach ($items as $item) {
            $set = collect($locales)->mapWithKeys(fn ($l) => [$l => $item->localizedUrl($l)])->filter()->all();
            if ($set !== []) {
                $sets[] = $set;
            }
        }

        $urls = [];
        foreach ($sets as $set) {
            $alternates = $set + (isset($set[$default]) ? ['x-default' => $set[$default]] : []);
            foreach ($set as $loc) {
                $urls[] = ['loc' => $loc, 'alternates' => $alternates];
            }
        }

        return $urls;
    }
}
