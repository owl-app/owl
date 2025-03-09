<?php

declare(strict_types=1);

namespace Yiisoft\Rbac\Doctrine;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use Yiisoft\Rbac\Doctrine\Exception\SeparatorCollisionException;
use Yiisoft\Rbac\Doctrine\ItemTreeTraversal\ItemTreeTraversalFactory;
use Yiisoft\Rbac\Doctrine\ItemTreeTraversal\ItemTreeTraversalInterface;
use Yiisoft\Rbac\Item;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;

/**
 * **Warning:** Do not use directly! Use with `Manager` from {@link https://github.com/yiisoft/rbac} package.
 *
 * Storage for RBAC items (roles and permissions) and their relations in the form of database tables. Operations are
 * performed using Doctrine Database.
 *
 * @phpstan-import-type ItemsIndexedByName from ItemsStorageInterface
 *
 * @phpstan-type RawItem = array{
 *     type: Item::TYPE_*,
 *     name: string,
 *     description: string|null,
 *     rule_name: string|null,
 *     created_at: int|string,
 *     updated_at: int|string
 * }
 * @phpstan-type RawRole = array{
 *     type: Item::TYPE_ROLE,
 *     name: string,
 *     description: string|null,
 *     rule_name: string|null,
 *     created_at: int|string,
 *     updated_at: int|string
 *  }
 * @phpstan-type RawPermission = array{
 *     type: Item::TYPE_PERMISSION,
 *     name: string,
 *     description: string|null,
 *     rule_name: string|null,
 *     created_at: int|string,
 *     updated_at: int|string
 * }
 */
final class ItemsStorage implements ItemsStorageInterface
{
    /**
     * @var string Separator used for joining and splitting item names.
     *
     * @var non-empty-string
     */
    private string $namesSeparator;

    /** @var ItemTreeTraversalInterface|null Lazily created RBAC item tree traversal strategy. */
    private ?ItemTreeTraversalInterface $treeTraversal = null;

    /**
     * @param string $tableName A name of the table for storing RBAC items.
     *
     * @param non-empty-string $tableName
     *
     * @param string $childrenTableName A name of the table for storing relations between RBAC items. When set to
     * `null`, it will be automatically generated using {@see $tableName}.
     *
     * @param non-empty-string $childrenTableName
     *
     * @param string $namesSeparator Separator used for joining and splitting item names.
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly string $tableName = 'yii_rbac_item',
        private readonly string $childrenTableName = 'yii_rbac_item_child',
        string $namesSeparator = ',',
    ) {
        $this->assertNamesSeparator($namesSeparator);
        $this->namesSeparator = $namesSeparator;
    }

    public function clear(): void
    {
        $itemsStorage = $this;

        $this->connection->transactional(function (Connection $conn) use ($itemsStorage): void {
            $queryBuilder = $conn->createQueryBuilder();

            $queryBuilder->delete($itemsStorage->childrenTableName)->executeQuery();
            $queryBuilder->delete($itemsStorage->tableName)->executeQuery();
        });
    }

    public function getAll(): array
    {
        $stmt = ($this->connection->createQueryBuilder())
            ->select('*')
            ->from($this->tableName)
            ->executeQuery();

        /** @var RawItem[] $rows */
        $rows = $stmt->fetchAllAssociative();

