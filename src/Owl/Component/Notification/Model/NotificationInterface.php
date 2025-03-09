<?php

declare(strict_types=1);

namespace Owl\Component\Notification\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface NotificationInterface extends TimestampableInterface, ResourceInterface
{
    public function getSubject(): string;

    public function setSubject(string $subject): void;

    public function getDescription(): string;

    public function setDescription(string $description): void;

    public function getDateIssue(): ?\DateTimeInterface;

    public function setDateIssue(?\DateTimeInterface $dateIssue): void;

    public function getCurrentFrom(): ?\DateTimeInterface;

    public function setCurrentFrom(?\DateTimeInterface $currentFrom): void;

    public function getCurrentTo(): ?\DateTimeInterface;

    public function setCurrentTo(?\DateTimeInterface $currentTo): void;
}
