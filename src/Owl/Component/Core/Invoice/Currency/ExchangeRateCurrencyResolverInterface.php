<?php

declare(strict_types=1);

namespace Owl\Component\Core\Invoice\Currency;

use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

interface ExchangeRateCurrencyResolverInterface
{
    public function resolve(InvoiceInterface $invoice): ?CurrencyInterface;
}
