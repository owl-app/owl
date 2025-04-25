<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Invoice;

use Owl\Component\Invoice\Calculator\MoneyCalculator;

final class InvoiceLineItemCalculator implements InvoiceLineItemCalculatorInterface
{
    public function tryCalculateBySubtotal(?float $subtotal, ?float $quantity): ?array
    {
        if (!$this->isNotEmpty([$quantity, $subtotal])) {
            return null;
        }

        $unitPrice = MoneyCalculator::calculateUnitPriceFromSubtotalMajor($subtotal, $quantity);

        if (!$this->isDivisible($subtotal, $quantity)) {
            $subtotal = MoneyCalculator::calculateSubtotalFromMajor($unitPrice, $quantity);
        }

        return [$unitPrice, $subtotal];
    }

    public function tryCalculateByUnitPrice(?float $unitPrice, ?float $quantity): ?float
    {
        if (!$this->isNotEmpty([$unitPrice, $quantity])) {
            return null;
        }

        $subtotal = MoneyCalculator::calculateSubtotalFromMajor($unitPrice, $quantity);

        return $subtotal;
    }

    private function isDivisible(float $value, float $quantity): bool
    {
        $int = MoneyCalculator::toMinor($value);
        $result = $int / $quantity;

        return floor($result) == $result;
    }

    private function isNotEmpty(array $array): bool
    {
        foreach ($array as $value) {
            if (empty($value) || is_null($value)) {
                return false;
            }
        }
        return true;
    }
}
