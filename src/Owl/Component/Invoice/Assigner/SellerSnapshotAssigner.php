<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Assigner;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class SellerSnapshotAssigner implements SnapshotAssignerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RepositoryInterface $sellerSnapshotRepository,
    ) {
    }

    public function assign(InvoiceInterface $invoice): void
    {
        $newSeller = $invoice->getSeller();
        $uow = $this->entityManager->getUnitOfWork();
        $oldSeller = $uow->getOriginalEntityData($invoice)['seller'] ?? null;

        if (!$this->isSellerChanged($newSeller, $oldSeller)) {
            return;
        }

        $existingSnapshot = $this->sellerSnapshotRepository->findOneBy([
            'company' => $newSeller->getCompany(),
            'taxNumber' => $newSeller->getTaxNumber(),
            'street' => $newSeller->getStreet(),
            'city' => $newSeller->getCity(),
            'postcode' => $newSeller->getPostcode(),
        ]);

        if ($existingSnapshot) {
            $invoice->setSeller($existingSnapshot);
        }
    }

    private function isSellerChanged(SellerInterface $newSeller, ?SellerInterface $oldSeller): bool
    {
        if (
            ($newSeller !== null && $oldSeller === null) ||
            $newSeller->getCompany() !== $oldSeller->getCompany() ||
            $newSeller->getTaxNumber() !== $oldSeller->getTaxNumber() ||
            $newSeller->getStreet() !== $oldSeller->getStreet() ||
            $newSeller->getCity() !== $oldSeller->getCity() ||
            $newSeller->getPostcode() !== $oldSeller->getPostcode()
        ) {
            return true;
        }

        return false;
    }
}
