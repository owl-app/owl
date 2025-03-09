<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of AdminUserInterface
 *
 * @extends RepositoryInterface<T>
 */
interface AdminUserRepositoryInterface extends RepositoryInterface
{
    public function findEnabledWithOwner(?int $userId): QueryBuilder;
}
