<?php

declare(strict_types=1);

namespace Yiisoft\Rbac\Doctrine\ItemTreeTraversal;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Yiisoft\Rbac\Doctrine\ItemsStorage;
use Yiisoft\Rbac\Item;

/**
 * A RBAC item tree traversal strategy based on CTE (common table expression). Uses `WITH` expression to form a
 * recursive query. The base queries are unified as much as possible to work for all RDBMS supported by Yii Database
 * with minimal differences.
 *
 * @internal
 *
 * @phpstan-import-type RawItem from ItemsStorage
 * @phpstan-import-type Hierarchy from ItemTreeTraversalInterface
 */
abstract class CteItemTreeTraversal implements ItemTreeTraversalInterface
{
    /**
     * @param Connection $connection Doctrine database connection instance.
     * @param string $tableName A name of the table for storing RBAC items.
     * @param non-empty-string $tableName
     * @param string $childrenTableName A name of the table for storing relations between RBAC items.
     * @param non-empty-string $childrenTableName
     * @param string $namesSeparator Separator used for joining item names.
     * @param non-empty-string $namesSeparator
     */
    public function __construct(
        protected Connection $connection,
        protected string $tableName,
        protected string $childrenTableName,
        protected string $namesSeparator,
    ) {
    }

    /**
     * @return RawItem[] An array of parent items.
     */
    public function getParentRows(string $name): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $baseOuterQueryBuilder = $queryBuilder
            ->select('item.*')
            ->where(
                $queryBuilder->expr()->neq('item.name', $queryBuilder->createNamedParameter($name)),
            );

