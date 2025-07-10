<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Core\Repository\NotificationAcceptedRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class NotificationAcceptedRepository extends EntityRepository implements NotificationAcceptedRepositoryInterface
{
    public function findByNotification(int $notificationId): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $queryBuilder
            ->andWhere('o.notification = :notificationId')
            ->setParameter('notificationId', $notificationId)
        ;
    }
}
