<?php

declare(strict_types=1);

namespace Owl\Component\Core\Invoice;

use Owl\Component\Core\Factory\BuyerFactoryInterface;
use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;

class BuyerSnapshotByContractorAssigner implements SnapshotAssignerInterface
{
    public function __construct(
        private SnapshotAssignerInterface$decoratedBuyerSnapshotRepository,
        private BuyerFactoryInterface $buyerFactory,
    ) {
    }

    public function assign(InvoiceInterface|Invoice $invoice): void
    {
        if ($invoice->isContractorChanged()) {
            $invoice->setBuyer(
                $this->buyerFactory->createFromContractor($invoice->getContractor())
            );
        }

        $this->decoratedBuyerSnapshotRepository->assign($invoice);
    }
}
