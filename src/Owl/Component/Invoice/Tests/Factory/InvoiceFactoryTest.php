<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Invoice\Factory;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Owl\Component\Invoice\Factory\InvoiceFactory;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Provider\InvoiceSerieProviderInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;

class InvoiceFactoryTest extends TestCase
{
    /** @var FactoryInterface<InvoiceInterface>&MockObject */
    private MockObject $decoratedFactory;

    private ServiceRegistryInterface&MockObject $registryInvoiceSequenceStrategy;

    private InvoiceNumberGeneratorInterface&MockObject $invoiceNumberGenerator;

    private InvoiceSerieProviderInterface&MockObject $invoiceSerieProvider;

    /** @var InvoiceFactory<InvoiceInterface> */
    private InvoiceFactory $invoiceFactory;

    private InvoiceSequenceStrategyInterface&MockObject $sequenceStrategy;

    private InvoiceInterface&MockObject $invoice;

    private InvoiceSerieInterface&MockObject $invoiceSerie;

    private SequenceInterface&MockObject $invoiceSequence;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->registryInvoiceSequenceStrategy = $this->createMock(ServiceRegistryInterface::class);
        $this->invoiceNumberGenerator = $this->createMock(InvoiceNumberGeneratorInterface::class);
        $this->invoiceSerieProvider = $this->createMock(InvoiceSerieProviderInterface::class);
        $this->sequenceStrategy = $this->createMock(InvoiceSequenceStrategyInterface::class);
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->invoiceSerie = $this->createMock(InvoiceSerieInterface::class);
        $this->invoiceSequence = $this->createMock(SequenceInterface::class);

        $this->invoiceFactory = new InvoiceFactory(
            $this->decoratedFactory,
            $this->registryInvoiceSequenceStrategy,
            $this->invoiceNumberGenerator,
            $this->invoiceSerieProvider
        );
    }

    public function testCreateNew(): void
    {
        $this->decoratedFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($this->invoice);

        $result = $this->invoiceFactory->createNew();

        self::assertSame($this->invoice, $result);
    }

    public function testCreateWithDefaults(): void
    {
        $type = 'invoice';
        $sequenceIncrement = 'yearly_based';
        $counter = 123;

        $this->invoiceSerieProvider->expects(self::once())
            ->method('getSerie')
            ->with($type)
            ->willReturn($this->invoiceSerie);

        $this->invoiceSerie->expects(self::once())
            ->method('getSequenceIncrement')
            ->willReturn($sequenceIncrement);

        $this->registryInvoiceSequenceStrategy->expects(self::once())
            ->method('get')
            ->with($sequenceIncrement)
            ->willReturn($this->sequenceStrategy);

        $this->sequenceStrategy->expects(self::once())
            ->method('getNextCounter')
            ->with($this->invoiceSerie, self::isInstanceOf(DateTimeImmutable::class))
            ->willReturn($this->invoiceSequence);

        $this->invoiceSequence->expects(self::once())
            ->method('getNextCounter')
            ->willReturn($counter);

        $this->decoratedFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($this->invoice);

        $this->invoice->expects(self::once())
            ->method('setSequenceNumber')
            ->with($counter);

        $this->invoice->expects(self::once())
            ->method('setType')
            ->with($type);

        $this->invoice->expects(self::once())
            ->method('setIssueDate')
            ->with(self::isInstanceOf(DateTimeImmutable::class));

        $this->invoice->expects(self::once())
            ->method('setSerie')
            ->with($this->invoiceSerie);

        $result = $this->invoiceFactory->createWithDefaults($type);

        self::assertSame($this->invoice, $result);
    }
}