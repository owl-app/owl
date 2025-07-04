<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;
use Owl\Component\Rbac\Model\PermissionInterface;

class Role extends AuthItem implements RoleInterface
{
    /**
     * @return 'role'
     */
    public function getType(): string
    {
        return 'role';
    }

    /**
     * @var Collection<int, PermissionInterface>
     */
    protected Collection $permissions;

    public function __construct()
    {
        parent::__construct();
        $this->permissions = new ArrayCollection();
    }

    /**
     * @return Collection<int, PermissionInterface>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function hasPermissions(): bool
    {
        return !$this->permissions->isEmpty();
    }

    public function addPermission(PermissionInterface $permission): void
    {
        Assert::isInstanceOf($permission, PermissionInterface::class);

        if (!$this->hasPermission($permission)) {
            $this->permissions->add($permission);
        }
    }

    public function removePermission(PermissionInterface $permission): void
    {
        Assert::isInstanceOf($permission, PermissionInterface::class);

        if ($this->hasPermission($permission)) {
            $this->permissions->removeElement($permission);
        }
    }

    public function hasPermission(PermissionInterface $permission): bool
    {
        return $this->permissions->contains($permission);
    }
}