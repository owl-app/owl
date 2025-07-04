<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Owl\Component\Core\Model\Rbac\RoleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface RoleRepositoryInterface extends RepositoryInterface
{
    /**
     * @return RoleInterface[]
     */
    public function findWithoutAdminSystem(): array;

    public function findByCanonicalName(string $canonicalName): ?RoleInterface;
}