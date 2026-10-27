<?php

namespace App\Models;

use App\Enums\PricePeriod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

#[Fillable(['service_id', 'key', 'title', 'description', 'features', 'price', 'price_period', 'is_featured', 'is_active', 'sort'])]
class Package extends Model
{
    use HasTranslations;

    /** @var array<int, string> features — список строк на каждом языке */
    public array $translatable = ['title', 'description', 'features'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_period' => PricePeriod::class,
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'sort' => 'integer',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort')->orderBy('id');
    }

    public function isOnRequest(): bool
    {
        return $this->price === null;
    }
}
