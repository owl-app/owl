<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Calculator;

use Owl\Component\Invoice\Converter\CurrencyConverter;

class LineDataCalculator
{
    /**
     * Converts tax rate to minor units (e.g. 20 ➝ 0.2).
     */
    public static function normalizeTaxRate(float $vat): float
    {
        return $vat > 1 ? $vat / 100 : $vat;
    }

    /**
     * Calculates total value from minor unit price.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateTotalPriceFromMajor(float $unitPrice, float $quantity, bool $toMinor = false): int|float
    {
        $unitPriceMinor = CurrencyConverter::toMinor($unitPrice);

        return self::calculateTotalPriceFromMinor($unitPriceMinor, $quantity, $toMinor);
    }

    /**
     * Calculates total value from major unit price.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateTotalPriceFromMinor(int $unitPrice, float $quantity, bool $toMinor = true): int|float
    {
        if (!self::isNotEmpty([$unitPrice, $quantity])) {
            return 0;
        }

        $subtotal = (int) round($unitPrice * $quantity);

        if ($toMinor) {
            return $subtotal;
        }

        return CurrencyConverter::toMajor($subtotal);
    }

    /**
     * Calculates tax from subtotal major.
     * Returns value in minor/major units (e.g. cents).
     */
    public static function calculateTaxFromMajor(float $subtotal, ?float $taxRate, bool $toMinor = false): int|float
    {
        return self::calculateTaxFromMinor(CurrencyConverter::toMinor($subtotal), $taxRate, $toMinor);
    }

    /**
     * Calculates tax from subtotal minor.
     * Returns value in minor/major units (e.g. cents).
     */
    public static function calculateTaxFromMinor(int $subtotal, ?float $taxRate, bool $toMinor = true): int|float
    {
        if (is_null($taxRate)) {
            return 0;
        }

        $taxRate = self::normalizeTaxRate($taxRate);
        $tax = (int) round($subtotal * $taxRate);

        if ($toMinor) {
            return $tax;
        }

        return CurrencyConverter::toMajor($tax);
    }

    /**
     * Calculates unit price by subtotal from major.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateUnitPriceByTotalPriceFromMajor(?float $subtotal, ?float $quantity, bool $toMinor = false): ?array
    {
        return self::calculateUnitPriceByTotalPriceFromMinor(CurrencyConverter::toMinor($subtotal), $quantity, $toMinor);
    }

    /**
     * Calculates unit price by subtotal from minor.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateUnitPriceByTotalPriceFromMinor(?int $subtotal, ?float $quantity, bool $toMinor = true): ?array
    {
        if (!self::isNotEmpty([$quantity, $subtotal])) {
            return null;
        }

        $unitPrice = (int) round($subtotal / $quantity);

        if (!self::isDivisible($subtotal, $quantity)) {
            $subtotal = self::calculateTotalPriceFromMinor($unitPrice, $quantity);
        }

        return [
            $toMinor ? $unitPrice : CurrencyConverter::toMajor($unitPrice),
            $toMinor ? $subtotal : CurrencyConverter::toMajor($subtotal)
        ];
    }

    private static function isDivisible(int $value, float $quantity): bool
    {
        $result = $value / $quantity;

        return floor($result) == $result;
    }

    private static function isNotEmpty(array $array): bool
    {
        foreach ($array as $value) {
            if (empty($value) || is_null($value)) {
                return false;
            }
        }
        return true;
    }
}
