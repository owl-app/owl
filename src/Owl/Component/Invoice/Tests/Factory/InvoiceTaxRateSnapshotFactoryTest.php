<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Factory;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Resource\Exception\UnsupportedMethodException;
use Sylius\Resource\Factory\FactoryInterface;
use Owl\Component\Invoice\Factory\InvoiceTaxRateSnapshotFactory;
use Owl\Component\Invoice\Model\Taxation\TaxRateSnapshotInterface;

class InvoiceTaxRateSnapshotFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private InvoiceTaxRateSnapshotFactory $factory;

    private TaxRateSnapshotInterface&MockObject $snapshot;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->snapshot = $this->createMock(TaxRateSnapshotInterface::class);
        $this->factory = new InvoiceTaxRateSnapshotFactory($this->decoratedFactory);
    }

    public function testCreateNewThrowsException(): void
    {
        $this->expectException(UnsupportedMethodException::class);
        $this->expectExceptionMessage('createNew');
        
        $this->factory->createNew();
    }

    public function testCreate(): void
    {
        $code = 'VAT';
        $name = 'Value Added Tax';
        $amount = 0.23;

        $this->decoratedFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($this->snapshot);

        $this->snapshot->expects(self::once())
            ->method('setCode')
            ->with($code);
        
        $this->snapshot->expects(self::once())
            ->method('setName')
            ->with($name);
        
        $this->snapshot->expects(self::once())
            ->method('setAmount')
            ->with($amount);

        $result = $this->factory->create($code, $name, $amount);
        
        self::assertSame($this->snapshot, $result);
    }
}