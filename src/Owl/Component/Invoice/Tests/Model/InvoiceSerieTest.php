<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Model;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Owl\Component\Invoice\Model\InvoiceSerie;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;

class InvoiceSerieTest extends TestCase
{
    private InvoiceSerieInterface $invoiceSerie;

    protected function setUp(): void
    {
        $this->invoiceSerie = new InvoiceSerie();
    }

    public function testShouldImplementInvoiceSerieInterface(): void
    {
        self::assertInstanceOf(InvoiceSerieInterface::class, $this->invoiceSerie);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->invoiceSerie->getId());
    }

    public function testShouldHaveZeroNextCounterByDefault(): void
    {
        self::assertSame(0, $this->invoiceSerie->getNextCounter());
    }

    public function testNextCounterShouldBeMutable(): void
    {
        $this->invoiceSerie->setNextCounter(5);
        self::assertSame(5, $this->invoiceSerie->getNextCounter());

        $this->invoiceSerie->setNextCounter(10);
        self::assertSame(10, $this->invoiceSerie->getNextCounter());
    }

    public function testShouldHaveNoFormatByDefault(): void
    {
        self::assertNull($this->invoiceSerie->getFormat());
    }

    public function testFormatShouldBeMutable(): void
    {
        $this->invoiceSerie->setFormat('INV/{Y}/{M}/{counter}');
        self::assertSame('INV/{Y}/{M}/{counter}', $this->invoiceSerie->getFormat());

        $this->invoiceSerie->setFormat(null);
        self::assertNull($this->invoiceSerie->getFormat());
    }

    public function testShouldHaveNoInvoiceTypeByDefault(): void
    {
        self::assertNull($this->invoiceSerie->getInvoiceType());
    }

    public function testInvoiceTypeShouldBeMutable(): void
    {
        $this->invoiceSerie->setInvoiceType('standard');
        self::assertSame('standard', $this->invoiceSerie->getInvoiceType());

        $this->invoiceSerie->setInvoiceType(null);
        self::assertNull($this->invoiceSerie->getInvoiceType());
    }

    public function testShouldHaveNoSequenceIncrementByDefault(): void
    {
        self::assertNull($this->invoiceSerie->getSequenceIncrement());
    }

    public function testSequenceIncrementShouldBeMutable(): void
    {
        $this->invoiceSerie->setSequenceIncrement('1');
        self::assertSame('1', $this->invoiceSerie->getSequenceIncrement());

        $this->invoiceSerie->setSequenceIncrement(null);
        self::assertNull($this->invoiceSerie->getSequenceIncrement());
    }

    public function testShouldNotBeDefaultByDefault(): void
    {
        self::assertFalse($this->invoiceSerie->getIsDefault());
    }

    public function testIsDefaultShouldBeMutable(): void
    {
        $this->invoiceSerie->setIsDefault(true);
        self::assertTrue($this->invoiceSerie->getIsDefault());

        $this->invoiceSerie->setIsDefault(false);
        self::assertFalse($this->invoiceSerie->getIsDefault());
    }

    public function testShouldInitializeEmptySequencesCollectionByDefault(): void
    {
        self::assertInstanceOf(Collection::class, $this->invoiceSerie->getSequences());
        self::assertCount(0, $this->invoiceSerie->getSequences());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->invoiceSerie->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->invoiceSerie->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoiceSerie->setCreatedAt($date);
        self::assertSame($date, $this->invoiceSerie->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->invoiceSerie->setUpdatedAt($date);
        self::assertSame($date, $this->invoiceSerie->getUpdatedAt());
    }

    public function testSequencesCollectionType(): void
    {
        $sequences = $this->invoiceSerie->getSequences();
        self::assertInstanceOf(ArrayCollection::class, $sequences);
    }
}
