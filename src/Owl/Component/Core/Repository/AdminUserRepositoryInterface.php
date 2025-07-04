<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface AdminUserRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int|null $userId
     *
     * @return QueryBuilder
     */
    public function findEnabledWithOwner(?int $userId): QueryBuilder;
}