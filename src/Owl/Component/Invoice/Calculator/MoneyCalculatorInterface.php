<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Calculator;

interface MoneyCalculatorInterface
{
    public static function toMinor(float $amount): int;

    public static function toMajor(int $amount): float;

    public static function normalizeTaxRate(float $vat): float;

    public static function calculateSubtotalFromMajor(float $unitPrice, float $quantity, bool $toMinor = false): int|float;

    public static function calculateSubtotalFromMinor(int $unitPrice, float $quantity, bool $toMinor = true): int|float;

    public static function calculateTaxFromMinor(int $subtotal, ?float $taxRate, bool $toMinor = true): int|float;

    public static function calculateUnitPriceFromSubtotalMajor(float $subtotal, float $quantity, bool $toMinor = false): int|float;

    public static function calculateUnitPriceFromSubtotalMinor(int $subtotal, float $quantity, bool $toMinor = true): int|float;

    public static function formatMajor(int $amount, string $decimalSeparator = '.', string $thousandsSeparator = ''): string;
}