        return $this
            ->getRowsStatement($name, baseOuterQueryBuilder: $baseOuterQueryBuilder)
            ->fetchAllAssociative();
    }

    /**
     * @return Hierarchy An array of items with their children.
     */
    public function getHierarchy(string $name): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $baseOuterQueryBuilder = $queryBuilder->select(['item.*', 'parent_of.children']);

        $queryBuilder = $this->connection->createQueryBuilder();
        $cteSelectItemQuery = $queryBuilder
            ->select(['name', $this->getEmptyChildrenExpression()])
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->eq('name', ':cteSelectItemQueryName'),
            )
            ->setParameter('cteSelectItemQueryName', $name);

        $queryBuilder = $this->connection->createQueryBuilder();
        $cteSelectRelationQuery = $queryBuilder
            ->select(['parent', $this->getTrimConcatChildrenExpression()])
            ->from($this->childrenTableName, 'item_child_recursive')
            ->innerJoin(
                'item_child_recursive',
                'parent_of',
                'parent_of',
                'item_child_recursive.child = parent_of.child_name',
            );

        $queryBuilder = $this->connection->createQueryBuilder();
        $outerQuery = $baseOuterQueryBuilder
            ->from('parent_of')
            ->leftJoin(
                'parent_of',
                $this->tableName,
                'item',
                'item.name = parent_of.child_name',
            );

        $sql = "{$this->getWithExpression()} parent_of(child_name, children) AS (
            $cteSelectItemQuery
            UNION ALL
            $cteSelectRelationQuery
        )
        $outerQuery";

        return $this->connection->executeQuery(
            $sql,
            array_merge($outerQuery->getParameters(), $cteSelectItemQuery->getParameters()),
            array_merge($outerQuery->getParameterTypes(), $cteSelectItemQuery->getParameterTypes()),
        )->fetchAllAssociative();
    }

    /**
     * @return RawItem[] An array of child items.
     */
    public function getChildrenRows(string|array $names): array
    {
        $baseOuterQueryBuilder = $this->getChildrenBaseOuterQuery($names);

        return $this
            ->getRowsStatement($names, baseOuterQueryBuilder: $baseOuterQueryBuilder, areParents: false)
            ->fetchAllAssociative();
    }

    /**
     * @return RawItem[] An array of child items that are permissions.
     */
    public function getChildPermissionRows(string|array $names): array
    {
        $baseOuterQueryBuilder = $this->getChildrenBaseOuterQuery($names);
        $baseOuterQueryBuilder
            ->andWhere(
                $baseOuterQueryBuilder->expr()->eq(
                    'item.type',
                    $baseOuterQueryBuilder->createNamedParameter(Item::TYPE_PERMISSION, \PDO::PARAM_STR),
                ),
            );

        return $this
            ->getRowsStatement($names, baseOuterQueryBuilder: $baseOuterQueryBuilder, areParents: false)
            ->fetchAllAssociative();
    }

    /**
     * @return RawItem[] An array of child items that are roles.
     */
    public function getChildRoleRows(string|array $names): array
    {
        $baseOuterQueryBuilder = $this->getChildrenBaseOuterQuery($names);
        $baseOuterQueryBuilder
            ->andWhere(
                $baseOuterQueryBuilder->expr()->eq(
                    'item.type',
                    $baseOuterQueryBuilder->createNamedParameter(Item::TYPE_ROLE, \PDO::PARAM_STR),
                ),
            );

        return $this
            ->getRowsStatement($names, baseOuterQueryBuilder: $baseOuterQueryBuilder, areParents: false)
            ->fetchAllAssociative();
    }

    public function hasChild(string $parentName, string $childName): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $baseOuterQueryBuilder = $queryBuilder
            ->select('*')
            ->andWhere(
                $queryBuilder->expr()->eq(
                    'item.name',
                    $queryBuilder->createNamedParameter($childName, \PDO::PARAM_STR),
                ),
            );

        /** @var array<0, 1>|false $result */
        $result = $this
            ->getRowsStatement($parentName, baseOuterQueryBuilder: $baseOuterQueryBuilder, areParents: false)
            ->fetchAssociative();

        return $result !== false;
    }

    /**
     * @infection-ignore-all
     *  - ProtectedVisibility.
     *
     * @return non-empty-string
     */
    protected function getEmptyChildrenExpression(): string
    {
        return "''";
    }

    /**
     * @return non-empty-string
     */
    protected function getTrimConcatChildrenExpression(): string
    {
        return "TRIM('$this->namesSeparator' FROM CONCAT(children, '$this->namesSeparator', " .
            'item_child_recursive.child))';
    }

    private function getRowsStatement(
        string|array $names,
        QueryBuilder $baseOuterQueryBuilder,
        bool $areParents = true,
    ): Result {
        if ($areParents) {
            $cteSelectRelationName = 'parent';
            $cteConditionRelationName = 'child';
            $cteName = 'parent_of';
            $cteParameterName = 'child_name';
        } else {
            $cteSelectRelationName = 'child';
            $cteConditionRelationName = 'parent';
            $cteName = 'child_of';
            $cteParameterName = 'parent_name';
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $cteSelectItemQuery = $queryBuilder
            ->select('name')
            ->from($this->tableName);

        if (is_string($names)) {
            $cteSelectItemQuery = $cteSelectItemQuery->where(
                $cteSelectItemQuery->expr()->eq('name', ':cteSelectItemQueryName'),
            );
            $cteSelectItemQuery->setParameter('cteSelectItemQueryName', $names);
        } else {
            $cteSelectItemQuery = $cteSelectItemQuery->where(
                $cteSelectItemQuery->expr()->in(
                    'name',
                    $cteSelectItemQuery->createNamedParameter($names, ArrayParameterType::STRING),
                ),
            );
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $cteSelectRelationQuery = $queryBuilder
            ->select($cteSelectRelationName)
            ->from($this->childrenTableName, 'item_child_recursive')
            ->innerJoin(
                'item_child_recursive',
                $cteName,
                $cteName,
                "item_child_recursive.$cteConditionRelationName = $cteName.$cteParameterName",
            );

        $queryBuilder = $this->connection->createQueryBuilder();

        $outerQuery = $baseOuterQueryBuilder
            ->from($cteName)
            ->leftJoin(
                $cteName,
                $this->tableName,
                'item',
                "item.name = $cteName.$cteParameterName",
            );

        $sql = "{$this->getWithExpression()} $cteName($cteParameterName) AS (
            $cteSelectItemQuery
            UNION ALL
            $cteSelectRelationQuery
        )
        $outerQuery";

        return $this->connection->executeQuery(
            $sql,
            array_merge($outerQuery->getParameters(), $cteSelectItemQuery->getParameters()),
            array_merge($outerQuery->getParameterTypes(), $cteSelectItemQuery->getParameterTypes()),
        );
    }

    /**
     * @param string|non-empty-array<array-key, string> $names
     */
    private function getChildrenBaseOuterQuery(string|array $names): QueryBuilder
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $baseOuterQuery = $queryBuilder->select('item.*')->distinct();

        if (is_string($names)) {
            return $baseOuterQuery->where(
                $queryBuilder->expr()->neq(
                    'item.name',
                    $queryBuilder->createNamedParameter($names, \PDO::PARAM_STR),
                ),
            );
        }

        return $baseOuterQuery
            ->where(
                $queryBuilder->expr()->notIn(
                    'item.name',
                    $queryBuilder->createNamedParameter($names, ArrayParameterType::STRING),
                ),
            );
    }

    /**
     * Gets `WITH` expression used in a DB query.
     *
     * @infection-ignore-all
     * - ProtectedVisibility.
     *
     * @return string `WITH` expression.
     */
    protected function getWithExpression(): string
    {
        return 'WITH RECURSIVE';
    }
}
