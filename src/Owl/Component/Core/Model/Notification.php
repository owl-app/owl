<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Owl\Component\File\Model\FileInterface;
use Owl\Component\Notification\Model\Notification as BaseNotification;
use Owl\Component\User\Model\UserInterface as BaseUserInterface;

class Notification extends BaseNotification implements NotificationInterface
{
    /** @var BaseUserInterface */
    protected $user;

    /**
     * @var Collection<array-key, FileInterface>
     */
    protected $files;

    /** @var string */
    protected $assignedGroup;

    /**
     * @var Collection<array-key, NotificationAcceptedInterface>
     */
    protected $acceptedNotifications;

    public function __construct()
    {
        parent::__construct();

        /** @var ArrayCollection<array-key, FileInterface> $this->files */
        $this->files = new ArrayCollection();

        /** @var ArrayCollection<array-key, NotificationAcceptedInterface> $this->acceptedNotifications */
        $this->acceptedNotifications = new ArrayCollection();
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(FileInterface $file): void
    {
        $this->files->add($file);
    }

    public function removeFile(FileInterface $file): void
    {
        $this->files->removeElement($file);
    }

    /**
     * @return BaseUserInterface
     */
    public function getUser(): ?BaseUserInterface
    {
        return $this->user;
    }

    public function setUser(?BaseUserInterface $user): void
    {
        $this->user = $user;
    }

    public function getAssignedGroup(): string
    {
        return $this->assignedGroup;
    }

    public function setAssignedGroup(string $assignedGroup): void
    {
        $this->assignedGroup = $assignedGroup;
    }

    /**
     * @return Collection<array-key, NotificationAcceptedInterface>
     */
    public function getAcceptedNotifications(): Collection
    {
        return $this->acceptedNotifications;
    }

    public function addAcceptedNotification(NotificationAcceptedInterface $notification): void
    {
        if (!$this->hasAcceptedNotification($notification)) {
            $this->acceptedNotifications->add($notification);
        }
    }

    public function removeAcceptedNotification(NotificationAcceptedInterface $notification): void
    {
        if ($this->hasAcceptedNotification($notification)) {
            $this->acceptedNotifications->removeElement($notification);
        }
    }

    public function hasAcceptedNotification(NotificationAcceptedInterface $notification): bool
    {
        return $this->acceptedNotifications->contains($notification);
    }
}
