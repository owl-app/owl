<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Calculator;

use Owl\Component\Invoice\Calculator\LineDataCalculator;
use PHPUnit\Framework\TestCase;

class LineDataCalculatorTest extends TestCase
{
    public function testNormalizeTaxRate(): void
    {
        $this->assertEquals(0.2, LineDataCalculator::normalizeTaxRate(20));
        $this->assertEquals(0.23, LineDataCalculator::normalizeTaxRate(23));
        $this->assertEquals(0.05, LineDataCalculator::normalizeTaxRate(0.05));
        $this->assertEquals(0.5, LineDataCalculator::normalizeTaxRate(0.5));
    }

    public function testCalculateTotalPriceFromMajor(): void
    {
        $this->assertEquals(2500, LineDataCalculator::calculateTotalPriceFromMajor(25.0, 1.0, true));
        $this->assertEquals(5000, LineDataCalculator::calculateTotalPriceFromMajor(25.0, 2.0, true));
        $this->assertEquals(2250, LineDataCalculator::calculateTotalPriceFromMajor(15.0, 1.5, true));

        $this->assertEquals(25.0, LineDataCalculator::calculateTotalPriceFromMajor(25.0, 1.0, false));
        $this->assertEquals(50.0, LineDataCalculator::calculateTotalPriceFromMajor(25.0, 2.0, false));
        $this->assertEquals(22.5, LineDataCalculator::calculateTotalPriceFromMajor(15.0, 1.5, false));
    }

    public function testCalculateTotalPriceFromMinor(): void
    {
        $this->assertEquals(2500, LineDataCalculator::calculateTotalPriceFromMinor(2500, 1.0, true));
        $this->assertEquals(5000, LineDataCalculator::calculateTotalPriceFromMinor(2500, 2.0, true));
        $this->assertEquals(3750, LineDataCalculator::calculateTotalPriceFromMinor(2500, 1.5, true));

        $this->assertEquals(25.0, LineDataCalculator::calculateTotalPriceFromMinor(2500, 1.0, false));
        $this->assertEquals(50.0, LineDataCalculator::calculateTotalPriceFromMinor(2500, 2.0, false));
        $this->assertEquals(37.5, LineDataCalculator::calculateTotalPriceFromMinor(2500, 1.5, false));
    }

    public function testCalculateTotalPriceWithEmptyValues(): void
    {
        $this->assertEquals(0, LineDataCalculator::calculateTotalPriceFromMinor(0, 1.0));
        $this->assertEquals(0, LineDataCalculator::calculateTotalPriceFromMinor(100, 0));
    }

    public function testCalculateTaxFromMajor(): void
    {
        $this->assertEquals(500, LineDataCalculator::calculateTaxFromMajor(25.0, 20.0, true));
        $this->assertEquals(575, LineDataCalculator::calculateTaxFromMajor(25.0, 23.0, true));
        $this->assertEquals(125, LineDataCalculator::calculateTaxFromMajor(25.0, 5.0, true));

        $this->assertEquals(5.0, LineDataCalculator::calculateTaxFromMajor(25.0, 20.0, false));
        $this->assertEquals(5.75, LineDataCalculator::calculateTaxFromMajor(25.0, 23.0, false));
        $this->assertEquals(1.25, LineDataCalculator::calculateTaxFromMajor(25.0, 5.0, false));
    }

    public function testCalculateTaxFromMinor(): void
    {
        $this->assertEquals(500, LineDataCalculator::calculateTaxFromMinor(2500, 20.0, true));
        $this->assertEquals(575, LineDataCalculator::calculateTaxFromMinor(2500, 23.0, true));
        $this->assertEquals(125, LineDataCalculator::calculateTaxFromMinor(2500, 5.0, true));

        $this->assertEquals(5.0, LineDataCalculator::calculateTaxFromMinor(2500, 20.0, false));
        $this->assertEquals(5.75, LineDataCalculator::calculateTaxFromMinor(2500, 23.0, false));
        $this->assertEquals(1.25, LineDataCalculator::calculateTaxFromMinor(2500, 5.0, false));
    }

    public function testCalculateTaxWithNullTaxRate(): void
    {
        $this->assertEquals(0, LineDataCalculator::calculateTaxFromMinor(2500, null));
        $this->assertEquals(0, LineDataCalculator::calculateTaxFromMajor(25.0, null));
    }

    public function testCalculateUnitPriceByTotalPriceFromMajor(): void
    {
        $this->assertEquals([2500, 2500], LineDataCalculator::calculateUnitPriceByTotalPriceFromMajor(25.0, 1.0, true));
        $this->assertEquals([1250, 2500], LineDataCalculator::calculateUnitPriceByTotalPriceFromMajor(25.0, 2.0, true));
        $this->assertEquals([1667, 2501], LineDataCalculator::calculateUnitPriceByTotalPriceFromMajor(25.0, 1.5, true));

        $this->assertEquals([25.0, 25.0], LineDataCalculator::calculateUnitPriceByTotalPriceFromMajor(25.0, 1.0, false));
        $this->assertEquals([12.5, 25.0], LineDataCalculator::calculateUnitPriceByTotalPriceFromMajor(25.0, 2.0, false));
        $this->assertEquals([16.67, 25.01], LineDataCalculator::calculateUnitPriceByTotalPriceFromMajor(25.0, 1.5, false));
    }

    public function testCalculateUnitPriceByTotalPriceFromMinor(): void
    {
        $this->assertEquals([2500, 2500], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(2500, 1.0, true));
        $this->assertEquals([1250, 2500], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(2500, 2.0, true));
        $this->assertEquals([1667, 2501], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(2500, 1.5, true));

        $this->assertEquals([25.0, 25.0], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(2500, 1.0, false));
        $this->assertEquals([12.5, 25.0], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(2500, 2.0, false));
        $this->assertEquals([16.67, 25.01], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(2500, 1.5, false));
    }

    public function testCalculateUnitPriceWithEmptyValues(): void
    {
        $this->assertNull(LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(0, 1.0));
        $this->assertNull(LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(100, 0));
        $this->assertNull(LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(null, 1.0));
        $this->assertNull(LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(100, null));
    }

    public function testCalculateUnitPriceWithDivisibleValues(): void
    {
        $this->assertEquals([10, 100], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(100, 10.0, true));
        $this->assertEquals([0.1, 1.0], LineDataCalculator::calculateUnitPriceByTotalPriceFromMinor(100, 10.0, false));
    }
}
