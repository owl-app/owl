<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Owl\Component\Core\Model\Rbac\RoleInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use Owl\Component\Core\Repository\RoleRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of RoleInterface
 *
 * @implements RoleRepositoryInterface<T>
 */
class RoleRepository extends EntityRepository implements RoleRepositoryInterface
{
    public function findWithoutAdminSystem(): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name != :name')
            ->setParameter('name', RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCanonicalName(string $canonicalName): RoleInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name = :name')
            ->setParameter('name', $canonicalName)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
