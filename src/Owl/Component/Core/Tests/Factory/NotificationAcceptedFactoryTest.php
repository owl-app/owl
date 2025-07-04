<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Factory;

use Owl\Component\Core\Factory\NotificationAcceptedFactory;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\NotificationAcceptedInterface;
use Owl\Component\Core\Model\NotificationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class NotificationAcceptedFactoryTest extends TestCase
{
    private NotificationAcceptedFactory $notificationAcceptedFactory;

    private FactoryInterface&MockObject $defaultFactory;

    private NotificationAcceptedInterface&MockObject $notificationAccepted;

    private NotificationInterface&MockObject $notification;

    private AdminUserInterface&MockObject $adminUser;

    protected function setUp(): void
    {
        $this->defaultFactory = $this->createMock(FactoryInterface::class);
        $this->notificationAccepted = $this->createMock(NotificationAcceptedInterface::class);
        $this->notification = $this->createMock(NotificationInterface::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);

        $this->defaultFactory->method('createNew')->willReturn($this->notificationAccepted);

        $this->notificationAcceptedFactory = new NotificationAcceptedFactory($this->defaultFactory);
    }

    public function testCreateNew(): void
    {
        $this->defaultFactory->expects($this->once())->method('createNew');

        $result = $this->notificationAcceptedFactory->createNew();

        $this->assertSame($this->notificationAccepted, $result);
    }

    public function testCreateAction(): void
    {
        $this->defaultFactory->expects($this->once())->method('createNew');
        $this->notificationAccepted->expects($this->once())->method('setNotification')->with($this->notification);
        $this->notificationAccepted->expects($this->once())->method('setUser')->with($this->adminUser);

        $result = $this->notificationAcceptedFactory->createAction($this->notification, $this->adminUser);

        $this->assertSame($this->notificationAccepted, $result);
    }
}
