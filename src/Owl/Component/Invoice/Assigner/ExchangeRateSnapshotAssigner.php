<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolverInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ExchangeRateSnapshotAssigner implements SnapshotAssignerInterface
{
    public function __construct(
        private ExchangeRateCurrencyResolverInterface $exchangeRateCurrencyResolver,
        private RepositoryInterface $exchangeRateSnapshotRepository,
    ) {
    }

    public function assign(InvoiceInterface $invoice): void
    {
        $currency = $this->exchangeRateCurrencyResolver->resolve($invoice);
        $exchangeRateSnapshot = $invoice->getExchangeRateSnapshot();

        if ($currency?->getCode() !== $exchangeRateSnapshot->getCode() || $exchangeRateSnapshot->isRatioChanged()) {
            $existingSnapshot = $this->exchangeRateSnapshotRepository->findOneBy([
                'code' => $currency?->getCode(),
                'ratio' => $exchangeRateSnapshot->getRatio(),
            ]);

            if ($existingSnapshot) {
                $invoice->setExchangeRateSnapshot($existingSnapshot);
            } else {
                $exchangeRateSnapshot->setCode($currency->getCode());
            }
        }
    }
}
