<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Owl\Component\Core\Model\Rbac\RoleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of RoleInterface
 *
 * @extends RepositoryInterface<T>
 */
interface RoleRepositoryInterface extends RepositoryInterface
{
    public function findWithoutAdminSystem(): array;

    public function findByCanonicalName(string $canonicalName): RoleInterface;
}
