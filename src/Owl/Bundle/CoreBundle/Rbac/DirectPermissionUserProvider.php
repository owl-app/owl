<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Rbac;

use Stringable;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;
use Yiisoft\Rbac\Role;

final class DirectPermissionUserProvider implements DirectPermissionUserProviderInterface
{
    public function __construct(
        private ManagerInterface $rbacManager,
        private ItemsStorageInterface $rbacItemStorage,
        private AssignmentsStorageInterface $rbacAssignmentsStorage,
    ) {
    }

    /**
     * @return array<int, Permission|Role>
     */
    public function getPermission(int|Stringable|string $userId): array
    {
        $userId = (string) $userId;
        $assignments = $this->rbacAssignmentsStorage->getByUserId($userId);
        $assignmentNames = array_keys($assignments);

        /** @var array<string, Permission|Role> $items */
        $items = $this->rbacItemStorage->getByNames($assignmentNames);

        return array_merge(
            $this->rbacManager->getDefaultRoles(),
            array_values($items),
        );
    }
}