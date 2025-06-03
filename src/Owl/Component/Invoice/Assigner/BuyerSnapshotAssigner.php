<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class BuyerSnapshotAssigner implements SnapshotAssignerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RepositoryInterface $buyerSnapshotRepository,
    ) {
    }

    public function assign(InvoiceInterface $invoice): void
    {
        $newBuyer = $invoice->getBuyer();
        $uow = $this->entityManager->getUnitOfWork();
        $oldBuyer = $uow->getOriginalEntityData($invoice)['buyer'] ?? null;

        if (!$newBuyer || !$this->isBuyerChanged($newBuyer, $oldBuyer)) {
            return;
        }

        $existingSnapshot = $this->buyerSnapshotRepository->findOneBy([
            'company' => $newBuyer->getCompany(),
            'taxNumber' => $newBuyer->getTaxNumber(),
            'street' => $newBuyer->getStreet(),
            'city' => $newBuyer->getCity(),
            'postcode' => $newBuyer->getPostcode(),
        ]);

        if ($existingSnapshot) {
            $invoice->setBuyer($existingSnapshot);
        }
    }

    private function isBuyerChanged(BuyerInterface $newBuyer, ?BuyerInterface $oldBuyer): bool
    {
        if (
            ($newBuyer !== null && $oldBuyer === null) ||
            $newBuyer->getCompany() !== $oldBuyer->getCompany() ||
            $newBuyer->getTaxNumber() !== $oldBuyer->getTaxNumber() ||
            $newBuyer->getStreet() !== $oldBuyer->getStreet() ||
            $newBuyer->getCity() !== $oldBuyer->getCity() ||
            $newBuyer->getPostcode() !== $oldBuyer->getPostcode()
        ) {
            return true;
        }

        return false;
    }
}
