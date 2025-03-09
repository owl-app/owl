<?php

declare(strict_types=1);

namespace Owl\Component\Status\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface StatusInterface extends TimestampableInterface, ResourceInterface
{
    public function getStatus(): string;

    public function setStatus(string $status): void;

    public function getComment(): ?string;

    public function setComment(?string $comment): void;

    public function getOwner(): ?OwnerInterface;

    public function setOwner(?OwnerInterface $owner): void;

    public function getStatusSubject(): ?StatusableInterface;

    public function setStatusSubject(?StatusableInterface $statusSubject): void;
}
