<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Doctrine\Orm;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Owl\Bridge\SyliusResource\Doctrine\Common\Applicator\ResourceFilterApplicatorInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

final class CollectionProvider implements CollectionProviderInterface
{
    public function __construct(
        private ResourceFilterApplicatorInterface $resourceFilterApplicator,
        private QueryBuilderApplicatorInterface $queryBuilderApplicator,
    ) {
    }

    /**
     * @param EntityRepository<object> $repository
     * @param array<string, mixed> $criteria
     * @param array<string, mixed> $repositoryOptions
     * @param array<string, string> $sorting
     *
     * @return array<object>|Pagerfanta<object>
     */
    public function get(EntityRepository $repository, ?array $criteria = [], array $repositoryOptions = [], array $sorting = [], bool $isPaginated = false): array|Pagerfanta
    {
        $queryBuilder = $this->getQueryBuilder($repository, $repositoryOptions);

        if ($criteria) {
            $this->queryBuilderApplicator->applyFilters($queryBuilder, $repository->getClassName(), $criteria);
        }

        if ($sorting) {
            $this->queryBuilderApplicator->applySort($queryBuilder, $repository->getClassName(), $sorting);
        }

        $this->resourceFilterApplicator->apply($queryBuilder, $repository->getClassName(), self::TYPE);

        if ($isPaginated) {
            return new Pagerfanta(new QueryAdapter($queryBuilder, false, false));
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param EntityRepository<object> $repository
     * @param array<string, mixed> $repositoryOptions
     */
    private function getQueryBuilder(EntityRepository $repository, array $repositoryOptions = []): QueryBuilder
    {
        if (isset($repositoryOptions['method'])) {
            $method = $repositoryOptions['method'];
            $arguments = $repositoryOptions['arguments'] ?? [];

            return $repository->$method(...$arguments);
        }

        return $repository->createQueryBuilder('o');
    }
}
