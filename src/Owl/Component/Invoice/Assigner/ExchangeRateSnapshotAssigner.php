<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Owl\Component\Core\Invoice\Currency\ExchangeRateCurrencyResolverInterface;
use Owl\Component\Core\Model\Invoice\InvoiceInterface as CoreInvoiceInterface;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshotInterface;
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
        /** @var CoreInvoiceInterface $invoice */
        $currency = $this->exchangeRateCurrencyResolver->resolve($invoice);
        /** @var ExchangeRateSnapshotInterface|null $exchangeRateSnapshot */
        $exchangeRateSnapshot = $invoice->getExchangeRateSnapshot();
        $currencyCode = $currency?->getCode() ?? null;

        if ($exchangeRateSnapshot && $currencyCode && ($currencyCode !== $exchangeRateSnapshot->getCode() || $exchangeRateSnapshot->isRatioChanged())) {
            $existingSnapshot = $this->exchangeRateSnapshotRepository->findOneBy([
                'code' => $currencyCode,
                'ratio' => $exchangeRateSnapshot->getRatio(),
            ]);

            if ($existingSnapshot instanceof ExchangeRateSnapshotInterface) {
                $invoice->setExchangeRateSnapshot($existingSnapshot);
            } else {
                $exchangeRateSnapshot->setCode($currencyCode);
            }
        }
    }
}
