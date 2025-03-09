<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

interface AuthAssignmentInterface
{
    public function getItemName(): string;

    public function setItemName(?string $itemName): void;
}
