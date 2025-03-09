<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Factory;

use Owl\Component\Rbac\Model\AuthItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of AuthItemInterface
 *
 * @implements PermissionFactoryInterface<T>
 */
class PermissionFactory implements PermissionFactoryInterface
{
    private FactoryInterface $permissionFactory;

    public function __construct(FactoryInterface $permissionFactory)
    {
        $this->permissionFactory = $permissionFactory;
    }

    public function createNew(): AuthItemInterface
    {
        return $this->permissionFactory->createNew();
    }

    public function createWithData(
        string $name,
        string $groupPermission,
        string $description,
    ): AuthItemInterface {
        $permission = $this->createNew();
        $permission->setName($name);
        $permission->setGroupPermission($groupPermission);
        $permission->setDescription($description);

        return $permission;
    }
}
