<?php

declare(strict_types=1);

namespace Owl\Bundle\InvoiceBundle\Tests\EventListener;

use Owl\Bundle\InvoiceBundle\EventListener\InvoiceSnapshotAssignListener;
use Owl\Component\Core\Model\Invoice\InvoiceInterface;
use Owl\Component\Invoice\Assigner\SnapshotAssignerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;

final class InvoiceSnapshotAssignListenerTest extends TestCase
{
    private PrioritizedServiceRegistryInterface&MockObject $registrySnapshotAssigner;
    private InvoiceSnapshotAssignListener $listener;

    protected function setUp(): void
    {
        $this->registrySnapshotAssigner = $this->createMock(PrioritizedServiceRegistryInterface::class);
        $this->listener = new InvoiceSnapshotAssignListener($this->registrySnapshotAssigner);
    }

    public function testAssignsSnapshotsUsingAllRegisteredAssigners(): void
    {
        // Arrange
        $invoice = $this->createMock(InvoiceInterface::class);

        $snapshotAssigner1 = $this->createMock(SnapshotAssignerInterface::class);
        $snapshotAssigner1->expects($this->once())
            ->method('assign')
            ->with($invoice);

        $snapshotAssigner2 = $this->createMock(SnapshotAssignerInterface::class);
        $snapshotAssigner2->expects($this->once())
            ->method('assign')
            ->with($invoice);

        $assigners = [$snapshotAssigner1, $snapshotAssigner2];

        $this->registrySnapshotAssigner
            ->expects($this->once())
            ->method('all')
            ->willReturn($assigners);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->assignSnapshot($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }

    public function testHandlesEmptyRegistry(): void
    {
        // Arrange
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->registrySnapshotAssigner
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->assignSnapshot($event);

        // Assert
        // No exceptions should be thrown when registry is empty
    }

    public function testThrowsExceptionWhenSubjectIsNotInvoiceInterface(): void
    {
        // Arrange
        $invalidSubject = new \stdClass();
        $event = new GenericEvent($invalidSubject);

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->listener->assignSnapshot($event);
    }

    public function testAssignsSnapshotsInPriorityOrder(): void
    {
        // Arrange
        $invoice = $this->createMock(InvoiceInterface::class);
        $callOrder = [];

        $snapshotAssigner1 = $this->createMock(SnapshotAssignerInterface::class);
        $snapshotAssigner1->expects($this->once())
            ->method('assign')
            ->with($invoice)
            ->willReturnCallback(function() use (&$callOrder) {
                $callOrder[] = 'assigner1';
            });

        $snapshotAssigner2 = $this->createMock(SnapshotAssignerInterface::class);
        $snapshotAssigner2->expects($this->once())
            ->method('assign')
            ->with($invoice)
            ->willReturnCallback(function() use (&$callOrder) {
                $callOrder[] = 'assigner2';
            });

        $assigners = [$snapshotAssigner1, $snapshotAssigner2];

        $this->registrySnapshotAssigner
            ->expects($this->once())
            ->method('all')
            ->willReturn($assigners);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->assignSnapshot($event);

        // Assert
        $this->assertSame(['assigner1', 'assigner2'], $callOrder);
    }

    public function testBreakAssignmentIfOneAssignerFails(): void
    {
        // Arrange
        $invoice = $this->createMock(InvoiceInterface::class);

        $snapshotAssigner1 = $this->createMock(SnapshotAssignerInterface::class);
        $snapshotAssigner1->expects($this->once())
            ->method('assign')
            ->with($invoice)
            ->willThrowException(new \RuntimeException('Assigner failed'));

        $snapshotAssigner2 = $this->createMock(SnapshotAssignerInterface::class);
        $snapshotAssigner2->expects($this->never())
            ->method('assign')
            ->with($invoice);

        $assigners = [$snapshotAssigner1, $snapshotAssigner2];

        $this->registrySnapshotAssigner
            ->expects($this->once())
            ->method('all')
            ->willReturn($assigners);

        $event = new GenericEvent($invoice);

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Assigner failed');
        $this->listener->assignSnapshot($event);
    }

    public function testCallsAssignOnSingleAssigner(): void
    {
        // Arrange
        $invoice = $this->createMock(InvoiceInterface::class);

        $snapshotAssigner = $this->createMock(SnapshotAssignerInterface::class);
        $snapshotAssigner->expects($this->once())
            ->method('assign')
            ->with($invoice);

        $assigners = [$snapshotAssigner];

        $this->registrySnapshotAssigner
            ->expects($this->once())
            ->method('all')
            ->willReturn($assigners);

        $event = new GenericEvent($invoice);

        // Act
        $this->listener->assignSnapshot($event);

        // Assert
        // Assertions are covered by the mock expectations above
    }
}
