<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\QueryBuilder;
use Owl\Component\Core\Repository\SuggestionRepositoryInterface;
use Owl\Component\Suggestion\Model\SuggestionInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of SuggestionInterface
 *
 * @implements SuggestionRepositoryInterface<T>
 */
class SuggestionRepository extends EntityRepository implements SuggestionRepositoryInterface
{
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