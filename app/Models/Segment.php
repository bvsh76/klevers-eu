<?php

namespace App\Models;

use App\Models\Concerns\HasPublicPage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

#[Fillable(['key', 'slug', 'title', 'intro', 'body', 'meta_title', 'meta_description', 'is_published', 'sort'])]
class Segment extends Model
{
    use HasPublicPage, HasTranslations;

    /** @var array<int, string> */
    public array $translatable = ['slug', 'title', 'intro', 'body', 'meta_title', 'meta_description'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)->orderBy('services.sort');
    }

    public function publicSection(): ?string
    {
        return 'segments';
    }
}
