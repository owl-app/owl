<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Calculator;

class LineDataCalculator
{
    /**
     * Converts main currency units (e.g. EUR) to minor units (e.g. cents).
     */
    public static function toMinor(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Converts minor currency units (e.g. cents) to major units (e.g. EUR).
     */
    public static function toMajor(int $amount): float
    {
        return round($amount / 100, 2);
    }

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
    public static function calculatebyUnitPriceFromMajor(float $unitPrice, float $quantity, bool $toMinor = false): int|float
    {
        $unitPriceMinor = self::toMinor($unitPrice);

        return self::calculateByUnitPriceFromMinor($unitPriceMinor, $quantity, $toMinor);
    }

    /**
     * Calculates total value from major unit price.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateByUnitPriceFromMinor(int $unitPrice, float $quantity, bool $toMinor = true): int|float
    {
        if (!self::isNotEmpty([$unitPrice, $quantity])) {
            return 0;
        }

        $subtotal = (int) round($unitPrice * $quantity);

        if ($toMinor) {
            return $subtotal;
        }

        return self::toMajor($subtotal);
    }

    /**
     * Calculates tax from subtotal major.
     * Returns value in minor/major units (e.g. cents).
     */
    public static function calculateTaxFromMajor(float $subtotal, ?float $taxRate, bool $toMinor = false): int|float
    {
        return self::calculateTaxFromMinor(self::toMinor($subtotal), $taxRate, $toMinor);
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

        return self::toMajor($tax);
    }

    /**
     * Calculates unit price by subtotal from major.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateBySumFromMajor(?float $subtotal, ?float $quantity, bool $toMinor = false): ?array
    {
        return self::calculateBySumFromMinor(self::toMinor($subtotal), $quantity, $toMinor);
    }

    /**
     * Calculates unit price by subtotal from minor.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateBySumFromMinor(?int $subtotal, ?float $quantity, bool $toMinor = true): ?array
    {
        if (!self::isNotEmpty([$quantity, $subtotal])) {
            return null;
        }

        $unitPrice = (int) round($subtotal / $quantity);

        if (!self::isDivisible($subtotal, $quantity)) {
            $subtotal = self::calculateByUnitPriceFromMinor($unitPrice, $quantity);
        }

        return [
            $toMinor ? $unitPrice : self::toMajor($unitPrice),
            $toMinor ? $subtotal : self::toMajor($subtotal)
        ];
    }

    /**
     * Formats amount in minor units as a major unit string, e.g. 343 ➝ "3.43"
     */
    public static function formatMajor(int $amount, string $decimalSeparator = '.', string $thousandsSeparator = ''): string
    {
        return number_format($amount / 100, 2, $decimalSeparator, $thousandsSeparator);
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
