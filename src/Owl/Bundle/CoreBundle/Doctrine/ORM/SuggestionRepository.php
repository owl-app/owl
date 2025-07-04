<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Core\Repository\SuggestionRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class SuggestionRepository extends EntityRepository implements SuggestionRepositoryInterface
{
    /**
     * @param array<int|string> $ids
     */
    public function findByIdWithOwner(array $ids, ?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        $queryBuilder = $queryBuilder ?? $this->createQueryBuilder('o');

        return $queryBuilder
            ->andWhere($queryBuilder->expr()->in('o.id', $ids))
        ;
    }

    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->addSelect('files')
            ->leftJoin('o.files', 'files')
        ;
    }
}