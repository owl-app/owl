<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model;

use Owl\Component\Core\Model\NotificationAccepted;
use Owl\Component\Core\Model\NotificationAcceptedInterface;
use Owl\Component\User\Model\UserInterface;
use PHPUnit\Framework\TestCase;

final class NotificationAcceptedTest extends TestCase
{
    private NotificationAccepted $notificationAccepted;

    protected function setUp(): void
    {
        $this->notificationAccepted = new NotificationAccepted();
    }

    public function testImplementsNotificationAcceptedInterface(): void
    {
        self::assertInstanceOf(NotificationAcceptedInterface::class, $this->notificationAccepted);
    }

    public function testUserIsMutable(): void
    {
        $user = $this->createMock(UserInterface::class);
        $this->notificationAccepted->setUser($user);
        self::assertSame($user, $this->notificationAccepted->getUser());
    }

    public function testNotificationIsMutable(): void
    {
        $notification = $this->createMock(\Owl\Component\Core\Model\NotificationInterface::class);
        $this->notificationAccepted->setNotification($notification);
        self::assertSame($notification, $this->notificationAccepted->getNotification());
    }

    public function testAcceptedAtIsMutable(): void
    {
        $date = new \DateTimeImmutable('2020-01-01');
        $this->notificationAccepted->setAcceptedAt($date);
        self::assertSame($date, $this->notificationAccepted->getAcceptedAt());
    }
}
