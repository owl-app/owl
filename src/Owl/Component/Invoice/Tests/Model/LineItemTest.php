<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Owl\Component\Invoice\Enum\CalculateValuesFromEnum;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\LineItem;
use Owl\Component\Invoice\Model\LineItemInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateInterface;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;

class LineItemTest extends TestCase
{
    private LineItem $lineItem;
    private InvoiceInterface&MockObject $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->lineItem = new LineItem();

        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->invoice->method('getCalculateValuesFrom')
            ->willReturn(CalculateValuesFromEnum::FROM_NET->value);
    }

    public function testShouldImplementLineItemInterface(): void
    {
        self::assertInstanceOf(LineItemInterface::class, $this->lineItem);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->lineItem->getId());
    }

    public function testShouldHaveNoNameByDefault(): void
    {
        self::assertNull($this->lineItem->getName());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->lineItem->setName('Service X');
        self::assertSame('Service X', $this->lineItem->getName());

        $this->lineItem->setName(null);
        self::assertNull($this->lineItem->getName());
    }

    public function testShouldHaveNoQuantityByDefault(): void
    {
        self::assertNull($this->lineItem->getQuantity());
    }

    public function testQuantityShouldBeMutable(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $this->invoice->expects($this->once())
            ->method('recalculateLineItemsTotals');

        $this->lineItem->setQuantity(5.5);
        self::assertSame(5.5, $this->lineItem->getQuantity());
    }

    public function testQuantityShouldHandleStringConversion(): void
    {
        // Simulate Doctrine hydrating a decimal as string
        $reflection = new \ReflectionProperty($this->lineItem, 'quantity');
        $reflection->setAccessible(true);
        $reflection->setValue($this->lineItem, '3.25');

        self::assertSame(3.25, $this->lineItem->getQuantity());
    }

    public function testShouldHaveNoUnitByDefault(): void
    {
        self::assertNull($this->lineItem->getUnit());
    }

    public function testUnitShouldBeMutable(): void
    {
        $this->lineItem->setUnit(LineItemInterface::UNIT_HOUR);
        self::assertSame(LineItemInterface::UNIT_HOUR, $this->lineItem->getUnit());
    }

    public function testUnitLabelsShouldBeAvailable(): void
    {
        $unitLabels = LineItem::getUnitLabels();
        self::assertArrayHasKey(LineItemInterface::UNIT_HOUR, $unitLabels);
        self::assertArrayHasKey(LineItemInterface::UNIT_PIECE, $unitLabels);
    }

    public function testShouldHaveZeroUnitPriceByDefault(): void
    {
        self::assertSame(0, $this->lineItem->getUnitPrice());
    }

    public function testUnitPriceShouldBeMutable(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $this->invoice->expects($this->once())
            ->method('recalculateLineItemsTotals');

        $this->lineItem->setUnitPrice(1000);
        self::assertSame(1000, $this->lineItem->getUnitPrice());
    }

    public function testShouldHaveZeroSubtotalByDefault(): void
    {
        self::assertSame(0, $this->lineItem->getSubtotal());
    }

    public function testShouldHaveZeroTaxTotalByDefault(): void
    {
        self::assertSame(0, $this->lineItem->getTaxTotal());
    }

    public function testShouldHaveZeroTotalByDefault(): void
    {
        self::assertSame(0, $this->lineItem->getTotal());
    }

    public function testShouldHaveNoInvoiceByDefault(): void
    {
        self::assertNull($this->lineItem->getInvoice());
    }

    public function testInvoiceShouldBeMutable(): void
    {
        $this->lineItem->setInvoice($this->invoice);
        self::assertSame($this->invoice, $this->lineItem->getInvoice());
    }

    public function testShouldHaveNoTaxRateByDefault(): void
    {
        self::assertNull($this->lineItem->getTaxRate());
    }

    public function testTaxRateShouldBeMutable(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $taxRate = $this->createMock(TaxRateInterface::class);

        $this->invoice->expects($this->once())
            ->method('recalculateLineItemsTotals');

        $this->lineItem->setTaxRate($taxRate);
        self::assertSame($taxRate, $this->lineItem->getTaxRate());
    }

    public function testShouldHaveNoTaxRateSnapshotByDefault(): void
    {
        self::assertNull($this->lineItem->getTaxRateSnapshot());
    }

    public function testTaxRateSnapshotShouldBeMutable(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);

        $this->invoice->expects($this->once())
            ->method('recalculateLineItemsTotals');

        $this->lineItem->setTaxRateSnapshot($taxRateSnapshot);
        self::assertSame($taxRateSnapshot, $this->lineItem->getTaxRateSnapshot());
    }

    public function testGetTaxRateAmountShouldReturnNullWhenNoTaxRateOrSnapshot(): void
    {
        self::assertNull($this->lineItem->getTaxRateAmount());
    }

    public function testGetTaxRateAmountShouldReturnTaxRateAmountWhenNoSnapshot(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);

        $this->lineItem->setTaxRate($taxRate);

        self::assertSame(0.23, $this->lineItem->getTaxRateAmount());
    }

    public function testGetTaxRateAmountShouldReturnSnapshotAmountWhenSnapshotExists(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);
        $taxRate->method('getCode')->willReturn('VAT23');

        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $taxRateSnapshot->method('getAmount')->willReturn(0.22);
        $taxRateSnapshot->method('getCode')->willReturn('VAT23');

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setTaxRateSnapshot($taxRateSnapshot);

        self::assertSame(0.22, $this->lineItem->getTaxRateAmount());
    }

    public function testGetTaxRateAmountShouldReturnTaxRateAmountWhenSnapshotCodeDiffers(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);
        $taxRate->method('getCode')->willReturn('VAT23');

        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $taxRateSnapshot->method('getAmount')->willReturn(0.22);
        $taxRateSnapshot->method('getCode')->willReturn('VAT22');

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setTaxRateSnapshot($taxRateSnapshot);

        self::assertSame(0.23, $this->lineItem->getTaxRateAmount());
    }

    public function testShouldNotDetectTaxRateNameDifferentWhenNoSnapshot(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $this->lineItem->setTaxRate($taxRate);

        self::assertFalse($this->lineItem->isTaxRateNameDiffrent());
    }

    public function testShouldDetectTaxRateNameDifferent(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getName')->willReturn('VAT 23%');

        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $taxRateSnapshot->method('getName')->willReturn('VAT 22%');

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setTaxRateSnapshot($taxRateSnapshot);

        self::assertTrue($this->lineItem->isTaxRateNameDiffrent());
    }

    public function testShouldNotDetectTaxRateNameDifferentWhenNamesMatch(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getName')->willReturn('VAT 23%');

        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $taxRateSnapshot->method('getName')->willReturn('VAT 23%');

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setTaxRateSnapshot($taxRateSnapshot);

        self::assertFalse($this->lineItem->isTaxRateNameDiffrent());
    }

    public function testShouldNotDetectTaxRateAmountDifferentWhenNoSnapshot(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $this->lineItem->setTaxRate($taxRate);

        self::assertFalse($this->lineItem->isTaxRateAmountDiffrent());
    }

    public function testShouldDetectTaxRateAmountDifferent(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);

        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $taxRateSnapshot->method('getAmount')->willReturn(0.22);

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setTaxRateSnapshot($taxRateSnapshot);

        self::assertTrue($this->lineItem->isTaxRateAmountDiffrent());
    }

    public function testShouldNotDetectTaxRateAmountDifferentWhenAmountsMatch(): void
    {
        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);

        $taxRateSnapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $taxRateSnapshot->method('getAmount')->willReturn(0.23);

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setTaxRateSnapshot($taxRateSnapshot);

        self::assertFalse($this->lineItem->isTaxRateAmountDiffrent());
    }

    public function testCalculateValuesFromShouldBeMutable(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $this->invoice->expects($this->once())
            ->method('recalculateLineItemsTotals');

        $this->lineItem->setCalculateValuesFrom(CalculateValuesFromEnum::FROM_GROSS->value);
        self::assertSame(CalculateValuesFromEnum::FROM_GROSS->value, $this->lineItem->getCalculateValuesFrom());
    }

    public function testGetTotalPriceShouldCalculateFromUnitPriceAndQuantity(): void
    {
        $this->lineItem->setUnitPrice(1000);
        $this->lineItem->setQuantity(2.5);

        // Expected: 1000 * 2.5 = 2500
        self::assertSame(2500, $this->lineItem->getTotalPrice());
    }

    public function testRecalculateTotalsFromNetPrice(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setUnitPrice(1000);
        $this->lineItem->setQuantity(2);
        $this->lineItem->setCalculateValuesFrom(CalculateValuesFromEnum::FROM_NET->value);

        // Unit price: 1000, Quantity: 2
        // Subtotal: 1000 * 2 = 2000
        // Tax total: 2000 * 0.23 = 460
        // Total: 2000 + 460 = 2460
        self::assertSame(2000, $this->lineItem->getSubtotal());
        self::assertSame(460, $this->lineItem->getTaxTotal());
        self::assertSame(2460, $this->lineItem->getTotal());
    }

    public function testRecalculateTotalsFromGrossPrice(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setUnitPrice(1230);
        $this->lineItem->setQuantity(2);
        $this->lineItem->setCalculateValuesFrom(CalculateValuesFromEnum::FROM_GROSS->value);

        // Unit price: 1230, Quantity: 2
        // Total price: 1230 * 2 = 2460
        // Subtotal: 2460 / 1.23 = 2000 (rounded)
        // Tax total: 2460 - 2000 = 460
        // Total: 2000 + 460 = 2460
        self::assertSame(2000, $this->lineItem->getSubtotal());
        self::assertSame(460, $this->lineItem->getTaxTotal());
        self::assertSame(2460, $this->lineItem->getTotal());
    }

    public function testRecalculateTotalsWithNegativeValuesShouldClampToZero(): void
    {
        $this->lineItem->setInvoice($this->invoice);

        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);

        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setUnitPrice(-1000);
        $this->lineItem->setQuantity(2);
        $this->lineItem->setCalculateValuesFrom(CalculateValuesFromEnum::FROM_NET->value);

        // Even though calculations would yield negative values,
        // the total should be clamped to 0
        self::assertSame(-2000, $this->lineItem->getSubtotal());
        self::assertSame(-460, $this->lineItem->getTaxTotal());
        self::assertSame(0, $this->lineItem->getTotal());
    }

    public function testRecalculateTotalsShouldUseInvoiceCalculateValuesFromWhenNotSet(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getCalculateValuesFrom')
            ->willReturn(CalculateValuesFromEnum::FROM_NET->value);
        $invoice->expects($this->exactly(3))
            ->method('recalculateLineItemsTotals');

        $taxRate = $this->createMock(TaxRateInterface::class);
        $taxRate->method('getAmount')->willReturn(0.23);

        $this->lineItem->setInvoice($invoice);
        $this->lineItem->setTaxRate($taxRate);
        $this->lineItem->setUnitPrice(1000);
        $this->lineItem->setQuantity(2);

        // Should use invoice's calculate values from setting
        self::assertSame(2000, $this->lineItem->getSubtotal());
        self::assertSame(460, $this->lineItem->getTaxTotal());
        self::assertSame(2460, $this->lineItem->getTotal());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->lineItem->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->lineItem->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('-1 day');
        $this->lineItem->setCreatedAt($date);
        self::assertSame($date, $this->lineItem->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->lineItem->setUpdatedAt($date);
        self::assertSame($date, $this->lineItem->getUpdatedAt());
    }
}
