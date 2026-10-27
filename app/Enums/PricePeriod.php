<?php

namespace App\Enums;

enum PricePeriod: string
{
    case Month = 'month';
    case Once = 'once';
    case Hour = 'hour';
}
