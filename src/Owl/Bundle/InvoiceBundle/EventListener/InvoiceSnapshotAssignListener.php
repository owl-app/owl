<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Model\LineItemInterface;
use Webmozart\Assert\Assert;
use Owl\Component\Invoice\Factory\InvoiceTaxRateSnapshotFactoryInterface;
use Owl\Component\Invoice\Model\BaseInvoiceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class InvoiceSnapshotAssignListener
{
    public function __construct(
        private RepositoryInterface $taxRateSnapshotRepository,
        private InvoiceTaxRateSnapshotFactoryInterface $invoiceTaxRateSnapshotFactory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function assignSnapshot(GenericEvent $event): void
    {
        $invoice = $event->getSubject();

        /** @var BaseInvoiceInterface $invoice */
        Assert::isInstanceOf($invoice, BaseInvoiceInterface::class);

        foreach($invoice->getLineItems() as $lineItem) {
            $this->assignSnapshotToLineItem($lineItem);
        }
    }

    private function assignSnapshotToLineItem(LineItemInterface $lineItem): void
    {
        if ($lineItem->getTaxRate() === null) {
            return;
        }

        $taxRateSnapshot = $lineItem->getTaxRateSnapshot();
        $taxRate =  $lineItem->getTaxRate();
        $dataSnapshot = [];

        if ($lineItem->getId() === null || $taxRate->getCode() !== $lineItem->getTaxRateSnapshot()->getCode()) {
            $dataSnapshot = [
                'code' => $taxRate->getCode(),
                'name' => $taxRate->getName(),
                'amount' => $taxRate->getAmount(),
            ];
        } else if ($taxRateSnapshot->isNameChanged() || $taxRateSnapshot->isAmountChanged()) {
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
            $snapshot = $this->invoiceTaxRateSnapshotFactory->create(...$dataSnapshot);

            $lineItem->setTaxRateSnapshot($snapshot);
        }
    }
}
