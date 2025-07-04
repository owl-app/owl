<?php

declare(strict_types=1);

namespace Owl\Component\Core\Invoice\Snapshot;

use Owl\Component\Core\Factory\BuyerFactoryInterface;
use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Core\Model\ContractorInterface;

/**
 * @template T of \Owl\Component\Core\Model\BuyerInterface
 */
class BuyerSnapshotByContractorAssigner implements SnapshotAssignerInterface
{
    /**
     * @param SnapshotAssignerInterface $decoratedBuyerSnapshotRepository
     * @param BuyerFactoryInterface<T> $buyerFactory
     */
    public function __construct(
        private SnapshotAssignerInterface $decoratedBuyerSnapshotRepository,
        private BuyerFactoryInterface $buyerFactory,
    ) {
    }

    public function assign(InvoiceInterface|Invoice $invoice): void
    {
        if ($invoice instanceof Invoice && $invoice->isContractorChanged()) {
            /** @var ContractorInterface|null $contractor */
            $contractor = $invoice->getContractor();
            if ($contractor !== null) {
                $invoice->setBuyer(
                    $this->buyerFactory->createFromContractor($contractor),
                );
            }

            $this->decoratedBuyerSnapshotRepository->assign($invoice);
        }
    }
}
