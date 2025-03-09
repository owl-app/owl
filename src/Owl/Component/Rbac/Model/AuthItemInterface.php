<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface AuthItemInterface extends ResourceInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getType(): string;

    public function getGroupPermission(): ?string;

    public function setGroupPermission(string $groupPermission): void;

    public function getDescription(): ?string;

    public function setDescription(string $description): void;
}
