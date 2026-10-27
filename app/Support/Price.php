<?php

namespace App\Support;

/**
 * Цена в EUR для вывода без зависимости от intl: ru/et — «1 200 €», en — «€1,200».
 */
final class Price
{
    public static function format(string|int|float $amount, string $locale): string
    {
        $value = (float) $amount;
        $decimals = floor($value) == $value ? 0 : 2;

        return $locale === 'en'
            ? '€'.number_format($value, $decimals, '.', ',')
            : number_format($value, $decimals, ',', "\u{00A0}")."\u{00A0}€";
    }
}
