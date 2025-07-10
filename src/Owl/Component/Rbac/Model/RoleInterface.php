<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

use Doctrine\Common\Collections\Collection;

interface RoleInterface extends AuthItemInterface
{
    /**
     * @return Collection<int, PermissionInterface>
     */
    public function getPermissions(): Collection;

    public function hasPermissions(): bool;

    public function addPermission(PermissionInterface $permission): void;

    public function removePermission(PermissionInterface $permission): void;

    public function hasPermission(PermissionInterface $permission): bool;
}
