<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Assigner;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Owl\Component\Invoice\Assigner\BuyerSnapshotAssigner;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class BuyerSnapshotAssignerTest extends TestCase
{
    private EntityManagerInterface|MockObject $entityManager;
    private RepositoryInterface|MockObject $buyerSnapshotRepository;
    private BuyerSnapshotAssigner $buyerSnapshotAssigner;
    private UnitOfWork|MockObject $unitOfWork;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->buyerSnapshotRepository = $this->createMock(RepositoryInterface::class);
        $this->unitOfWork = $this->createMock(UnitOfWork::class);

        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->buyerSnapshotAssigner = new BuyerSnapshotAssigner(
            $this->entityManager,
            $this->buyerSnapshotRepository
        );
    }

    public function testAssignDoesNothingWhenBuyerHasNotChanged(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $buyer = $this->createMock(BuyerInterface::class);

        $invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($buyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
            ->willReturn(['buyer' => $buyer]);

        $this->buyerSnapshotRepository->expects($this->never())->method('findOneBy');
        $invoice->expects($this->never())->method('setBuyer');

        $this->buyerSnapshotAssigner->assign($invoice);
    }

    public function testAssignUpdatesBuyerWhenBuyerHasChangedAndExistingSnapshotFound(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);
        $oldBuyer = $this->createMock(BuyerInterface::class);
        $existingSnapshot = $this->createMock(BuyerInterface::class);

        $newBuyer->method('getCompany')->willReturn('New Company');
        $oldBuyer->method('getCompany')->willReturn('Old Company');
        $newBuyer->method('getTaxNumber')->willReturn('123');
        $newBuyer->method('getStreet')->willReturn('Street');
        $newBuyer->method('getCity')->willReturn('City');
        $newBuyer->method('getPostcode')->willReturn('12345');

        $invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
            ->willReturn(['buyer' => $oldBuyer]);

        $this->buyerSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'company' => 'New Company',
                'taxNumber' => '123',
                'street' => 'Street',
                'city' => 'City',
                'postcode' => '12345',
            ])
            ->willReturn($existingSnapshot);

        $invoice->expects($this->once())
            ->method('setBuyer')
            ->with($existingSnapshot);

        $this->buyerSnapshotAssigner->assign($invoice);
    }

    public function testAssignDoesNotUpdateBuyerWhenNoExistingSnapshotFound(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);
        $oldBuyer = $this->createMock(BuyerInterface::class);

        $newBuyer->method('getCompany')->willReturn('New Company');
        $oldBuyer->method('getCompany')->willReturn('Old Company');
        $newBuyer->method('getTaxNumber')->willReturn('123');
        $newBuyer->method('getStreet')->willReturn('Street');
        $newBuyer->method('getCity')->willReturn('City');
        $newBuyer->method('getPostcode')->willReturn('12345');

        $invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
            ->willReturn(['buyer' => $oldBuyer]);

        $this->buyerSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $invoice->expects($this->never())
            ->method('setBuyer');

        $this->buyerSnapshotAssigner->assign($invoice);
    }

    public function testAssignHandlesNewBuyerWithoutOldBuyer(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);

        $invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($invoice)
            ->willReturn([]);

        $newBuyer->method('getCompany')->willReturn('Company');
        $newBuyer->method('getTaxNumber')->willReturn('123');
        $newBuyer->method('getStreet')->willReturn('Street');
        $newBuyer->method('getCity')->willReturn('City');
        $newBuyer->method('getPostcode')->willReturn('12345');

        $this->buyerSnapshotRepository->expects($this->once())
            ->method('findOneBy');

        $this->buyerSnapshotAssigner->assign($invoice);
    }
}