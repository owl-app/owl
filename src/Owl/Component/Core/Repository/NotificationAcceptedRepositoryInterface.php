<?php

declare(strict_types=1);

namespace Owl\Component\Core\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface NotificationAcceptedRepositoryInterface extends RepositoryInterface
{
    public function findByNotification(int $notificationId): QueryBuilder;
}