        return $this->getItemsIndexedByName($rows);
    }

    public function getByNames(array $names): array
    {
        if (empty($names)) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->in(
                    'name',
                    $queryBuilder->createNamedParameter($names, ArrayParameterType::STRING),
                ),
            )
            ->executeQuery();

        /** @var RawItem[] $rawItems */
        $rawItems = $stmt->fetchAllAssociative();

        return $this->getItemsIndexedByName($rawItems);
    }

    public function get(string $name): Permission|Role|null
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where($queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name)))
            ->executeQuery();

        /** @var RawItem|false $row */
        $row = $stmt->fetchAssociative();

        return $row === false ? null : $this->createItem($row);
    }

    public function exists(string $name): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where($queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name)))
            ->executeQuery();

        /** @var RawItem|false $row */
        $row = $stmt->fetchOne();

        return $row !== false;
    }

    public function roleExists(string $name): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name)),
                    $queryBuilder->expr()->eq('type', $queryBuilder->createNamedParameter(Item::TYPE_ROLE)),
                ),
            )
            ->executeQuery();

        /** @var RawItem|false $row */
        $row = $stmt->fetchOne();

        return $row !== false;
    }

    public function add(Item $item): void
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->insert($this->tableName);

        foreach ($item->getAttributes() as $name => $value) {
            $queryBuilder->setValue(
                $name,
                $queryBuilder->createNamedParameter($value),
            );
        }

        $queryBuilder->executeQuery();
    }

    public function update(string $name, Item $item): void
    {
        $itemsStorage = $this;

        $this
            ->connection
            ->transactional(function (Connection $database) use ($itemsStorage, $name, $item): void {
                $queryBuilder = $this->connection->createQueryBuilder();

                $stmt = $queryBuilder
                    ->select('*')
                    ->from($itemsStorage->childrenTableName)
                    ->where(
                        $queryBuilder->expr()->or(
                            $queryBuilder->expr()->eq('parent', $name),
                            $queryBuilder->expr()->eq('child', $name),
                        ),
                    )
                    ->executeQuery();

                $itemsChildren = $stmt->fetchAllAssociative();
                $itemsStorage->clearQueryBuilder($queryBuilder);

                if ($itemsChildren !== []) {
                    $itemsStorage->removeRelatedItemsChildren($database, $name);
                }

                $parameterName = $queryBuilder->createNamedParameter($name);
                $queryBuilder->update($itemsStorage->tableName, 'i')
                    ->where($queryBuilder->expr()->eq('name', $parameterName));

                foreach ($item->getAttributes() as $name => $value) {
                    $queryBuilder->set(
                        'i.' . $name,
                        $queryBuilder->createNamedParameter($value),
                    );
                }

                $queryBuilder->executeQuery();
                $itemsStorage->clearQueryBuilder($queryBuilder);

                if ($itemsChildren !== []) {
                    foreach ($itemsChildren as $itemChild) {
                        if ($itemChild['parent'] === $name) {
                            $itemChild['parent'] = $item->getName();
                        }

                        if ($itemChild['child'] === $name) {
                            $itemChild['child'] = $item->getName();
                        }

                        $queryBuilder->insert($itemsStorage->childrenTableName);
                        $queryBuilder->setValue('parent', $queryBuilder->createNamedParameter($itemChild['parent']));
                        $queryBuilder->setValue('child', $queryBuilder->createNamedParameter($itemChild['child']));
                        $queryBuilder->executeQuery();
                    }
                }
            });
    }

    public function remove(string $name): void
    {
        $itemsStorage = $this;

        $this
            ->connection
            ->transactional(function (Connection $database) use ($itemsStorage, $name): void {
                $itemsStorage->removeRelatedItemsChildren($database, $name);
                $queryBuilder = $database->createQueryBuilder();

                $queryBuilder->delete($itemsStorage->tableName)
                    ->where($queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name)))
                    ->executeQuery();
            });
    }

    public function getRoles(): array
    {
        return $this->getItemsByType(Item::TYPE_ROLE);
    }

    public function getRolesByNames(array $names): array
    {
        if (empty($names)) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('type', $queryBuilder->createNamedParameter(Item::TYPE_ROLE)),
                    $queryBuilder->expr()->in(
                        'name',
                        $queryBuilder->createNamedParameter($names, ArrayParameterType::STRING),
                    ),
                ),
            )
            ->executeQuery();

        /** @var RawRole[] $rawItems */
        $rawItems = $stmt->fetchAllAssociative();

        /** @var array<string, Role> */
        return $this->getItemsIndexedByName($rawItems);
    }

    public function getRole(string $name): ?Role
    {
        return $this->getItemByTypeAndName(Item::TYPE_ROLE, $name);
    }

    public function clearRoles(): void
    {
        $this->clearItemsByType(Item::TYPE_ROLE);
    }

    public function getPermissions(): array
    {
        return $this->getItemsByType(Item::TYPE_PERMISSION);
    }

    public function getPermissionsByNames(array $names): array
    {
        if (empty($names)) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('type', $queryBuilder->createNamedParameter(Item::TYPE_PERMISSION)),
                    $queryBuilder->expr()->in(
                        'name',
                        $queryBuilder->createNamedParameter($names, ArrayParameterType::STRING),
                    ),
                ),
            )
            ->executeQuery();

        /** @var RawRole[] $rawItems */
        $rawItems = $stmt->fetchAllAssociative();

        /** @var array<string, Permission> */
        return $this->getItemsIndexedByName($rawItems);
    }

    public function getPermission(string $name): ?Permission
    {
        return $this->getItemByTypeAndName(Item::TYPE_PERMISSION, $name);
    }

    public function clearPermissions(): void
    {
        $this->clearItemsByType(Item::TYPE_PERMISSION);
    }

    public function getParents(string $name): array
    {
        $rawItems = $this->getTreeTraversal()->getParentRows($name);

        return $this->getItemsIndexedByName($rawItems);
    }

    public function getHierarchy(string $name): array
    {
        $tree = [];
        $childrenNamesMap = [];

        foreach ($this->getTreeTraversal()->getHierarchy($name) as $data) {
            $childrenNamesMap[$data['name']] = $data['children'] !== '' && $data['children'] !== null
                ? explode($this->namesSeparator, $data['children'])
                : [];
            unset($data['children']);
            $tree[$data['name']] = ['item' => $this->createItem($data)];
        }

        foreach ($tree as $index => $_item) {
            $children = [];
            foreach ($childrenNamesMap[$index] as $childrenName) {
                if (!isset($tree[$childrenName])) {
                    throw new SeparatorCollisionException();
                }

                $children[$childrenName] = $tree[$childrenName]['item'];
            }

            $tree[$index]['children'] = $children;
        }

        return $tree;
    }

    public function getDirectChildren(string $name): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('i.*')
            ->from($this->tableName, 'i')
            ->innerJoin('i', $this->childrenTableName, 'children', 'children.child = i.name')
            ->where('i.parent.name = :name')
            ->setParameter('name', $name);

        /** @var RawItem[] $rawItems */
        $rawItems = $stmt->fetchAllAssociative();

        return $this->getItemsIndexedByName($rawItems);
    }

    public function getAllChildren(string|array $names): array
    {
        if (is_array($names) && empty($names)) {
            return [];
        }

        $rawItems = $this->getTreeTraversal()->getChildrenRows($names);

        return $this->getItemsIndexedByName($rawItems);
    }

    public function getAllChildPermissions(string|array $names): array
    {
        if (is_array($names) && empty($names)) {
            return [];
        }

        $rawItems = $this->getTreeTraversal()->getChildPermissionRows($names);

        /** @var array<string, Permission> */
        return $this->getItemsIndexedByName($rawItems);
    }

    public function getAllChildRoles(string|array $names): array
    {
        if (is_array($names) && empty($names)) {
            return [];
        }

        $rawItems = $this->getTreeTraversal()->getChildRoleRows($names);

        /** @var array<string, Role> */
        return $this->getItemsIndexedByName($rawItems);
    }

    public function hasChildren(string $name): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->childrenTableName)
            ->where($queryBuilder->expr()->eq('parent', $queryBuilder->createNamedParameter($name)))
            ->executeQuery();

        /** @var RawItem|false $row */
        $row = $stmt->fetchOne();

        return $row !== false;
    }

    public function hasChild(string $parentName, string $childName): bool
    {
        return $this->getTreeTraversal()->hasChild($parentName, $childName);
    }

    public function hasDirectChild(string $parentName, string $childName): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->childrenTableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('parent', $queryBuilder->createNamedParameter($parentName)),
                    $queryBuilder->expr()->eq('child', $queryBuilder->createNamedParameter($childName)),
                ),
            )
            ->executeQuery();

        /** @var RawItem|false $row */
        $row = $stmt->fetchOne();

        return $row !== false;
    }

    public function addChild(string $parentName, string $childName): void
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->insert($this->childrenTableName);

        $queryBuilder->setValue('parent', $queryBuilder->createNamedParameter($parentName));
        $queryBuilder->setValue('child', $queryBuilder->createNamedParameter($childName));

        $queryBuilder->executeQuery();
    }

    public function removeChild(string $parentName, string $childName): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete($this->childrenTableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('parent', $queryBuilder->createNamedParameter($parentName)),
                    $queryBuilder->expr()->eq('child', $queryBuilder->createNamedParameter($childName)),
                ),
            )
            ->executeQuery();
    }

    public function removeChildren(string $parentName): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete($this->childrenTableName)
            ->where($queryBuilder->expr()->eq('parent', $queryBuilder->createNamedParameter($parentName)))
            ->executeQuery();
    }

    /**
     * Gets either all existing roles or permissions, depending on a specified type.
     *
     * @param string $type Either {@see Item::TYPE_ROLE} or {@see Item::TYPE_PERMISSION}.
     *
     * @param Item::TYPE_* $type
     *
     * @return array A list of roles / permissions.
     *
     * @return ($type is Item::TYPE_PERMISSION ? array<string, Permission> : array<string, Role>)
     */
    private function getItemsByType(string $type): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->eq(
                    'type',

                    $queryBuilder->createNamedParameter($type),
                ),
            )
            ->executeQuery();

        /** @var RawPermission[] | RawRole[] $rawItems */
        $rawItems = $stmt->fetchAllAssociative();

        return $this->getItemsIndexedByName($rawItems);
    }

    /**
     * Gets a single item by its type and name.
     *
     * @param string $type Either {@see Item::TYPE_ROLE} or {@see Item::TYPE_PERMISSION}.
     *
     * @param Item::TYPE_* $type
     *
     * @return Permission|Role|null Either role or permission, depending on an initial type specified. `null` is
     * returned when no item was found by given condition.
     *
     * @return ($type is Item::TYPE_PERMISSION ? Permission : Role)|null
     */
    private function getItemByTypeAndName(string $type, string $name): Permission|Role|null
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('type', $queryBuilder->createNamedParameter($type)),
                    $queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($name)),
                ),
            )
            ->executeQuery();

        /**
         * @var RawItem|false $row
         */
        $row = $stmt->fetchAssociative();

        return $row === false ? null : $this->createItem($row);
    }

    /**
     * A factory method for creating single item with all attributes filled.
     *
     * @param RawPermission|RawRole $rawItem
     *
     * @return Permission|Role Either role or permission, depending on an initial type specified.
     */
    private function createItem(array $rawItem): Permission|Role
    {
        $item = $this
            ->createItemByTypeAndName($rawItem['type'], $rawItem['name'])
            ->withCreatedAt((int) $rawItem['created_at'])
            ->withUpdatedAt((int) $rawItem['updated_at']);

        if ($rawItem['description'] !== null) {
            $item = $item->withDescription($rawItem['description']);
        }

        if ($rawItem['rule_name'] !== null) {
            $item = $item->withRuleName($rawItem['rule_name']);
        }

        return $item;
    }

    /**
     * A basic factory method for creating a single item with name only.
     *
     * @param string $type Either {@see Item::TYPE_ROLE} or {@see Item::TYPE_PERMISSION}.
     *
     * @param Item::TYPE_* $type
     *
     * @return Permission|Role Either role or permission, depending on an initial type specified.
     *
     * @return ($type is Item::TYPE_PERMISSION ? Permission : Role)
     */
    private function createItemByTypeAndName(string $type, string $name): Permission|Role
    {
        return $type === Item::TYPE_PERMISSION ? new Permission($name) : new Role($name);
    }

    /**
     * Removes all related records in items children table for a given item name.
     *
     * @param Connection $database Yii database connection instance.
     * @param string $name Item name.
     */
    private function removeRelatedItemsChildren(Connection $database, string $name): void
    {
        $queryBuilder = $database->createQueryBuilder();
        $parameterItemName = $queryBuilder->createNamedParameter($name);

        $queryBuilder->delete($this->childrenTableName)
            ->where(
                $queryBuilder->expr()->or(
                    $queryBuilder->expr()->eq('parent', $parameterItemName),
                    $queryBuilder->expr()->eq('child', $parameterItemName),
                ),
            )
            ->executeQuery();
    }

    /**
     * Removes all existing items of a specified type.
     *
     * @param string $type Either {@see Item::TYPE_ROLE} or {@see Item::TYPE_PERMISSION}.
     *
     * @param Item::TYPE_* $type
     */
    private function clearItemsByType(string $type): void
    {
        $itemsStorage = $this;

        $this->connection->transactional(function (Connection $database) use ($itemsStorage, $type): void {
            // Create a query builder instance
            $queryBuilder = new QueryBuilder($database);

            // Build the subquery for parents
            $parentsSubQuerySelect = $queryBuilder
                ->select('parent')
                ->distinct()
                ->from($itemsStorage->childrenTableName);

            $parentsSubQuery = $queryBuilder->select('parents.parent')
                ->from($parentsSubQuerySelect->getSQL(), 'parents')
                ->innerJoin('parents', $itemsStorage->tableName, 'parent_items', 'parents.parent = parent_items.name')
                ->where('parent_items.type = :type')
                ->setParameter('type', $type);

            $childrenSubQuerySelect = $queryBuilder
                ->select('child')
                ->distinct()
                ->from($itemsStorage->childrenTableName);

            // Build the subquery for children
            $childrenSubQuery = $queryBuilder->select('children.child')
                ->from($childrenSubQuerySelect->getSQL(), 'children')
                ->distinct()
                ->innerJoin('children', $itemsStorage->tableName, 'child_items', 'children.child = child_items.name')
                ->where('child_items.type = :type')
                ->setParameter('type', $type);

            // Execute the main query
            $queryBuilder->delete($itemsStorage->childrenTableName)
                ->where('parent IN (' . $parentsSubQuery->getSQL() . ')')
                ->orWhere('child IN (' . $childrenSubQuery->getSQL() . ')');

            // Get the final SQL statement
            $sql = $queryBuilder->getSQL();

            // Execute the query and fetch the results
            $database->executeQuery($sql);
            $itemsStorage->clearQueryBuilder($queryBuilder);

            $queryBuilder->delete($itemsStorage->tableName)
                ->where('type = :type')
                ->setParameter('type', $type)
                ->executeQuery();
        });
    }

    /**
     * Creates RBAC item tree traversal strategy and returns it.
     * In case it was already created, it just retrieves previously saved instance.
     */
    private function getTreeTraversal(): ItemTreeTraversalInterface
    {
        if ($this->treeTraversal === null) {
            $this->treeTraversal = ItemTreeTraversalFactory::getItemTreeTraversal(
                $this->connection,
                $this->tableName,
                $this->childrenTableName,
                $this->namesSeparator,
            );
        }

        return $this->treeTraversal;
    }

    /**
     * @param RawItem[] $rawItems
     *
     * @return ItemsIndexedByName
     */
    private function getItemsIndexedByName(array $rawItems): array
    {
        $items = [];

        foreach ($rawItems as $rawItem) {
            $items[$rawItem['name']] = $this->createItem($rawItem);
        }

        return $items;
    }

    /**
     * @phpstan-assert non-empty-string $namesSeparator
     */
    private function assertNamesSeparator(string $namesSeparator): void
    {
        if (strlen($namesSeparator) !== 1) {
            throw new InvalidArgumentException('Names separator must be exactly 1 character long.');
        }
    }

    private function clearQueryBuilder(QueryBuilder $queryBuilder, bool $clearParameters = false): void
    {
        if ($clearParameters) {
            $queryBuilder->setParameters([]);
        }

        $queryBuilder->resetQueryParts();
    }
}
