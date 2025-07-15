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

    #[Test]
    public function it_assigns_snapshots_using_all_registered_assigners(): void
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

    #[Test]
    public function it_handles_empty_registry(): void
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

    #[Test]
    public function it_throws_exception_when_subject_is_not_invoice_interface(): void
    {
        // Arrange
        $invalidSubject = new \stdClass();
        $event = new GenericEvent($invalidSubject);

        // Act & Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->listener->assignSnapshot($event);
    }

    #[Test]
    public function it_assigns_snapshots_in_priority_order(): void
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

    #[Test]
    public function it_break_assignment_if_one_assigner_fails(): void
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

    #[Test]
    public function it_calls_assign_on_single_assigner(): void
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
