<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\Notification;
use Owl\Component\Core\Model\NotificationInterface;
use PHPUnit\Framework\TestCase;

final class NotificationTest extends TestCase
{
    private Notification $notification;

    protected function setUp(): void
    {
        $this->notification = new Notification();
    }

    public function testImplementsNotificationInterface(): void
    {
        self::assertInstanceOf(NotificationInterface::class, $this->notification);
    }

    public function testUserIsMutable(): void
    {
        $user = $this->createMock(\Owl\Component\User\Model\UserInterface::class);
        $this->notification->setUser($user);
        self::assertSame($user, $this->notification->getUser());
    }

    public function testAssignedGroupIsMutable(): void
    {
        $this->notification->setAssignedGroup('ALL');
        self::assertSame('ALL', $this->notification->getAssignedGroup());
    }
} 