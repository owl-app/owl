<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PermissionRepositoryInterface extends RepositoryInterface
{
    /**
     * @return string[] List of permission names
     */
    public function findAllNames(): array;

    /**
     * @return QueryBuilder|null
     */
    public function findOneByName(string $name): ?QueryBuilder;
}