<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Factory;

use Owl\Component\Core\Factory\NotificationFactory;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\NotificationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class NotificationFactoryTest extends TestCase
{
    private NotificationFactory $notificationFactory;

    private FactoryInterface&MockObject $defaultFactory;

    private NotificationInterface&MockObject $notification;

    private AdminUserInterface&MockObject $adminUser;

    protected function setUp(): void
    {
        $this->defaultFactory = $this->createMock(FactoryInterface::class);
        $this->notification = $this->createMock(NotificationInterface::class);
        $this->adminUser = $this->createMock(AdminUserInterface::class);

        $this->defaultFactory->method('createNew')->willReturn($this->notification);

        $this->notificationFactory = new NotificationFactory($this->defaultFactory);
    }

    public function testCreateNew(): void
    {
        $this->defaultFactory->expects($this->once())->method('createNew');

        $result = $this->notificationFactory->createNew();

        $this->assertSame($this->notification, $result);
    }

    public function testCreateAction(): void
    {
        $status = 'new';

        $this->defaultFactory->expects($this->once())->method('createNew');
        $this->notification->expects($this->once())->method('setUser')->with($this->adminUser);

        $result = $this->notificationFactory->createAction($status, $this->adminUser);

        $this->assertSame($this->notification, $result);
    }

    public function testCreateActionWithEmptyStatus(): void
    {
        $status = '';

        $this->defaultFactory->expects($this->once())->method('createNew');
        $this->notification->expects($this->once())->method('setUser')->with($this->adminUser);

        $result = $this->notificationFactory->createAction($status, $this->adminUser);

        $this->assertSame($this->notification, $result);
    }
}