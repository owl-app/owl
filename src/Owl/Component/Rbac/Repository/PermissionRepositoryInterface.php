<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Repository;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Rbac\Model\AuthItemInterface;
use Owl\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of PermissionInterface
 *
 * @extends RepositoryInterface<T>
 */
interface PermissionRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array|AuthItemInterface[]
     */
    public function findAllNames(): array;

    public function findOneByName(array ...$name): QueryBuilder;
}
