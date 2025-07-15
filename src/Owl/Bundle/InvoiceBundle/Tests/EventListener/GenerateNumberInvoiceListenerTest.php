<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Tests\EventListener;

use Owl\Bundle\InvoiceBundle\EventListener\GenerateNumberInvoiceListener;
use Owl\Component\Invoice\Generator\InvoiceNumberGeneratorInterface;
use Owl\Component\Invoice\Model\InvoiceInterface;
use Owl\Component\Invoice\Model\InvoiceSerieInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class GenerateNumberInvoiceListenerTest extends TestCase
{
    private InvoiceNumberGeneratorInterface&MockObject $invoiceNumberGenerator;
    private GenerateNumberInvoiceListener $listener;

    protected function setUp(): void
    {
        $this->invoiceNumberGenerator = $this->createMock(InvoiceNumberGeneratorInterface::class);
        $this->listener = new GenerateNumberInvoiceListener($this->invoiceNumberGenerator);
    }

    public function testGeneratesFullNumberWhenSerieIsPresent(): void
    {
        // Arrange
        $issueDate = new \DateTime('2024-01-15');
        $sequenceNumber = 123;
        $format = 'INV-{YYYY}-{MM}-{NNNN}';
        $expectedFullNumber = 'INV-2024-01-0123';

        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie->method('getFormat')->willReturn($format);

        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn($serie);
        $invoice->method('getSequenceNumber')->willReturn($sequenceNumber);
        $invoice->method('getIssueDate')->willReturn($issueDate);

        $this->invoiceNumberGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($format, $sequenceNumber, $issueDate)
            ->willReturn($expectedFullNumber);

        $invoice->expects($this->once())
            ->method('setFullNumber')
            ->with($expectedFullNumber);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }

    public function testDoesNotGenerateFullNumberWhenSerieIsNull(): void
    {
        // Arrange
        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn(null);

        $this->invoiceNumberGenerator
            ->expects($this->never())
            ->method('generate');

        $invoice->expects($this->never())
            ->method('setFullNumber');

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }

    public function testThrowsExceptionWhenSubjectIsNotInvoiceInterface(): void
    {
        // Arrange
        $invalidSubject = new \stdClass();
        $event = new GenericEvent($invalidSubject);

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->listener->preCreate($event);
    }

    public function testGeneratesFullNumberWithDifferentSequenceNumbers(): void
    {
        // Arrange
        $issueDate = new \DateTime('2024-03-20');
        $sequenceNumber = 456;
        $format = 'BILL-{YYYY}-{NNNN}';
        $expectedFullNumber = 'BILL-2024-0456';

        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie->method('getFormat')->willReturn($format);

        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn($serie);
        $invoice->method('getSequenceNumber')->willReturn($sequenceNumber);
        $invoice->method('getIssueDate')->willReturn($issueDate);

        $this->invoiceNumberGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($format, $sequenceNumber, $issueDate)
            ->willReturn($expectedFullNumber);

        $invoice->expects($this->once())
            ->method('setFullNumber')
            ->with($expectedFullNumber);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }

    public function testHandlesDifferentDateFormats(): void
    {
        // Arrange
        $issueDate = new \DateTimeImmutable('2024-12-31');
        $sequenceNumber = 999;
        $format = 'FAC-{YYYY}-{NNNN}';
        $expectedFullNumber = 'FAC-2024-0999';

        $serie = $this->createMock(InvoiceSerieInterface::class);
        $serie->method('getFormat')->willReturn($format);

        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getSerie')->willReturn($serie);
        $invoice->method('getSequenceNumber')->willReturn($sequenceNumber);
        $invoice->method('getIssueDate')->willReturn($issueDate);

        $this->invoiceNumberGenerator
            ->expects($this->once())
            ->method('generate')
            ->with($format, $sequenceNumber, $issueDate)
            ->willReturn($expectedFullNumber);

        $invoice->expects($this->once())
            ->method('setFullNumber')
            ->with($expectedFullNumber);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->preCreate($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }
}
