<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Sequention\Strategy;

use DateTimeImmutable;
use Owl\Component\Invoice\Factory\InvoiceSequenceFactoryInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Sequention\Strategy\YearlyInvoiceSequenceStrategy;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class YearlyInvoiceSequenceStrategyTest extends TestCase
{
    private YearlyInvoiceSequenceStrategy $strategy;

    private RepositoryInterface&MockObject $sequenceRepository;

    private InvoiceSequenceFactoryInterface&MockObject $invoiceSequenceFactory;

    private InvoiceSerieInterface&MockObject $invoiceSerie;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sequenceRepository = $this->createMock(RepositoryInterface::class);
        $this->invoiceSequenceFactory = $this->createMock(InvoiceSequenceFactoryInterface::class);
        $this->invoiceSerie = $this->createMock(InvoiceSerieInterface::class);

        $this->strategy = new YearlyInvoiceSequenceStrategy(
            $this->sequenceRepository,
            $this->invoiceSequenceFactory,
        );
    }

    public function testGetNextCounterCreatesNewSequenceWhenNoneExists(): void
    {
        // Arrange
        $date = new DateTimeImmutable('2023-05-15');
        $year = 2023;
        $expectedSequence = $this->createMock(SequenceInterface::class);

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([
                'year' => $year,
                'serie' => $this->invoiceSerie,
            ])
            ->willReturn(null);

        $this->invoiceSequenceFactory->expects(self::once())
            ->method('create')
            ->with($this->invoiceSerie, $year)
            ->willReturn($expectedSequence);

        $result = $this->strategy->getNextCounter($this->invoiceSerie, $date);

        self::assertSame($expectedSequence, $result);
    }

    public function testGetNextCounterReturnsExistingSequence(): void
    {
        $date = new DateTimeImmutable('2023-05-15');
        $year = 2023;
        $existingSequence = $this->createMock(SequenceInterface::class);

        $this->sequenceRepository->expects(self::once())
            ->method('findOneBy')
            ->with([
                'year' => $year,
                'serie' => $this->invoiceSerie,
            ])
            ->willReturn($existingSequence);

        $this->invoiceSequenceFactory->expects(self::never())
            ->method('create');

        $result = $this->strategy->getNextCounter($this->invoiceSerie, $date);

        self::assertSame($existingSequence, $result);
    }
}
