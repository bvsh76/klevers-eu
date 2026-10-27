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
}
