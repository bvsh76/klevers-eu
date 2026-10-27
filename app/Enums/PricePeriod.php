<?php

namespace App\Enums;

enum PricePeriod: string
{
    case Month = 'month';
    case Once = 'once';
    case Hour = 'hour';

    public function label(): string
    {
        return match ($this) {
            self::Month => 'в месяц',
            self::Once => 'разово',
            self::Hour => 'в час',
        };
    }
}
