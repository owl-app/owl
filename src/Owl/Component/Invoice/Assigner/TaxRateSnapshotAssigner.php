<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Factory\InvoiceTaxRateSnapshotFactoryInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of TaxRateSnapshotInterface
 */
class TaxRateSnapshotAssigner implements SnapshotAssignerInterface
{
    /**
     * @param RepositoryInterface<T> $taxRateSnapshotRepository
     * @param InvoiceTaxRateSnapshotFactoryInterface<T> $invoiceTaxRateSnapshotFactory
     */
    public function __construct(
        private RepositoryInterface $taxRateSnapshotRepository,
        private InvoiceTaxRateSnapshotFactoryInterface $invoiceTaxRateSnapshotFactory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function assign(InvoiceInterface $invoice): void
    {
        foreach ($invoice->getLineItems() as $lineItem) {
            $this->assignSnapshotToLineItem($lineItem);
        }
    }

    private function assignSnapshotToLineItem(LineItemInterface $lineItem): void
    {
        if ($lineItem->getTaxRate() === null) {
            return;
        }

        $taxRateSnapshot = $lineItem->getTaxRateSnapshot();
        $taxRate = $lineItem->getTaxRate();
        $dataSnapshot = [];

        if ($lineItem->getId() === null || ($taxRateSnapshot !== null && $taxRate->getCode() !== $taxRateSnapshot->getCode())) {
            $dataSnapshot = [
                'code' => $taxRate->getCode(),
                'name' => $taxRate->getName(),
                'amount' => $taxRate->getAmount(),
            ];
        } elseif ($taxRateSnapshot !== null && ($taxRateSnapshot->isNameChanged() || $taxRateSnapshot->isAmountChanged())) {
            $dataSnapshot = [
                'code' => $taxRateSnapshot->getCode(),
                'name' => $taxRateSnapshot->getName(),
                'amount' => $taxRateSnapshot->getAmount(),
            ];
        }

        if (empty($dataSnapshot)) {
            return;
        }

        /** @var TaxRateSnapshotInterface|null $existingSnapshot */
        $existingSnapshot = $this->taxRateSnapshotRepository->findOneBy($dataSnapshot);

        if ($taxRateSnapshot !== null) {
            $this->entityManager->detach($taxRateSnapshot);
        }

        if ($existingSnapshot !== null) {
            $lineItem->setTaxRateSnapshot($existingSnapshot);
        } else {
            /** @var TaxRateSnapshotInterface $snapshot */
            $snapshot = $this->invoiceTaxRateSnapshotFactory->create(...array_values($dataSnapshot));

            $lineItem->setTaxRateSnapshot($snapshot);
        }
    }
}
