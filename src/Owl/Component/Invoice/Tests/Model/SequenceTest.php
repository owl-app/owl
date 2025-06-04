<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Model;

use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\Sequence;
use Owl\Component\Invoice\Model\SequenceInterface;
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    private Sequence $sequence;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sequence = new Sequence();
    }

    public function testShouldImplementSequenceInterface(): void
    {
        self::assertInstanceOf(SequenceInterface::class, $this->sequence);
    }

    public function testShouldHaveNoIdByDefault(): void
    {
        self::assertNull($this->sequence->getId());
    }

    public function testShouldHaveZeroNextCounterByDefault(): void
    {
        self::assertSame(0, $this->sequence->getNextCounter());
    }

    public function testNextCounterShouldBeMutable(): void
    {
        $this->sequence->setNextCounter(5);
        self::assertSame(5, $this->sequence->getNextCounter());

        $this->sequence->setNextCounter(10);
        self::assertSame(10, $this->sequence->getNextCounter());
    }

    public function testShouldIncrementNextCounter(): void
    {
        $this->sequence->setNextCounter(5);
        $this->sequence->incrementNextCounter();
        self::assertSame(6, $this->sequence->getNextCounter());
    }

    public function testYearShouldBeMutable(): void
    {
        $this->sequence->setYear(2023);
        self::assertSame(2023, $this->sequence->getYear());

        $this->sequence->setYear(2024);
        self::assertSame(2024, $this->sequence->getYear());
    }

    public function testShouldHaveNoMonthByDefault(): void
    {
        self::assertNull($this->sequence->getMonth());
    }

    public function testMonthShouldBeMutable(): void
    {
        $this->sequence->setMonth(1);
        self::assertSame(1, $this->sequence->getMonth());

        $this->sequence->setMonth(12);
        self::assertSame(12, $this->sequence->getMonth());

        $this->sequence->setMonth(null);
        self::assertNull($this->sequence->getMonth());
    }

    public function testVersionShouldBeMutable(): void
    {
        $this->sequence->setVersion(1);
        self::assertSame(1, $this->sequence->getVersion());

        $this->sequence->setVersion(2);
        self::assertSame(2, $this->sequence->getVersion());
    }

    public function testSerieShouldBeMutable(): void
    {
        $serie = $this->createMock(InvoiceSerieInterface::class);
        $this->sequence->setSerie($serie);
        self::assertSame($serie, $this->sequence->getSerie());
    }

    public function testShouldInitializeCreationDateByDefault(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->sequence->getCreatedAt());
    }

    public function testShouldHaveNoLastUpdateDateByDefault(): void
    {
        self::assertNull($this->sequence->getUpdatedAt());
    }

    public function testCreationDateShouldBeMutable(): void
    {
        $date = new \DateTime('-1 day');
        $this->sequence->setCreatedAt($date);
        self::assertSame($date, $this->sequence->getCreatedAt());
    }

    public function testLastUpdateDateShouldBeMutable(): void
    {
        $date = new \DateTime();
        $this->sequence->setUpdatedAt($date);
        self::assertSame($date, $this->sequence->getUpdatedAt());
    }
}
