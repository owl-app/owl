<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Converter;

use Owl\Component\Invoice\Converter\CurrencyConverter;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    public function testDefaultRound(): void
    {
        $this->assertEquals(12.35, CurrencyConverter::defaultRound(12.345));
        $this->assertEquals(12.35, CurrencyConverter::defaultRound(12.346));
        $this->assertEquals(12.35, CurrencyConverter::defaultRound(12.349));
        $this->assertEquals(12.35, CurrencyConverter::defaultRound(12.3451));
        $this->assertEquals(12.00, CurrencyConverter::defaultRound(12));
        $this->assertEquals(0.00, CurrencyConverter::defaultRound(0));
        $this->assertEquals(-12.35, CurrencyConverter::defaultRound(-12.345));
    }

    public function testToMinor(): void
    {
        $this->assertEquals(1235, CurrencyConverter::toMinor(12.35));
        $this->assertEquals(1200, CurrencyConverter::toMinor(12));
        $this->assertEquals(1205, CurrencyConverter::toMinor(12.05));
        $this->assertEquals(0, CurrencyConverter::toMinor(0));
        $this->assertEquals(-1235, CurrencyConverter::toMinor(-12.35));
        $this->assertEquals(1235, CurrencyConverter::toMinor(12.345));
        $this->assertEquals(1235, CurrencyConverter::toMinor(12.349));
        $this->assertEquals(1235, CurrencyConverter::toMinor(12.351));
    }

    public function testToMajor(): void
    {
        $this->assertEquals(12.35, CurrencyConverter::toMajor(1235));
        $this->assertEquals(12.00, CurrencyConverter::toMajor(1200));
        $this->assertEquals(0.01, CurrencyConverter::toMajor(1));
        $this->assertEquals(0.00, CurrencyConverter::toMajor(0));
        $this->assertEquals(-12.35, CurrencyConverter::toMajor(-1235));
    }

    public function testFormatMajor(): void
    {
        $this->assertEquals('12.35', CurrencyConverter::formatMajor(1235));
        $this->assertEquals('0.01', CurrencyConverter::formatMajor(1));
        $this->assertEquals('0.00', CurrencyConverter::formatMajor(0));
        $this->assertEquals('-12.35', CurrencyConverter::formatMajor(-1235));

        $this->assertEquals('12,35', CurrencyConverter::formatMajor(1235, ','));

        $this->assertEquals('1,234.56', CurrencyConverter::formatMajor(123456, '.', ','));

        $this->assertEquals('1 234,56', CurrencyConverter::formatMajor(123456, ',', ' '));
    }
}
