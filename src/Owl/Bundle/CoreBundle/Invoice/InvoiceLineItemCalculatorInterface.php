<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Invoice;

interface InvoiceLineItemCalculatorInterface
{
    public function tryCalculateBySubtotal(float $subtotal, float $quantity): ?array;

    public function tryCalculateByUnitPrice(float $unitPrice, float $quantity): ?float;
}
