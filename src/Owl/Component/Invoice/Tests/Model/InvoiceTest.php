<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use Doctrine\Common\Collections\Collection;
use Owl\Component\Invoice\Enum\CalculateValuesFromEnum;
use Owl\Component\Invoice\Model\Buyer\BuyerInterface;
use Owl\Component\Invoice\Model\Currency\ExchangeRateSnapshotInterface;
use Owl\Component\Invoice\Model\Seller\SellerInterface;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoice = new Invoice();
    }

    public function testShouldImplementInvoiceInterface(): void
    {
        self::assertInstanceOf(InvoiceInterface::class, $this->invoice);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->invoice->getId());
    }

    public function testShouldHaveNoSellerByDefault(): void
    {
        self::assertNull($this->invoice->getSeller());
    }

    public function testSellerShouldBeMutable(): void
    {
        $seller = $this->createMock(SellerInterface::class);
        $seller->method('getId')->willReturn(123);

        $this->invoice->setSeller($seller);
        self::assertSame($seller, $this->invoice->getSeller());
        self::assertTrue($this->invoice->isSellerChanged());
    }

    public function testSellerChangedFlagShouldBeSetWhenSellerChanges(): void
    {
        $seller1 = $this->createMock(SellerInterface::class);
        $seller1->method('getId')->willReturn(123);

        $seller2 = $this->createMock(SellerInterface::class);
        $seller2->method('getId')->willReturn(456);

        $this->invoice->setSeller($seller1);
        self::assertTrue($this->invoice->isSellerChanged());

        // Reset flag for testing (not a public method, but we're testing the behavior)
        $reflection = new \ReflectionProperty(Invoice::class, 'isSellerChanged');
        $reflection->setAccessible(true);
        $reflection->setValue($this->invoice, false);

        // Setting the same seller should not mark as changed
        $this->invoice->setSeller($seller1);
        self::assertFalse($this->invoice->isSellerChanged());

        // Setting a different seller should mark as changed
        $this->invoice->setSeller($seller2);
        self::assertTrue($this->invoice->isSellerChanged());
    }

    public function testShouldHaveNoBuyerByDefault(): void
    {
        self::assertNull($this->invoice->getBuyer());
    }

    public function testBuyerShouldBeMutable(): void
    {
        $buyer = $this->createMock(BuyerInterface::class);
        $buyer->method('getId')->willReturn(123);

        $this->invoice->setBuyer($buyer);
        self::assertSame($buyer, $this->invoice->getBuyer());
        self::assertTrue($this->invoice->isBuyerChanged());
    }

    public function testBuyerChangedFlagShouldBeSetWhenBuyerChanges(): void
    {
        $buyer1 = $this->createMock(BuyerInterface::class);
        $buyer1->method('getId')->willReturn(123);

        $buyer2 = $this->createMock(BuyerInterface::class);
        $buyer2->method('getId')->willReturn(456);

        $this->invoice->setBuyer($buyer1);
        self::assertTrue($this->invoice->isBuyerChanged());

        // Reset flag for testing
        $reflection = new \ReflectionProperty(Invoice::class, 'isBuyerChanged');
        $reflection->setAccessible(true);
        $reflection->setValue($this->invoice, false);

        // Setting the same buyer should not mark as changed
        $this->invoice->setBuyer($buyer1);
        self::assertFalse($this->invoice->isBuyerChanged());

        // Setting a different buyer should mark as changed
        $this->invoice->setBuyer($buyer2);
        self::assertTrue($this->invoice->isBuyerChanged());
    }

    public function testShouldHaveNoSequenceNumberByDefault(): void
    {
        self::assertNull($this->invoice->getSequenceNumber());
    }

    public function testSequenceNumberShouldBeMutable(): void
    {
        $this->invoice->setSequenceNumber(123);
        self::assertSame(123, $this->invoice->getSequenceNumber());

        $this->invoice->setSequenceNumber(null);
        self::assertNull($this->invoice->getSequenceNumber());
    }

    public function testShouldHaveNoFullNumberByDefault(): void
    {
        self::assertNull($this->invoice->getFullNumber());
    }

    public function testFullNumberShouldBeMutable(): void
    {
        $this->invoice->setFullNumber('INV/2025/05/001');
        self::assertSame('INV/2025/05/001', $this->invoice->getFullNumber());

        $this->invoice->setFullNumber(null);
        self::assertNull($this->invoice->getFullNumber());
    }

    public function testShouldHaveNoTypeByDefault(): void
    {
        self::assertNull($this->invoice->getType());
    }

    public function testTypeShouldBeMutable(): void
    {
        $this->invoice->setType('standard');
        self::assertSame('standard', $this->invoice->getType());

        $this->invoice->setType(null);
        self::assertNull($this->invoice->getType());
    }

    public function testShouldHaveNoIssueDateByDefault(): void
    {
        self::assertNull($this->invoice->getIssueDate());
    }

    public function testIssueDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoice->setIssueDate($date);
        self::assertSame($date, $this->invoice->getIssueDate());

        $this->invoice->setIssueDate(null);
        self::assertNull($this->invoice->getIssueDate());
    }

    public function testShouldHaveNoTransactionDateByDefault(): void
    {
        $this->expectException(\Error::class); // Null return type not allowed
        $this->invoice->getTransactionDate();
    }

    public function testTransactionDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoice->setTransactionDate($date);
        self::assertSame($date, $this->invoice->getTransactionDate());
    }

    public function testShouldHaveNoDuePaymentDateByDefault(): void
    {
        $this->expectException(\Error::class); // Null return type not allowed
        $this->invoice->getDuePaymentDate();
    }

    public function testDuePaymentDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoice->setDuePaymentDate($date);
        self::assertSame($date, $this->invoice->getDuePaymentDate());
    }

    public function testShouldHaveNoPaymentDateByDefault(): void
    {
        self::assertNull($this->invoice->getPaymentDate());
    }

    public function testPaymentDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoice->setPaymentDate($date);
        self::assertSame($date, $this->invoice->getPaymentDate());

        $this->invoice->setPaymentDate(null);
        self::assertNull($this->invoice->getPaymentDate());
    }

    public function testShouldHaveNoPaymentMethodByDefault(): void
    {
        self::assertNull($this->invoice->getPaymentMethod());
    }

    public function testPaymentMethodShouldBeMutable(): void
    {
        $this->invoice->setPaymentMethod('bank_transfer');
        self::assertSame('bank_transfer', $this->invoice->getPaymentMethod());

        $this->invoice->setPaymentMethod(null);
        self::assertNull($this->invoice->getPaymentMethod());
    }

    public function testShouldBeNotPaidByDefault(): void
    {
        self::assertFalse($this->invoice->isPaid());
    }

    public function testIsPaidShouldBeMutable(): void
    {
        $this->invoice->setIsPaid(true);
        self::assertTrue($this->invoice->isPaid());

        $this->invoice->setIsPaid(false);
        self::assertFalse($this->invoice->isPaid());
    }

    public function testShouldHaveFromNetCalculateValuesFromByDefault(): void
    {
        self::assertSame(CalculateValuesFromEnum::FROM_NET->value, $this->invoice->getCalculateValuesFrom());
    }

    public function testCalculateValuesFromShouldBeMutableAndUpdateLineItems(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $lineItem->expects(self::once())
            ->method('setCalculateValuesFrom')
            ->with(CalculateValuesFromEnum::FROM_GROSS->value);

        $this->invoice->setCalculateValuesFrom(CalculateValuesFromEnum::FROM_GROSS->value);
        $this->invoice->addLineItem($lineItem);

        self::assertSame(CalculateValuesFromEnum::FROM_GROSS->value, $this->invoice->getCalculateValuesFrom());
    }

    public function testSerieShouldBeMutable(): void
    {
        $serie = $this->createMock(InvoiceSerieInterface::class);
        $this->invoice->setSerie($serie);
        self::assertSame($serie, $this->invoice->getSerie());
    }

    public function testShouldHaveNoExchangeRateSnapshotByDefault(): void
    {
        self::assertNull($this->invoice->getExchangeRateSnapshot());
    }

    public function testExchangeRateSnapshotShouldBeMutable(): void
    {
        $snapshot = $this->createMock(ExchangeRateSnapshotInterface::class);
        $this->invoice->setExchangeRateSnapshot($snapshot);
        self::assertSame($snapshot, $this->invoice->getExchangeRateSnapshot());
    }

    public function testResolveExchangeRateRatioShouldReturnZeroWhenNoSnapshotExists(): void
    {
        self::assertSame(0.0, $this->invoice->resolveExchangeRateRatio());
    }

    public function testResolveExchangeRateRatioShouldReturnRatioFromSnapshot(): void
    {
        $snapshot = $this->createMock(ExchangeRateSnapshotInterface::class);
        $snapshot->method('getRatio')->willReturn(1.25);

        $this->invoice->setExchangeRateSnapshot($snapshot);
        self::assertSame(1.25, $this->invoice->resolveExchangeRateRatio());
    }

    public function testShouldInitializeEmptyLineItemsCollectionByDefault(): void
    {
        self::assertInstanceOf(Collection::class, $this->invoice->getLineItems());
        self::assertCount(0, $this->invoice->getLineItems());
    }

    public function testAddAndRemoveLineItems(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $lineItem->expects(self::once())
            ->method('setInvoice')
            ->with($this->invoice);

        $lineItem->expects(self::once())
            ->method('setCalculateValuesFrom')
            ->with(CalculateValuesFromEnum::FROM_NET->value);

        $this->invoice->addLineItem($lineItem);
        self::assertTrue($this->invoice->hasLineItem($lineItem));
        self::assertCount(1, $this->invoice->getLineItems());

        // Adding the same line item again should do nothing
        $this->invoice->addLineItem($lineItem);
        self::assertCount(1, $this->invoice->getLineItems());

        $this->invoice->removeLineItem($lineItem);
        self::assertFalse($this->invoice->hasLineItem($lineItem));
        self::assertCount(0, $this->invoice->getLineItems());
    }

    public function testClearLineItems(): void
    {
        $lineItem1 = $this->createMock(LineItemInterface::class);
        $lineItem2 = $this->createMock(LineItemInterface::class);

        $this->invoice->addLineItem($lineItem1);
        $this->invoice->addLineItem($lineItem2);
        self::assertCount(2, $this->invoice->getLineItems());

        $this->invoice->clearLineItems();
        self::assertCount(0, $this->invoice->getLineItems());
    }

    public function testTotalsShouldBeZeroByDefault(): void
    {
        self::assertSame(0, $this->invoice->getSubtotal());
        self::assertSame(0, $this->invoice->getTaxTotal());
        self::assertSame(0, $this->invoice->getTotal());
    }

    public function testTotalsCalculation(): void
    {
        // Create line items with specific totals
        $lineItem1 = $this->createMock(LineItemInterface::class);
        $lineItem1->method('getSubtotal')->willReturn(1000);
        $lineItem1->method('getTaxTotal')->willReturn(230);
        $lineItem1->method('getTotal')->willReturn(1230);

        $lineItem2 = $this->createMock(LineItemInterface::class);
        $lineItem2->method('getSubtotal')->willReturn(500);
        $lineItem2->method('getTaxTotal')->willReturn(115);
        $lineItem2->method('getTotal')->willReturn(615);

        $this->invoice->addLineItem($lineItem1);
        $this->invoice->addLineItem($lineItem2);

        // Manually trigger recalculation
        $this->invoice->recalculateLineItemsTotals();

        self::assertSame(1500, $this->invoice->getSubtotal());
        self::assertSame(345, $this->invoice->getTaxTotal());
        self::assertSame(1845, $this->invoice->getTotal());
    }

    public function testRecalculationWithNegativeTotalsShouldReturnZeros(): void
    {
        // Create line items with negative totals
        $lineItem = $this->createMock(LineItemInterface::class);
        $lineItem->method('getSubtotal')->willReturn(-1000);
        $lineItem->method('getTaxTotal')->willReturn(-230);
        $lineItem->method('getTotal')->willReturn(-1230);

        $this->invoice->addLineItem($lineItem);

        // Manually trigger recalculation
        $this->invoice->recalculateLineItemsTotals();

        self::assertSame(0, $this->invoice->getSubtotal());
        self::assertSame(0, $this->invoice->getTaxTotal());
        self::assertSame(0, $this->invoice->getTotal());
    }

    public function testConvertedTotals(): void
    {
        // Set up exchange rate snapshot with ratio
        $snapshot = $this->createMock(ExchangeRateSnapshotInterface::class);
        $snapshot->method('getRatio')->willReturn(1.25);
        $this->invoice->setExchangeRateSnapshot($snapshot);

        // Set up line items
        $lineItem = $this->createMock(LineItemInterface::class);
        $lineItem->method('getSubtotal')->willReturn(1000);
        $lineItem->method('getTaxTotal')->willReturn(230);
        $lineItem->method('getTotal')->willReturn(1230);

        $this->invoice->addLineItem($lineItem);
        $this->invoice->recalculateLineItemsTotals();

        self::assertSame(1250.0, $this->invoice->getSubtotalConverted());
        self::assertSame(287.5, $this->invoice->getTaxTotalConverted());
        self::assertSame(1537.5, $this->invoice->getTotalConverted());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->invoice->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->invoice->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoice->setCreatedAt($date);
        self::assertSame($date, $this->invoice->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoice->setUpdatedAt($date);
        self::assertSame($date, $this->invoice->getUpdatedAt());
    }
}
