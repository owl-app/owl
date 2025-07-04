<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Assigner;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Owl\Component\Invoice\Assigner\TaxRateSnapshotAssigner;
use Owl\Component\Invoice\Factory\InvoiceTaxRateSnapshotFactoryInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class TaxRateSnapshotAssignerTest extends TestCase
{
    private TaxRateSnapshotAssigner $taxRateSnapshotAssigner;

    private InvoiceInterface&MockObject $invoice;

    private RepositoryInterface&MockObject $taxRateSnapshotRepository;

    private InvoiceTaxRateSnapshotFactoryInterface&MockObject $invoiceTaxRateSnapshotFactory;

    private EntityManagerInterface&MockObject $entityManager;

    protected function setUp(): void
    {
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->taxRateSnapshotRepository = $this->createMock(RepositoryInterface::class);
        $this->invoiceTaxRateSnapshotFactory = $this->createMock(InvoiceTaxRateSnapshotFactoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->taxRateSnapshotAssigner = new TaxRateSnapshotAssigner(
            $this->taxRateSnapshotRepository,
            $this->invoiceTaxRateSnapshotFactory,
            $this->entityManager,
        );
    }

    public function testAssignSkipsLineItemsWithoutTaxRate(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $lineItems = new ArrayCollection([$lineItem]);

        $this->invoice->expects($this->once())
            ->method('getLineItems')
            ->willReturn($lineItems);

        $lineItem->expects($this->once())
            ->method('getTaxRate')
            ->willReturn(null);

        $lineItem->expects($this->never())->method('getTaxRateSnapshot');

        $this->taxRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignCreatesSnapshotForNewLineItem(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $newSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $lineItems = new ArrayCollection([$lineItem]);

        $this->invoice->expects($this->once())
            ->method('getLineItems')
            ->willReturn($lineItems);

        $lineItem->method('getId')->willReturn(null);
        $lineItem->method('getTaxRate')->willReturn($taxRate);
        $lineItem->method('getTaxRateSnapshot')->willReturn($taxRateSnapshot);

        $taxRate->method('getCode')->willReturn('VAT23');
        $taxRate->method('getName')->willReturn('VAT 23%');
        $taxRate->method('getAmount')->willReturn(0.23);

        $expectedData = [
            'code' => 'VAT23',
            'name' => 'VAT 23%',
            'amount' => 0.23,
        ];

        $this->taxRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with($expectedData)
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('detach')
            ->with($taxRateSnapshot);

        $this->invoiceTaxRateSnapshotFactory->expects($this->once())
            ->method('create')
            ->with('VAT23', 'VAT 23%', 0.23)
            ->willReturn($newSnapshot);

        $lineItem->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($newSnapshot);

        $this->taxRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignUsesExistingSnapshotWhenFound(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $existingSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $lineItems = new ArrayCollection([$lineItem]);

        $this->invoice->expects($this->once())
            ->method('getLineItems')
            ->willReturn($lineItems);

        $lineItem->method('getId')->willReturn(null);
        $lineItem->method('getTaxRate')->willReturn($taxRate);
        $lineItem->method('getTaxRateSnapshot')->willReturn($taxRateSnapshot);

        $taxRate->method('getCode')->willReturn('VAT23');
        $taxRate->method('getName')->willReturn('VAT 23%');
        $taxRate->method('getAmount')->willReturn(0.23);

        $expectedData = [
            'code' => 'VAT23',
            'name' => 'VAT 23%',
            'amount' => 0.23,
        ];
        $this->taxRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with($expectedData)
            ->willReturn($existingSnapshot);

        $this->entityManager->expects($this->once())
            ->method('detach')
            ->with($taxRateSnapshot);

        $this->invoiceTaxRateSnapshotFactory->expects($this->never())
            ->method('create');

        $lineItem->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($existingSnapshot);

        $this->taxRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignHandlesChangedTaxRateCode(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $newSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $lineItems = new ArrayCollection([$lineItem]);

        $this->invoice->expects($this->once())
            ->method('getLineItems')
            ->willReturn($lineItems);

        $lineItem->method('getId')->willReturn(42);
        $lineItem->method('getTaxRate')->willReturn($taxRate);
        $lineItem->method('getTaxRateSnapshot')->willReturn($taxRateSnapshot);

        $taxRate->method('getCode')->willReturn('VAT23');
        $taxRateSnapshot->method('getCode')->willReturn('VAT8');

        $taxRate->method('getName')->willReturn('VAT 23%');
        $taxRate->method('getAmount')->willReturn(0.23);

        $expectedData = [
            'code' => 'VAT23',
            'name' => 'VAT 23%',
            'amount' => 0.23,
        ];

        $this->taxRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with($expectedData)
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('detach')
            ->with($taxRateSnapshot);

        $this->invoiceTaxRateSnapshotFactory->expects($this->once())
            ->method('create')
            ->with('VAT23', 'VAT 23%', 0.23)
            ->willReturn($newSnapshot);

        $lineItem->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($newSnapshot);

        $this->taxRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignHandlesChangedNameOrAmount(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $newSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $lineItems = new ArrayCollection([$lineItem]);

        $this->invoice->expects($this->once())
            ->method('getLineItems')
            ->willReturn($lineItems);

        $lineItem->method('getId')->willReturn(42);
        $lineItem->method('getTaxRate')->willReturn($taxRate);
        $lineItem->method('getTaxRateSnapshot')->willReturn($taxRateSnapshot);

        $taxRate->method('getCode')->willReturn('VAT');
        $taxRateSnapshot->method('getCode')->willReturn('VAT');

        $taxRateSnapshot->method('isNameChanged')->willReturn(true);
        $taxRateSnapshot->method('isAmountChanged')->willReturn(false);

        $taxRateSnapshot->method('getName')->willReturn('Value Added Tax');
        $taxRateSnapshot->method('getAmount')->willReturn(0.23);

        $expectedData = [
            'code' => 'VAT',
            'name' => 'Value Added Tax',
            'amount' => 0.23,
        ];

        $this->taxRateSnapshotRepository->expects($this->once())
            ->method('findOneBy')
            ->with($expectedData)
            ->willReturn(null);

        $this->entityManager->expects($this->once())
            ->method('detach')
            ->with($taxRateSnapshot);

        $this->invoiceTaxRateSnapshotFactory->expects($this->once())
            ->method('create')
            ->with('VAT', 'Value Added Tax', 0.23)
            ->willReturn($newSnapshot);

        $lineItem->expects($this->once())
            ->method('setTaxRateSnapshot')
            ->with($newSnapshot);

        $this->taxRateSnapshotAssigner->assign($this->invoice);
    }

    public function testAssignDoesNothingWhenNoChanges(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $lineItems = new ArrayCollection([$lineItem]);

        $this->invoice->expects($this->once())
            ->method('getLineItems')
            ->willReturn($lineItems);

        $lineItem->method('getId')->willReturn(42);
        $lineItem->method('getTaxRate')->willReturn($taxRate);
        $lineItem->method('getTaxRateSnapshot')->willReturn($taxRateSnapshot);

        $taxRate->method('getCode')->willReturn('VAT');
        $taxRateSnapshot->method('getCode')->willReturn('VAT');

        $taxRateSnapshot->method('isNameChanged')->willReturn(false);
        $taxRateSnapshot->method('isAmountChanged')->willReturn(false);

        $this->taxRateSnapshotRepository->expects($this->never())->method('findOneBy');
        $this->entityManager->expects($this->never())->method('detach');
        $this->invoiceTaxRateSnapshotFactory->expects($this->never())->method('create');
        $lineItem->expects($this->never())->method('setTaxRateSnapshot');

        $this->taxRateSnapshotAssigner->assign($this->invoice);
    }
}