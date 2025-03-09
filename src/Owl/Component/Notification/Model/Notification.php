<?php

declare(strict_types=1);

namespace Owl\Component\Notification\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class Notification implements NotificationInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var string */
    protected $subject;

    /** @var string */
    protected $description;

    /** @var \DateTimeInterface */
    protected $dateIssue;

    /** @var \DateTimeInterface */
    protected $currentFrom;

    /** @var \DateTimeInterface */
    protected $currentTo;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDateIssue(): ?\DateTimeInterface
    {
        return $this->dateIssue;
    }

    public function setDateIssue(?\DateTimeInterface $dateIssue): void
    {
        $this->dateIssue = $dateIssue;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCurrentFrom(): ?\DateTimeInterface
    {
        return $this->currentFrom;
    }

    public function setCurrentFrom(?\DateTimeInterface $currentFrom): void
    {
        $this->currentFrom = $currentFrom;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCurrentTo(): ?\DateTimeInterface
    {
        return $this->currentTo;
    }

    public function setCurrentTo(?\DateTimeInterface $currentTo): void
    {
        $this->currentTo = $currentTo;
    }
}
