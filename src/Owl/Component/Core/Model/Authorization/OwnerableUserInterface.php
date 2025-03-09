<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Authorization;

use Owl\Component\User\Model\UserInterface as BaseUserInterface;

interface OwnerableUserInterface
{
    public function getUser(): ?BaseUserInterface;
}
