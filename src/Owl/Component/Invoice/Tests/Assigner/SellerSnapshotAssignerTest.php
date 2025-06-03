<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Assigner;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Owl\Component\Invoice\Assigner\SellerSnapshotAssigner;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;

class SellerSnapshotAssignerTest extends TestCase
{
    private SellerSnapshotAssigner $sellerSnapshotAssigner;

    private InvoiceInterface&MockObject $invoice;

    private EntityManagerInterface&MockObject $entityManager;

    private RepositoryInterface&MockObject $sellerSnapshotRepository;

    private UnitOfWork&MockObject $unitOfWork;

    protected function setUp(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->sellerSnapshotRepository = $this->createMock(RepositoryInterface::class);
        $this->unitOfWork = $this->createMock(UnitOfWork::class);

        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->sellerSnapshotAssigner = new SellerSnapshotAssigner(
            $this->entityManager,
            $this->sellerSnapshotRepository
        );
    }

    public function testAssignDoesNothingWhenSellerHasNotChanged(): void
    {
        $seller = $this->createMock(SellerInterface::class);

        $this->invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($seller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['seller' => $seller]);

        $this->sellerSnapshotRepository->expects($this->never())->method('findOneBy');
        $this->invoice->expects($this->never())->method('setSeller');

        $this->sellerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignUpdatesSellerWhenSellerHasChangedAndExistingSnapshotFound(): void
    {
        $newSeller = $this->createMock(SellerInterface::class);
        $oldSeller = $this->createMock(SellerInterface::class);
        $existingSnapshot = $this->createMock(SellerInterface::class);

        $newSeller->method('getCompany')->willReturn('New Company');
        $oldSeller->method('getCompany')->willReturn('Old Company');
        $newSeller->method('getTaxNumber')->willReturn('123456789');
        $newSeller->method('getStreet')->willReturn('Main Street');
        $newSeller->method('getCity')->willReturn('New York');
        $newSeller->method('getPostcode')->willReturn('10001');

        $this->invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($newSeller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['seller' => $oldSeller]);

        $this->sellerSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'company' => 'New Company',
                'taxNumber' => '123456789',
                'street' => 'Main Street',
                'city' => 'New York',
                'postcode' => '10001',
            ])
            ->willReturn($existingSnapshot);

        $this->invoice->expects($this->once())
            ->method('setSeller')
            ->with($existingSnapshot);

        $this->sellerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignDoesNotUpdateSellerWhenNoExistingSnapshotFound(): void
    {
        $newSeller = $this->createMock(SellerInterface::class);
        $oldSeller = $this->createMock(SellerInterface::class);

        $newSeller->method('getCompany')->willReturn('New Company');
        $oldSeller->method('getCompany')->willReturn('Old Company');
        $newSeller->method('getTaxNumber')->willReturn('123456789');
        $newSeller->method('getStreet')->willReturn('Main Street');
        $newSeller->method('getCity')->willReturn('New York');
        $newSeller->method('getPostcode')->willReturn('10001');

        $this->invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($newSeller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['seller' => $oldSeller]);

        $this->sellerSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->invoice->expects($this->never())
            ->method('setSeller');

        $this->sellerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignDetectsChangesInSellerAttributes(): void
    {
        $newSeller = $this->createMock(SellerInterface::class);
        $oldSeller = $this->createMock(SellerInterface::class);

        $newSeller->method('getCompany')->willReturn('Same Company');
        $oldSeller->method('getCompany')->willReturn('Same Company');
        $newSeller->method('getTaxNumber')->willReturn('NEW123456');
        $oldSeller->method('getTaxNumber')->willReturn('OLD123456');
        $newSeller->method('getStreet')->willReturn('Main Street');
        $oldSeller->method('getStreet')->willReturn('Main Street');
        $newSeller->method('getCity')->willReturn('New York');
        $oldSeller->method('getCity')->willReturn('New York');
        $newSeller->method('getPostcode')->willReturn('10001');
        $oldSeller->method('getPostcode')->willReturn('10001');

        $this->invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($newSeller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['seller' => $oldSeller]);

        $this->sellerSnapshotRepository->expects($this->once())
            ->method('findOneBy');

        $this->sellerSnapshotAssigner->assign($this->invoice);
    }
}