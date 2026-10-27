<?php

namespace Tests\Unit;

use App\Support\Price;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function test_formats_by_locale(): void
    {
        $this->assertSame("1\u{00A0}200\u{00A0}€", Price::format(1200, 'ru'));
        $this->assertSame("49,50\u{00A0}€", Price::format('49.50', 'et'));
        $this->assertSame('€1,200', Price::format('1200.00', 'en'));
    }
}
