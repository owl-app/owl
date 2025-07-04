<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Assigner;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Owl\Component\Invoice\Assigner\BuyerSnapshotAssigner;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class BuyerSnapshotAssignerTest extends TestCase
{
    private BuyerSnapshotAssigner $buyerSnapshotAssigner;

    private InvoiceInterface&MockObject $invoice;

    private EntityManagerInterface&MockObject $entityManager;

    private RepositoryInterface&MockObject $buyerSnapshotRepository;

    private UnitOfWork&MockObject $unitOfWork;

    protected function setUp(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->buyerSnapshotRepository = $this->createMock(RepositoryInterface::class);
        $this->unitOfWork = $this->createMock(UnitOfWork::class);

        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $this->buyerSnapshotAssigner = new BuyerSnapshotAssigner(
            $this->entityManager,
            $this->buyerSnapshotRepository,
        );
    }

    public function testAssignDoesNothingWhenBuyerHasNotChanged(): void
    {
        $buyer = $this->createMock(BuyerInterface::class);

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($buyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['buyer' => $buyer]);

        $this->buyerSnapshotRepository->expects($this->never())->method('findOneBy');
        $this->invoice->expects($this->never())->method('setBuyer');

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignUpdatesBuyerWhenBuyerHasChangedAndExistingSnapshotFound(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);
        $oldBuyer = $this->createMock(BuyerInterface::class);
        $existingSnapshot = $this->createMock(BuyerInterface::class);

        $newBuyer->method('getCompany')->willReturn('New Company');
        $oldBuyer->method('getCompany')->willReturn('Old Company');
        $newBuyer->method('getTaxNumber')->willReturn('123');
        $newBuyer->method('getStreet')->willReturn('Street');
        $newBuyer->method('getCity')->willReturn('City');
        $newBuyer->method('getPostcode')->willReturn('12345');

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
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

        $this->invoice->expects($this->once())
            ->method('setBuyer')
            ->with($existingSnapshot);

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignDoesNotUpdateBuyerWhenNoExistingSnapshotFound(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);
        $oldBuyer = $this->createMock(BuyerInterface::class);

        $newBuyer->method('getCompany')->willReturn('New Company');
        $oldBuyer->method('getCompany')->willReturn('Old Company');
        $newBuyer->method('getTaxNumber')->willReturn('123');
        $newBuyer->method('getStreet')->willReturn('Street');
        $newBuyer->method('getCity')->willReturn('City');
        $newBuyer->method('getPostcode')->willReturn('12345');

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['buyer' => $oldBuyer]);

        $this->buyerSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->invoice->expects($this->never())
            ->method('setBuyer');

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignHandlesNewBuyerWithoutOldBuyer(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn([]);

        $newBuyer->method('getCompany')->willReturn('Company');
        $newBuyer->method('getTaxNumber')->willReturn('123');
        $newBuyer->method('getStreet')->willReturn('Street');
        $newBuyer->method('getCity')->willReturn('City');
        $newBuyer->method('getPostcode')->willReturn('12345');

        $this->buyerSnapshotRepository->expects($this->once())
            ->method('findOneBy');

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignHandlesNullNewBuyer(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn(null);

        $this->buyerSnapshotRepository->expects($this->never())
            ->method('findOneBy');

        $this->invoice->expects($this->never())
            ->method('setBuyer');

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    #[DataProvider('buyerChangeFieldsProvider')]
    public function testBuyerChangedWhenFieldsDiffer(string $field, string $oldValue, string $newValue): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);
        $oldBuyer = $this->createMock(BuyerInterface::class);

        // Override the specific field being tested
        $newBuyer->method('get' . $field)->willReturn($newValue);
        $oldBuyer->method('get' . $field)->willReturn($oldValue);

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['buyer' => $oldBuyer]);

        $this->buyerSnapshotRepository->expects($this->once())
            ->method('findOneBy');

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignHandlesOriginalEntityDataWithoutBuyer(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $newBuyer = $this->createMock(BuyerInterface::class);

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($newBuyer);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['some_other_field' => 'value']);

        $newBuyer->method('getCompany')->willReturn('Company');
        $newBuyer->method('getTaxNumber')->willReturn('123');
        $newBuyer->method('getStreet')->willReturn('Street');
        $newBuyer->method('getCity')->willReturn('City');
        $newBuyer->method('getPostcode')->willReturn('12345');

        $this->buyerSnapshotRepository->expects($this->once())
            ->method('findOneBy');

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    public function testIsBuyerChangedReturnsFalseWhenAllFieldsMatch(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $buyer1 = $this->createMock(BuyerInterface::class);
        $buyer2 = $this->createMock(BuyerInterface::class);

        foreach (['Company', 'TaxNumber', 'Street', 'City', 'Postcode'] as $method) {
            $value = 'test_' . strtolower($method);
            $buyer1->method('get' . $method)->willReturn($value);
            $buyer2->method('get' . $method)->willReturn($value);
        }

        $this->invoice->expects($this->once())
            ->method('getBuyer')
            ->willReturn($buyer1);

        $this->unitOfWork->expects($this->once())
            ->method('getOriginalEntityData')
            ->with($this->invoice)
            ->willReturn(['buyer' => $buyer2]);

        $this->buyerSnapshotRepository->expects($this->never())
            ->method('findOneBy');

        $this->invoice->expects($this->never())
            ->method('setBuyer');

        $this->buyerSnapshotAssigner->assign($this->invoice);
    }

    public static function buyerChangeFieldsProvider(): array
    {
        return [
            'Company changed' => ['Company', 'Old Company', 'New Company'],
            'TaxNumber changed' => ['TaxNumber', '123', '456'],
            'Street changed' => ['Street', 'Old Street', 'New Street'],
            'City changed' => ['City', 'Old City', 'New City'],
            'Postcode changed' => ['Postcode', '12345', '67890'],
        ];
    }
}
