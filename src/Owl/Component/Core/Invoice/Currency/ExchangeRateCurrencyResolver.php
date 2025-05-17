<?php

declare(strict_types=1);

namespace Owl\Component\Core\Invoice\Currency;

use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

class ExchangeRateCurrencyResolver implements ExchangeRateCurrencyResolverInterface
{
    public function resolve(InvoiceInterface $invoice): ?CurrencyInterface
    {
        $invoiceCurrency = $invoice?->getCurrency();

        if ($invoiceCurrency) {
            $company = $invoice->getCompany();

            if ($company && $company->getCurrency()->getCode() !== $invoice?->getCurrency()?->getCode()) {
                return $company->getCurrency();
            }

            $contractor = $invoice->getContractor();

            if ($contractor && $contractor->getCurrency()->getCode() !== $invoice?->getCurrency()?->getCode()) {
                return $contractor->getCurrency();
            }
        }

        return null;
    }
}