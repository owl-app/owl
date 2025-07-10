<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Rbac\Repository\PermissionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class PermissionRepository extends EntityRepository implements PermissionRepositoryInterface
{
    /**
     * @return string[] List of permission names
     */
    public function findAllNames(): array
    {
        return $this->createQueryBuilder('o')
            ->select('o.name')
            ->getQuery()
            ->getSingleColumnResult()
        ;
    }

    public function findOneByName(string $name): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.name = :name')
            ->setParameter('name', $name)
        ;
    }
}
