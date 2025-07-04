<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Core\Model\NotificationAcceptedInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface NotificationAcceptedRepositoryInterface extends RepositoryInterface
{
    /**
     * @param int $notificationId
     *
     * @return QueryBuilder
     */
    public function findByNotification(int $notificationId): QueryBuilder;
}