<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Core\Model\NotificationAcceptedInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of NotificationAcceptedInterface
 *
 * @extends RepositoryInterface<T>
 */
interface NotificationAcceptedRepositoryInterface extends RepositoryInterface
{
    public function findByNotification($notificationId): QueryBuilder;
}
