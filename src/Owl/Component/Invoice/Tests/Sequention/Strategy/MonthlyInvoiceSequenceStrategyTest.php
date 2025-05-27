<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Sequention\Strategy;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Owl\Component\Invoice\Factory\InvoiceSequenceFactoryInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Sequention\Strategy\MonthlyInvoiceSequenceStrategy;

class MonthlyInvoiceSequenceStrategyTest extends TestCase
{
    private RepositoryInterface&MockObject $sequenceRepository;

    private InvoiceSequenceFactoryInterface&MockObject $invoiceSequenceFactory;

    private MonthlyInvoiceSequenceStrategy $strategy;

    private InvoiceSerieInterface&MockObject $invoiceSerie;

    protected function setUp(): void
    {
        $this->sequenceRepository = $this->createMock(RepositoryInterface::class);
        $this->invoiceSequenceFactory = $this->createMock(InvoiceSequenceFactoryInterface::class);
        $this->invoiceSerie = $this->createMock(InvoiceSerieInterface::class);

        $this->strategy = new MonthlyInvoiceSequenceStrategy(
            $this->sequenceRepository,
            $this->invoiceSequenceFactory
        );
    }

    public function testGetNextCounterCreatesNewSequenceWhenNoneExists(): void
    {
        $date = new DateTimeImmutable('2023-05-15');
        $year = 2023;
        $month = 5;
        $sequence = $this->createMock(SequenceInterface::class);

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([
                'year' => $year,
                'month' => $month,
                'serie' => $this->invoiceSerie,
            ])
            ->willReturn(null);

        $this->invoiceSequenceFactory->expects(self::once())
            ->method('create')
            ->with($this->invoiceSerie, $year, $month)
            ->willReturn($sequence);

        $result = $this->strategy->getNextCounter($this->invoiceSerie, $date);

        self::assertSame($sequence, $result);
    }

    public function testGetNextCounterReturnsExistingSequence(): void
    {
        $date = new DateTimeImmutable('2023-05-15');
        $year = 2023;
        $month = 5;
        $sequence = $this->createMock(SequenceInterface::class);

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([
                'year' => $year,
                'month' => $month,
                'serie' => $this->invoiceSerie,
            ])
            ->willReturn($sequence);

        $this->invoiceSequenceFactory->expects(self::never())
            ->method('create');

        $result = $this->strategy->getNextCounter($this->invoiceSerie, $date);

        self::assertSame($sequence, $result);
    }
}