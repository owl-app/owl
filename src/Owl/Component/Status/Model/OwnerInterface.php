<?php

declare(strict_types=1);

namespace Owl\Component\Status\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface OwnerInterface extends ResourceInterface
{
    public function getEmail(): ?string;

    public function setEmail(?string $email): void;

    public function getDisplayName(): string;

    public function setDisplayName(string $displayName): void;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): void;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): void;
}
