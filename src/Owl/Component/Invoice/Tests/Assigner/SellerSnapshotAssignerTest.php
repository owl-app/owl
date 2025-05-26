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

    private EntityManagerInterface|MockObject $entityManager;

    private RepositoryInterface|MockObject $sellerSnapshotRepository;

    private UnitOfWork|MockObject $unitOfWork;

    protected function setUp(): void
    {
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
        $invoice = $this->createMock(InvoiceInterface::class);
        $seller = $this->createMock(SellerInterface::class);

        $invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($seller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
            ->willReturn(['seller' => $seller]);

        $this->sellerSnapshotRepository->expects($this->never())->method('findOneBy');
        $invoice->expects($this->never())->method('setSeller');

        $this->sellerSnapshotAssigner->assign($invoice);
    }

    public function testAssignUpdatesSellerWhenSellerHasChangedAndExistingSnapshotFound(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $newSeller = $this->createMock(SellerInterface::class);
        $oldSeller = $this->createMock(SellerInterface::class);
        $existingSnapshot = $this->createMock(SellerInterface::class);

        $newSeller->method('getCompany')->willReturn('New Company');
        $oldSeller->method('getCompany')->willReturn('Old Company');
        $newSeller->method('getTaxNumber')->willReturn('123456789');
        $newSeller->method('getStreet')->willReturn('Main Street');
        $newSeller->method('getCity')->willReturn('New York');
        $newSeller->method('getPostcode')->willReturn('10001');

        $invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($newSeller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
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

        $invoice->expects($this->once())
            ->method('setSeller')
            ->with($existingSnapshot);

        $this->sellerSnapshotAssigner->assign($invoice);
    }

    public function testAssignDoesNotUpdateSellerWhenNoExistingSnapshotFound(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $newSeller = $this->createMock(SellerInterface::class);
        $oldSeller = $this->createMock(SellerInterface::class);

        $newSeller->method('getCompany')->willReturn('New Company');
        $oldSeller->method('getCompany')->willReturn('Old Company');
        $newSeller->method('getTaxNumber')->willReturn('123456789');
        $newSeller->method('getStreet')->willReturn('Main Street');
        $newSeller->method('getCity')->willReturn('New York');
        $newSeller->method('getPostcode')->willReturn('10001');

        $invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($newSeller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
            ->willReturn(['seller' => $oldSeller]);

        $this->sellerSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $invoice->expects($this->never())
            ->method('setSeller');

        $this->sellerSnapshotAssigner->assign($invoice);
    }

    public function testAssignDetectsChangesInSellerAttributes(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
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

        $invoice->expects($this->once())
            ->method('getSeller')
            ->willReturn($newSeller);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
            ->willReturn(['seller' => $oldSeller]);

        $this->sellerSnapshotRepository->expects($this->once())
            ->method('findOneBy');

        $this->sellerSnapshotAssigner->assign($invoice);
    }
}