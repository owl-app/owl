<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Factory;

use Owl\Component\Invoice\Factory\InvoiceSequenceFactory;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;

class InvoiceSequenceFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private InvoiceSequenceFactory $factory;

    private SequenceInterface&MockObject $sequence;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->sequence = $this->createMock(SequenceInterface::class);
        $this->factory = new InvoiceSequenceFactory($this->decoratedFactory);
    }

    public function testCreateNewThrowsException(): void
    {
        $this->expectException(UnsupportedMethodException::class);
        $this->expectExceptionMessage('createNew');

        $this->factory->createNew();
    }

    public function testCreateWithAllParams(): void
    {
        $serie = $this->createMock(InvoiceSerieInterface::class);
        $year = 2023;
        $month = 5;
        $nextCounter = 10;

        $this->decoratedFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($this->sequence);

        $this->sequence->expects(self::once())
            ->method('setSerie')
            ->with($serie);

        $this->sequence->expects(self::once())
            ->method('setYear')
            ->with($year);

        $this->sequence->expects(self::once())
            ->method('setMonth')
            ->with($month);

        $this->sequence->expects(self::once())
            ->method('setNextCounter')
            ->with($nextCounter);

        $result = $this->factory->create($serie, $year, $month, $nextCounter);

        self::assertSame($this->sequence, $result);
    }

    public function testCreateWithoutMonthAndNextCounter(): void
    {
        $serie = $this->createMock(InvoiceSerieInterface::class);
        $year = 2023;
        $nextCounter = 1;

        $this->decoratedFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($this->sequence);

        $this->sequence->expects(self::once())
            ->method('setSerie')
            ->with($serie);

        $this->sequence->expects(self::once())
            ->method('setYear')
            ->with($year);

        $this->sequence->expects(self::once())
            ->method('setMonth')
            ->with(null);

        $this->sequence->expects(self::once())
            ->method('setNextCounter')
            ->with($nextCounter);

        $result = $this->factory->create($serie, $year);

        self::assertSame($this->sequence, $result);
    }

    public function testCreateWithDefaultNextCounter(): void
    {
        $serie = $this->createMock(InvoiceSerieInterface::class);
        $year = 2023;
        $month = 5;

        $this->decoratedFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($this->sequence);

        $this->sequence->expects(self::once())
            ->method('setSerie')
            ->with($serie);

        $this->sequence->expects(self::once())
            ->method('setYear')
            ->with($year);

        $this->sequence->expects(self::once())
            ->method('setMonth')
            ->with($month);

        $this->sequence->expects(self::once())
            ->method('setNextCounter')
            ->with(1);

        $result = $this->factory->create($serie, $year, $month);

        self::assertSame($this->sequence, $result);
    }
}