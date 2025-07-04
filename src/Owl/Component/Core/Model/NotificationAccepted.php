<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Notification\Model\NotificationInterface as BaseNotificationInterface;
use Owl\Component\Core\Model\AdminUserInterface;

class NotificationAccepted implements NotificationAcceptedInterface
{
    /** @var mixed */
    protected $id;

    /** @var AdminUserInterface|null * */
    protected $user;

    /** @var NotificationInterface */
    protected $notification;

    /** @var \DateTimeInterface|null */
    protected $acceptedAt;

    public function __construct()
    {
        $this->acceptedAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getNotification()
    {
        return $this->notification;
    }

    public function setNotification($notification): void
    {
        $this->notification = $notification;
    }

    public function getAcceptedAt(): ?\DateTimeInterface
    {
        return $this->acceptedAt;
    }

    public function setAcceptedAt(?\DateTimeInterface $acceptedAt): void
    {
        $this->acceptedAt = $acceptedAt;
    }
}
