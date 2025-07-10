<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Doctrine\Orm;

use Doctrine\ORM\QueryBuilder;

interface QueryBuilderApplicatorInterface
{
    /**
     * @param array<string, mixed> $criteria
     */
    public function applyFilters(QueryBuilder $queryBuilder, string $resourceClass, array $criteria): void;

    /**
     * @param array<string, string> $sorting
     */
    public function applySort(QueryBuilder $queryBuilder, string $resourceClass, array $sorting): void;
}
