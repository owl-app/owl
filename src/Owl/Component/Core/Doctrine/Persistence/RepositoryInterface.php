<?php

declare(strict_types=1);

namespace Owl\Component\Core\Doctrine\Persistence;

use Doctrine\ORM\QueryBuilder;

interface RepositoryInterface
{
    /**
     * @param int[] $ids
     */
    public function findById(array $ids): QueryBuilder;
}