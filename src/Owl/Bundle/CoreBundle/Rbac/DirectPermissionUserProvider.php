<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Rbac;

use Stringable;
use Yiisoft\Rbac\AssignmentsStorageInterface;
use Yiisoft\Rbac\ItemsStorageInterface;
use Yiisoft\Rbac\ManagerInterface;

final class DirectPermissionUserProvider implements DirectPermissionUserProviderInterface
{
    public function __construct(
        private ManagerInterface $rbacManager,
        private ItemsStorageInterface $rbacItemStorage,
        private AssignmentsStorageInterface $rbacAssignmentsStorage,
    ) {
    }

    public function getPermission(int|Stringable|string $userId): array
    {
        $userId = (string) $userId;
        $assignments = $this->rbacAssignmentsStorage->getByUserId($userId);
        $assignmentNames = array_keys($assignments);

        return array_merge(
            $this->rbacManager->getDefaultRoles(),
            $this->rbacItemStorage->getByNames($assignmentNames),
        );
    }
}
