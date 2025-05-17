<?php

declare(strict_types=1);

namespace Owl\Component\Core\Invoice\Snapshot;

use Owl\Component\Core\Factory\SellerFactoryInterface;
use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;

class SellerSnapshotByCompanyAssigner implements SnapshotAssignerInterface
{
    public function __construct(
        private SnapshotAssignerInterface $decoratedBuyerSnapshotRepository,
        private SellerFactoryInterface $sellerFactory,
    ) {
    }

    public function assign(InvoiceInterface|Invoice $invoice): void
    {
        if ($invoice->isCompanyChanged()) {
            $invoice->setSeller(
                $this->sellerFactory->createFromCompany($invoice->getCompany())
            );

            $this->decoratedBuyerSnapshotRepository->assign($invoice);
        }
    }
}
