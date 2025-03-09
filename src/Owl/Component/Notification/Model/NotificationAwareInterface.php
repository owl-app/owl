<?php

declare(strict_types=1);

namespace Owl\Component\Notification\Model;

interface NotificationAwareInterface
{
    public function getNotification(): ?NotificationInterface;

    public function setNotification(?NotificationInterface $notification);
}
