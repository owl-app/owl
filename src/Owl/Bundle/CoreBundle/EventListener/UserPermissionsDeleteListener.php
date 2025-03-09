<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Owl\Component\Core\Model\AdminUserInterface;
use Yiisoft\Rbac\ManagerInterface;

final class UserPermissionsDeleteListener
{
    public function __construct(private ManagerInterface $rbacManager)
    {
    }

    public function preRemove(AdminUserInterface $adminUser, LifecycleEventArgs $event): void
    {
        // Transaction is started in ResourceDeleteHandler, so we can remove all permissions
        // In postRemove we haven't access to the user id
        $this->rbacManager->revokeAll((string) $adminUser->getId());
    }
}
