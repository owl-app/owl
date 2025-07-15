<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Tests\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Owl\Bundle\InvoiceBundle\EventListener\InvoiceSequenceIncrementListener;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use Owl\Component\Invoice\Model\SequenceInterface;
use Owl\Component\Invoice\Sequention\Strategy\InvoiceSequenceStrategyInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class InvoiceSequenceIncrementListenerTest extends TestCase
{
    private EntityManagerInterface&MockObject $entityManager;
    private ServiceRegistryInterface&MockObject $registryInvoiceSequenceStrategy;
    private InvoiceSequenceIncrementListener $listener;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->registryInvoiceSequenceStrategy = $this->createMock(ServiceRegistryInterface::class);
        $this->listener = new InvoiceSequenceIncrementListener(
            $this->entityManager,
            $this->registryInvoiceSequenceStrategy
        );
    }

    #[Test]
    public function it_updates_sequence_counter_and_persists_when_serie_is_present(): void
    {
        // Arrange
        $sequenceNumber = 123;
        $issueDate = new \DateTime('2024-01-15');
        $sequenceIncrement = 'monthly';

        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie->method('getSequenceIncrement')->willReturn($sequenceIncrement);

        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn($serie);
        $invoice->method('getSequenceNumber')->willReturn($sequenceNumber);
        $invoice->method('getIssueDate')->willReturn($issueDate);

        $invoiceSequence = $this->createMock(SequenceInterface::class);

        $strategy = $this->createMock(InvoiceSequenceStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('updateCounter')
            ->with($serie, $sequenceNumber, $issueDate)
            ->willReturn($invoiceSequence);

        $this->registryInvoiceSequenceStrategy
            ->expects($this->once())
            ->method('get')
            ->with($sequenceIncrement)
            ->willReturn($strategy);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($invoiceSequence);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }

    #[Test]
    public function it_does_not_update_sequence_when_serie_is_null(): void
    {
        // Arrange
        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn(null);

        $this->registryInvoiceSequenceStrategy
            ->expects($this->never())
            ->method('get');

        $this->entityManager
            ->expects($this->never())
            ->method('persist');

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }

    #[Test]
    public function it_throws_exception_when_subject_is_not_invoice_interface(): void
    {
        // Arrange
        $invalidSubject = new \stdClass();
        $event = new GenericEvent($invalidSubject);

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->listener->preCreate($event);
    }

    #[Test]
    public function it_handles_different_sequence_increment_strategies(): void
    {
        // Arrange
        $sequenceNumber = 456;
        $issueDate = new \DateTime('2024-03-20');
        $sequenceIncrement = 'yearly';

        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie->method('getSequenceIncrement')->willReturn($sequenceIncrement);

        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn($serie);
        $invoice->method('getSequenceNumber')->willReturn($sequenceNumber);
        $invoice->method('getIssueDate')->willReturn($issueDate);

        $invoiceSequence = $this->createMock(SequenceInterface::class);

        $strategy = $this->createMock(InvoiceSequenceStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('updateCounter')
            ->with($serie, $sequenceNumber, $issueDate)
            ->willReturn($invoiceSequence);

        $this->registryInvoiceSequenceStrategy
            ->expects($this->once())
            ->method('get')
            ->with($sequenceIncrement)
            ->willReturn($strategy);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($invoiceSequence);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }

    #[Test]
    public function it_handles_different_date_formats(): void
    {
        // Arrange
        $sequenceNumber = 789;
        $issueDate = new \DateTimeImmutable('2024-12-31');
        $sequenceIncrement = 'daily';

        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie->method('getSequenceIncrement')->willReturn($sequenceIncrement);

        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn($serie);
        $invoice->method('getSequenceNumber')->willReturn($sequenceNumber);
        $invoice->method('getIssueDate')->willReturn($issueDate);

        $invoiceSequence = $this->createMock(SequenceInterface::class);

        $strategy = $this->createMock(InvoiceSequenceStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('updateCounter')
            ->with($serie, $sequenceNumber, $issueDate)
            ->willReturn($invoiceSequence);

        $this->registryInvoiceSequenceStrategy
            ->expects($this->once())
            ->method('get')
            ->with($sequenceIncrement)
            ->willReturn($strategy);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($invoiceSequence);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }
}
