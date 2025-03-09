<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Owl\Bundle\UserBundle\Doctrine\ORM\UserRepository;
use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use Owl\Component\Core\Repository\AdminUserRepositoryInterface;

/**
 * @template T of AdminUserInterface
 *
 * @extends UserRepository<T>
 *
 * @implements AdminUserRepositoryInterface<T>
 */
class AdminUserRepository extends UserRepository implements AdminUserRepositoryInterface
{
    public function listElasticsearch($entityAlias): QueryBuilder
    {
        return $this->createQueryBuilder($entityAlias)
            ->addSelect('role')
            ->leftJoin($entityAlias . '.role', 'role')
            ->leftJoin('role.setting', 'setting')
            ->leftJoin($entityAlias . '.registration', 'registration')
        ;
    }

    public function findByRoleUser(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->addSelect('role')
            ->addSelect('registration')
            ->leftJoin('o.role', 'role')
            ->leftJoin('role.setting', 'setting')
            ->leftJoin('o.registration', 'registration')
            ->andWhere('role.name = :roleName')
            ->setParameter('roleName', RoleAwareInterface::ROLE_USER_NAME)
        ;
    }

    public function findEnabledWithOwner(?int $userId): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');

        if ($userId) {
            $queryBuilder
                ->orWhere('o.id = :userId')
                ->setParameter('userId', $userId)
            ;
        }

        $queryBuilder
            ->orWhere('o.enabled = :enabled')
            ->setParameter('enabled', 1)
        ;

        return $queryBuilder;
    }
}
