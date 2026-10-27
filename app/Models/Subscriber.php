<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['email', 'locale', 'source', 'consent_at', 'confirmed_at', 'unsubscribed_at'])]
class Subscriber extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber) {
            $subscriber->unsubscribe_token ??= Str::random(48);
        });
    }

    protected function casts(): array
    {
        return [
            'consent_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    protected function email(): Attribute
    {
        return Attribute::set(fn (string $value) => mb_strtolower(trim($value)));
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNull('unsubscribed_at');
    }
}
