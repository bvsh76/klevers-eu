<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const CACHE_KEY = 'settings.all';

    public const KEYS = ['whatsapp', 'telegram', 'lead_email', 'booking_embed', 'ga4_id'];

    protected $fillable = ['key', 'value'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** @return array<string, string|null> */
    public static function allValues(): array
    {
        return rescue(
            fn () => Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all()),
            [],
            false,
        );
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = static::allValues()[$key] ?? null;

        return filled($value) ? $value : $default;
    }

    public static function put(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
