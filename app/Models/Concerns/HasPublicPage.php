<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Публичная сущность с переводимым слагом и флагом публикации.
 */
trait HasPublicPage
{
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeWhereSlug(Builder $query, string $slug, string $locale): void
    {
        $query->where("slug->{$locale}", $slug);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort')->orderBy('id');
    }

    /** Ключ раздела в config('locales.sections'); null — страница без раздела. */
    public function publicSection(): ?string
    {
        return null;
    }

    /** URL записи на языке или null, если она там не опубликована/не переведена. */
    public function localizedUrl(string $locale): ?string
    {
        if (! $this->is_published) {
            return null;
        }

        $section = $this->publicSection();

        if ($section === null && $this->key === 'home') {
            return route('home', ['locale' => $locale]);
        }

        $slug = $this->getTranslation('slug', $locale, false);

        if (blank($slug) || blank($this->getTranslation('title', $locale, false))) {
            return null;
        }

        return $section === null
            ? route('page', ['locale' => $locale, 'slug' => $slug])
            : route('section', ['locale' => $locale, 'section' => config("locales.sections.{$section}.{$locale}"), 'slug' => $slug]);
    }
}
