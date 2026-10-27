<?php

namespace App\Models;

use App\Models\Concerns\HasPublicPage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable(['key', 'slug', 'title', 'body', 'meta_title', 'meta_description', 'is_published', 'sort'])]
class Page extends Model
{
    use HasPublicPage, HasTranslations;

    /** @var array<int, string> */
    public array $translatable = ['slug', 'title', 'body', 'meta_title', 'meta_description'];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort' => 'integer',
        ];
    }
}
