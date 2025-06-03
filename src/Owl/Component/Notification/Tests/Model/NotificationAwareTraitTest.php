<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Owl\Component\Notification\Model;

use Owl\Component\Notification\Model\NotificationAwareTrait;
use Owl\Component\Notification\Model\NotificationInterface;
use PHPUnit\Framework\TestCase;

final class NotificationAwareTraitTest extends TestCase
{
    private NotificationAwareObject $notificationAwareObject;

    protected function setUp(): void
    {
        $this->notificationAwareObject = new NotificationAwareObject();
    }

    public function testHasNoNotificationByDefault(): void
    {
        self::assertNull($this->notificationAwareObject->getNotification());
    }

    public function testNotificationIsMutable(): void
    {
        $notification = $this->createMock(NotificationInterface::class);
        $this->notificationAwareObject->setNotification($notification);
        self::assertSame($notification, $this->notificationAwareObject->getNotification());
    }

    public function testNotificationCanBeUnset(): void
    {
        $notification = $this->createMock(NotificationInterface::class);
        $this->notificationAwareObject->setNotification($notification);
        $this->notificationAwareObject->setNotification(null);
        self::assertNull($this->notificationAwareObject->getNotification());
    }
}

class NotificationAwareObject
{
    use NotificationAwareTrait;
} 