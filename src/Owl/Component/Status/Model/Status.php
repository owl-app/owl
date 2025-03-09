<?php

declare(strict_types=1);

namespace Owl\Component\Status\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

abstract class Status implements StatusInterface
{
    use TimestampableTrait;

    /** @var int */
    protected $id;

    /** @var string */
    protected $status;

    /** @var string */
    protected $comment;

    /** @var OwnerInterface */
    protected $owner;

    /** @var StatusableInterface|null */
    protected $statusSubject;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return OwnerInterface
     */
    public function getOwner(): ?OwnerInterface
    {
        return $this->owner;
    }

    public function setOwner(?OwnerInterface $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return StatusableInterface
     */
    public function getStatusSubject(): ?StatusableInterface
    {
        return $this->statusSubject;
    }

    public function setStatusSubject(?StatusableInterface $statusSubject): void
    {
        $this->statusSubject = $statusSubject;
    }

    abstract public function getStatusesLabels(): array;
}
