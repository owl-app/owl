<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model;

use Owl\Component\Core\Model\Rbac\RoleInterface;

interface RoleAwareInterface
{
    public const ROLE_ADMIN_SYSTEM_NAME = 'ROLE_ADMIN_SYSTEM';

    public const ROLE_USER_NAME = 'ROLE_USER';

    public function getRole(): ?RoleInterface;

    public function setRole(?RoleInterface $role): void;
}
