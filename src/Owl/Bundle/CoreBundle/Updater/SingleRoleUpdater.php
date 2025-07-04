<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Updater;

use Owl\Component\Core\Model\AdminUserInterface;
use Owl\Component\Core\Updater\SingleRoleUpdaterInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Role;

final class SingleRoleUpdater implements SingleRoleUpdaterInterface
{
    public function __construct(
        private ManagerInterface $rbacManager,
    ) {
    }

    public function assign(AdminUserInterface $user): void
    {
        $castedUserId = (string) $user->getId();
        $rolesUser = $this->rbacManager->getRolesByUserId($castedUserId);

        try {
            $this->deleteAssignedRoles($rolesUser, $castedUserId);
            $this->rbacManager->assign($user->getRole()->getName(), $castedUserId);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Role[] $roles
     */
    private function deleteAssignedRoles(array $roles, string $userId): void
    {
        if ($roles) {
            foreach ($roles as $role) {
                $this->rbacManager->revoke($role->getName(), $userId);
            }
        }
    }
}