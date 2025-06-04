<?php

declare(strict_types=1);

namespace Yiisoft\Rbac\Doctrine\ItemTreeTraversal;

use Doctrine\DBAL\Connection;
use RuntimeException;

/**
 * A factory for creating item tree traversal strategy depending on used RDBMS.
 *
 * @internal
 */
class ItemTreeTraversalFactory
{
    /**
     * Creates item tree traversal strategy depending on used RDBMS.
     *
     * @param Connection $connection Yii Database connection instance.
     * @param string $tableName A name of the table for storing RBAC items.
     * @param non-empty-string $tableName
     * @param string $childrenTableName A name of the table for storing relations between RBAC items.
     * @param non-empty-string $childrenTableName
     * @param string $namesSeparator Separator used for joining item names.
     * @param non-empty-string $namesSeparator
     *
     * @throws RuntimeException When a database was configured with an unknown driver, either because it is not
     * supported by Yii Database out of the box or newly added by Yii Database and not supported / tested yet in this
     * package.
     *
     * @return ItemTreeTraversalInterface Item tree traversal strategy.
     */
    public static function getItemTreeTraversal(
        Connection $connection,
        string $tableName,
        string $childrenTableName,
        string $namesSeparator,
    ): ItemTreeTraversalInterface {
        $arguments = [$connection, $tableName, $childrenTableName, $namesSeparator];
        $driver = $connection->getDriver()->getDatabasePlatform()->getName();

        // default - ignored due to the complexity of testing and preventing splitting of database argument.
        // @codeCoverageIgnoreStart
        return match ($driver) {
            'sqlite' => new SqliteCteItemTreeTraversal(...$arguments),
            'mysql' => new MysqlCteItemTreeTraversal(...$arguments),
            'pgsql' => new PostgresCteItemTreeTraversal(...$arguments),
            'sqlsrv' => new MssqlCteItemTreeTraversal(...$arguments),
            'oci' => new OracleCteItemTreeTraversal(...$arguments),
            default => throw new RuntimeException("$driver database driver is not supported."),
        };
        // @codeCoverageIgnoreEnd
    }
}
