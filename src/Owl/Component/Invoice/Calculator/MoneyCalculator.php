<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Calculator;

class MoneyCalculator implements MoneyCalculatorInterface
{
    public const MINOR_TYPE = 'minor';

    public const MAJOR_TYPE = 'major';

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
    public static function calculateSubtotalFromMajor(float $unitPrice, float $quantity, bool $toMinor = false): int|float
    {
        $unitPriceMinor = self::toMinor($unitPrice);

        return self::calculateSubtotalFromMinor($unitPriceMinor, $quantity, $toMinor);
    }

    /**
     * Calculates total value from major unit price.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateSubtotalFromMinor(int $unitPrice, float $quantity, bool $toMinor = true): int|float
    {
        $subtotal = (int) round($unitPrice * $quantity);

        if ($toMinor) {
            return $subtotal;
        }

        return self::toMajor($subtotal);
    }

    /**
     * Calculates tax from subtotal.
     * Returns value in minor/major units (e.g. cents).
     */
    public static function calculateTaxFromMinor(int $subtotal, ?float $taxRate, bool $toMinor = true): int|float
    {
        if ($subtotal <= 0) {
            throw new \InvalidArgumentException("Subtotal must be greater than zero.");
        }

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
     * Calculates unit price from major subtotal.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateUnitPriceFromSubtotalMajor(float $subtotal, float $quantity, bool $toMinor = false): int|float
    {
        return self::calculateUnitPriceFromSubtotalMinor(self::toMinor($subtotal), $quantity, $toMinor);
    }

    /**
     * Calculates unit price from minor subtotal.
     * Returns value in minor units (e.g. cents).
     */
    public static function calculateUnitPriceFromSubtotalMinor(int $subtotal, float $quantity, bool $toMinor = true): int|float
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity must be greater than zero.");
        }

        if ($subtotal <= 0) {
            throw new \InvalidArgumentException("Subtotal must be greater than zero.");
        }

        $unitPrice = (int) round($subtotal / $quantity);

        if ($toMinor) {
            return $unitPrice;
        }

        return self::toMajor($unitPrice);
    }

    /**
     * Formats amount in minor units as a major unit string, e.g. 343 ➝ "3.43"
     */
    public static function formatMajor(int $amount, string $decimalSeparator = '.', string $thousandsSeparator = ''): string
    {
        return number_format($amount / 100, 2, $decimalSeparator, $thousandsSeparator);
    }
}
