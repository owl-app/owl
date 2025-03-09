<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Notification\Model\NotificationInterface as BaseNotificationInterface;
use Owl\Component\User\Model\UserInterface as BaseUserInterface;

class NotificationAccepted implements NotificationAcceptedInterface
{
    /** @var mixed */
    protected $id;

    /** @var \Owl\Component\Core\Model\AdminUserInterface|null * */
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

    /**
     * @return AdminUserInterface|null
     */
    public function getUser(): ?BaseUserInterface
    {
        return $this->user;
    }

    public function setUser(?BaseUserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return NotificationInterface
     */
    public function getNotification(): ?BaseNotificationInterface
    {
        return $this->notification;
    }

    public function setNotification(?BaseNotificationInterface $notification): void
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
