<?php

declare(strict_types=1);

namespace Owl\Component\Notification\Model;

trait NotificationAwareTrait
{
    protected ?NotificationInterface $notification = null;

    public function getNotification(): ?NotificationInterface
    {
        return $this->notification;
    }

    public function setNotification(?NotificationInterface $notification): void
    {
        $this->notification = $notification;
    }
} 