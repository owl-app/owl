<?php

declare(strict_types=1);

namespace Owl\Component\Notification\Model;

/**
 * @template User of NotificationInterface
 */
interface NotificationAwareInterface
{
    /**
     * @return User|null
     */
    public function getNotification();

    /**
     * @param User|null $notification
     */
    public function setNotification($notification): void;
}