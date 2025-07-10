<?php

declare(strict_types=1);

namespace Owl\Component\Core\Invoice\Snapshot;

use Owl\Component\Core\Factory\SellerFactoryInterface;
use Owl\Component\Core\Model\CompanyInterface as CoreCompanyInterface;
use Owl\Component\Core\Model\Invoice\Invoice;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;

/**
 * @template T of SellerInterface
 */
class SellerSnapshotByCompanyAssigner implements SnapshotAssignerInterface
{
    /**
     * @param SellerFactoryInterface<SellerInterface> $sellerFactory
     */
    public function __construct(
        private SnapshotAssignerInterface $decoratedBuyerSnapshotRepository,
        private SellerFactoryInterface $sellerFactory,
    ) {
    }

    public function assign(InvoiceInterface|Invoice $invoice): void
    {
        if ($invoice instanceof Invoice && $invoice->isCompanyChanged()) {
            $company = $invoice->getCompany();
            if ($company instanceof CoreCompanyInterface) {
                $invoice->setSeller(
                    $this->sellerFactory->createFromCompany($company),
                );
            }

            $this->decoratedBuyerSnapshotRepository->assign($invoice);
        }
    }
}
