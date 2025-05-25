<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;

trait RepositoryTrait
{
    public function findById(array $ids): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $this->createQueryBuilder('o')
            ->where(
                $queryBuilder->expr()->in('o.id', $ids),
            );
    }
}
