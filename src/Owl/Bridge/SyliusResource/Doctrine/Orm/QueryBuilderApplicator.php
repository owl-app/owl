<?php

declare(strict_types=1);

namespace Owl\Bridge\SyliusResource\Doctrine\Orm;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;

final class QueryBuilderApplicator implements QueryBuilderApplicatorInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param class-string $resourceClass
     * @param array<string, mixed> $criteria
     */
    public function applyFilters(QueryBuilder $queryBuilder, string $resourceClass, array $criteria): void
    {
        /** @var ClassMetadata<object> $metadata */
        $metadata = $this->getClassMetadata($queryBuilder, $resourceClass);

        foreach ($criteria as $property => $value) {
            if (!in_array($property, array_merge($metadata->getAssociationNames(), $metadata->getFieldNames()), true)) {
                continue;
            }

            $name = $this->getPropertyName($property);

            if (null === $value) {
                $queryBuilder->andWhere($queryBuilder->expr()->isNull($name));
            } elseif (is_array($value)) {
                $queryBuilder->andWhere($queryBuilder->expr()->in($name, $value));
            } elseif ('' !== $value) {
                $parameter = str_replace('.', '_', $property);
                $queryBuilder
                    ->andWhere($queryBuilder->expr()->eq($name, ':' . $parameter))
                    ->setParameter($parameter, $value)
                ;
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param class-string $resourceClass
     * @param array<string, string> $sorting
     */
    public function applySort(QueryBuilder $queryBuilder, string $resourceClass, array $sorting): void
    {
        /** @var ClassMetadata<object> $metadata */
        $metadata = $this->getClassMetadata($queryBuilder, $resourceClass);

        foreach ($sorting as $property => $order) {
            if (!in_array($property, array_merge($metadata->getAssociationNames(), $metadata->getFieldNames()), true)) {
                continue;
            }

            if (!empty($order)) {
                $queryBuilder->addOrderBy($this->getPropertyName($property), $order);
            }
        }
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param class-string $resourceClass
     * @return ClassMetadata<object>
     */
    private function getClassMetadata(QueryBuilder $queryBuilder, string $resourceClass): ClassMetadata
    {
        /** @var ClassMetadata<object> $metadata */
        $metadata = $queryBuilder->getEntityManager()->getClassMetadata($resourceClass);
        return $metadata;
    }

    private function getPropertyName(string $name): string
    {
        if (false === strpos($name, '.')) {
            return 'o' . '.' . $name;
        }

        return $name;
    }
}
