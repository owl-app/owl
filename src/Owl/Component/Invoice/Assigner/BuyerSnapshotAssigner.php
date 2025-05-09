<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Webmozart\Assert\Assert;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class BuyerSnapshotAssigner implements SnapshotAssignerInterface
{
    public function __construct(
        private RepositoryInterface $buyerSnapshotRepository,
    ) {
    }

    public function assign(InvoiceInterface $invoice): void
    {
        /** @var InvoiceInterface $invoice */
        Assert::isInstanceOf($invoice, InvoiceInterface::class);

        if (!$invoice->isBuyerChanged() || $invoice->getBuyer() === null) {
            return;
        }

        $buyer = $invoice->getBuyer();

        $existingSnapshot = $this->buyerSnapshotRepository->findOneBy([
            'company' => $buyer->getCompany(),
            'taxNumber' => $buyer->getTaxNumber(),
            'street' => $buyer->getStreet(),
            'city' => $buyer->getCity(),
            'postcode' => $buyer->getPostcode(),
        ]);

        if ($existingSnapshot) {
            $invoice->setBuyer($existingSnapshot);
        }
    }
}
