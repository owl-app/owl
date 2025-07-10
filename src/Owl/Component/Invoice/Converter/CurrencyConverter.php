<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Converter;

class CurrencyConverter
{
    /**
     * Rounds the amount to the specified precision.
     *
     * @param float|int $amount The amount to round.
     *
     * @return float The rounded amount.
     */
    public static function defaultRound(float|int $amount): float
    {
        return round($amount, 2);
    }

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
        return self::defaultRound($amount / 100);
    }

    /**
     * Formats amount in minor units as a major unit string, e.g. 343 ➝ "3.43"
     */
    public static function formatMajor(int $amount, string $decimalSeparator = '.', string $thousandsSeparator = ''): string
    {
        return number_format($amount / 100, 2, $decimalSeparator, $thousandsSeparator);
    }
}
