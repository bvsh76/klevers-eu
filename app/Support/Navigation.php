<?php

namespace App\Support;

use App\Models\Page;
use App\Models\Segment;
use App\Models\Service;

/**
 * Навигация сайта для языка: только опубликованные и переведённые записи.
 * При недоступной БД (например, на странице 500) — пустые списки.
 */
final class Navigation
{
    public static function for(string $locale): array
    {
        return rescue(function () use ($locale) {
            $items = fn (string $model) => $model::published()->ordered()->get()
                ->map(fn ($record) => [
                    'key' => $record->key,
                    'title' => $record->getTranslation('title', $locale, false),
                    'url' => $record->localizedUrl($locale),
                ])
                ->filter(fn (array $item) => $item['url'] !== null)
                ->values();

            return [
                'services' => $items(Service::class),
                'segments' => $items(Segment::class),
                'pages' => $items(Page::class)->keyBy('key'),
            ];
        }, self::empty(), report: false);
    }

    private static function empty(): array
    {
        return ['services' => collect(), 'segments' => collect(), 'pages' => collect()];
    }
}
