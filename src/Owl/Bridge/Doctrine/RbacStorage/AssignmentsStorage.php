<?php

declare(strict_types=1);

namespace Yiisoft\Rbac\Doctrine;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Yiisoft\Rbac\Assignment;
use Yiisoft\Rbac\AssignmentsStorageInterface;

/**
 * **Warning:** Do not use directly! Use with `Manager` from {@link https://github.com/yiisoft/rbac} package.
 *
 * Storage for RBAC assignments in the form of database table. Operations are performed using Yii Database.
 *
 * @phpstan-type RawAssignment = array{
 *     item_name: string,
 *     user_id: string,
 *     created_at: int|string,
 * }
 */
final class AssignmentsStorage implements AssignmentsStorageInterface
{
    /**
     * @param Connection $connection Doctrine Database connection instance.
     * @param string $tableName A name of the table for storing RBAC assignments.
     *
     * @param non-empty-string $tableName
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly string $tableName = 'yii_rbac_assignment',
    ) {
    }

    public function getAll(): array
    {
        $stmt = ($this->connection->createQueryBuilder())
            ->select('*')
            ->from($this->tableName)
            ->executeQuery();

        /** @var RawAssignment[] $rows */
        $rows = $stmt->fetchAllAssociative();

        $assignments = [];
        foreach ($rows as $row) {
            $assignments[$row['user_id']][$row['item_name']] = new Assignment(
                $row['user_id'],
                $row['item_name'],
                (int) $row['created_at'],
            );
        }

        return $assignments;
    }

    public function getByUserId(string $userId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select(['item_name', 'created_at'])
            ->from($this->tableName)
            ->where($queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)))
            ->executeQuery();

        /** @var RawAssignment[] $rawAssignments */
        $rawAssignments = $stmt->fetchAllAssociative();

        $assignments = [];
        foreach ($rawAssignments as $rawAssignment) {
            $assignments[$rawAssignment['item_name']] = new Assignment(
                $userId,
                $rawAssignment['item_name'],
                (int) $rawAssignment['created_at'],
            );
        }

        return $assignments;
    }

    public function getByItemNames(array $itemNames): array
    {
        if (empty($itemNames)) {
            return [];
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->in(
                    'item_name',
                    $queryBuilder->createNamedParameter($itemNames, ArrayParameterType::STRING),
                ),
            )
            ->executeQuery();

        /** @var RawAssignment[] $rawAssignments */
        $rawAssignments = $stmt->fetchAllAssociative();

        $assignments = [];
        foreach ($rawAssignments as $rawAssignment) {
            $assignments[] = new Assignment(
                $rawAssignment['user_id'],
                $rawAssignment['item_name'],
                (int) $rawAssignment['created_at'],
            );
        }

        return $assignments;
    }

    public function get(string $itemName, string $userId): ?Assignment
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('created_at')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('item_name', $queryBuilder->createNamedParameter($itemName)),
                    $queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)),
                ),
            )
            ->executeQuery();

        /**
         * @var RawAssignment|null $row
         */
        $row = $stmt->fetchAssociative();

        return $row === null ? null : new Assignment($userId, $itemName, (int) $row['created_at']);
    }

    public function exists(string $itemName, string $userId): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('item_name', $queryBuilder->createNamedParameter($itemName)),
                    $queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)),
                ),
            )
            ->executeQuery();

        /** @var array<0, 1>|false $row */
        $row = $stmt->fetchOne();

        return $row !== false;
    }

    public function userHasItem(string $userId, array $itemNames): bool
    {
        if (empty($itemNames)) {
            return false;
        }

        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)),
                    $queryBuilder->expr()->in(
                        'item_name',
                        $queryBuilder->createNamedParameter($itemNames, ArrayParameterType::STRING),
                    ),
                ),
            )
            ->executeQuery();

        /** @var array<0, 1>|false $row */
        $row = $stmt->fetchOne();

        return $row !== false;
    }

    public function filterUserItemNames(string $userId, array $itemNames): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('item_name')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)),
                    $queryBuilder->expr()->in(
                        'item_name',
                        $queryBuilder->createNamedParameter($itemNames, ArrayParameterType::STRING),
                    ),
                ),
            )
            ->executeQuery();

        /** @var array{itemName: string} $rows */
        $rows = $stmt->fetchAllAssociative();

        return array_column($rows, 'item_name');
    }

    public function add(Assignment $assignment): void
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->insert($this->tableName);

        $queryBuilder->setValue('item_name', $queryBuilder->createNamedParameter($assignment->getItemName()));
        $queryBuilder->setValue('user_id', $queryBuilder->createNamedParameter($assignment->getUserId()));
        $queryBuilder->setValue('created_at', $queryBuilder->createNamedParameter(date('Y-m-d H:i:s', $assignment->getCreatedAt())));

        $queryBuilder->executeQuery();
    }

    public function hasItem(string $name): bool
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $stmt = $queryBuilder
            ->select('*')
            ->from($this->tableName)
            ->where(
                $queryBuilder->expr()->eq('item_name', $queryBuilder->createNamedParameter($name)),
            )
            ->executeQuery();

        /** @var array<0, 1>|false $row */
        $row = $stmt->fetchOne();

        return $row !== false;
    }

    public function renameItem(string $oldName, string $newName): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->update($this->tableName)
            ->where($queryBuilder->expr()->eq('name', $queryBuilder->createNamedParameter($oldName)));

        $queryBuilder->set('item_name', $queryBuilder->createNamedParameter($newName));

        $queryBuilder->executeQuery();
    }

    public function remove(string $itemName, string $userId): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete($this->tableName)
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('item_name', $queryBuilder->createNamedParameter($itemName)),
                    $queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)),
                ),
            )
            ->executeQuery();
    }

    public function removeByUserId(string $userId): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete($this->tableName)
            ->where(
                $queryBuilder->expr()->eq('user_id', $queryBuilder->createNamedParameter($userId)),
            )
            ->executeQuery();
    }

    public function removeByItemName(string $itemName): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder
            ->delete($this->tableName)
            ->where(
                $queryBuilder->expr()->eq('item_name', $queryBuilder->createNamedParameter($itemName)),
            )
            ->executeQuery();
    }

    public function clear(): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();

        $queryBuilder->delete($this->tableName)->executeQuery();
    }
}
