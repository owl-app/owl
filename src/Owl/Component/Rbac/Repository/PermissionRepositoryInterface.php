<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Repository;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Rbac\Model\AuthItemInterface;
use Owl\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PermissionRepositoryInterface extends RepositoryInterface
{
    /**
     * @return array|AuthItemInterface[]
     */
    public function findAllNames(): array;

    /**
     * @return QueryBuilder|null
     */
    public function findOneByName(string $name): ?QueryBuilder;
}