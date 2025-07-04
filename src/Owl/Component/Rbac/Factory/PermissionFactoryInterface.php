<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Factory;

use Owl\Component\Rbac\Model\AuthItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface PermissionFactoryInterface extends FactoryInterface
{
    public function createWithData(
        string $name,
        string $groupPermission,
        string $description,
    ): AuthItemInterface;
}