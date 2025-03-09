<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

interface RoleAwareInterface
{
    public function getRole(): ?RoleInterface;

    public function setRole(?RoleInterface $role);
}
