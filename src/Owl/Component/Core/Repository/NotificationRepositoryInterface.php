<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\NotificationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of NotificationInterface
 *
 * @extends RepositoryInterface<T>
 */
interface NotificationRepositoryInterface extends RepositoryInterface
{
    public function findAllNotAccepted(AdminUserInterface $user, string $groupAssigned): array;

    public function findNotAccepted(int $id, AdminUserInterface $user, string $groupAssigned): ?NotificationInterface;
}
