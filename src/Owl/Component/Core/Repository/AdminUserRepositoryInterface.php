<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface AdminUserRepositoryInterface extends RepositoryInterface
{
    public function findEnabledWithOwner(?int $userId): QueryBuilder;
}
