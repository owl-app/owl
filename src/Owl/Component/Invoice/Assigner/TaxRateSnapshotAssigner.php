<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Factory\InvoiceTaxRateSnapshotFactoryInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class TaxRateSnapshotAssigner implements SnapshotAssignerInterface
{
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

        if ($lineItem->getId() === null || $taxRate->getCode() !== $lineItem->getTaxRateSnapshot()->getCode()) {
            $dataSnapshot = [
                'code' => $taxRate->getCode(),
                'name' => $taxRate->getName(),
                'amount' => $taxRate->getAmount(),
            ];
        } elseif ($taxRateSnapshot->isNameChanged() || $taxRateSnapshot->isAmountChanged()) {
            $dataSnapshot = [
                'code' => $lineItem->getTaxRateSnapshot()->getCode(),
                'name' => $lineItem->getTaxRateSnapshot()->getName(),
                'amount' => $lineItem->getTaxRateSnapshot()->getAmount(),
            ];
        }

        if (empty($dataSnapshot)) {
            return;
        }

        $existingSnapshot = $this->taxRateSnapshotRepository->findOneBy($dataSnapshot);

        if ($taxRateSnapshot) {
            $this->entityManager->detach($taxRateSnapshot);
        }

        if ($existingSnapshot) {
            $lineItem->setTaxRateSnapshot($existingSnapshot);
        } else {
            $snapshot = $this->invoiceTaxRateSnapshotFactory->createNew($dataSnapshot);

            $lineItem->setTaxRateSnapshot($snapshot);
        }
    }
}