<?php

declare(strict_types=1);

namespace Owl\Bundle\RbacBundle\Provider;

use Owl\Component\User\Security\Provider\PermissionProviderInterface;
use Yiisoft\Rbac\ManagerInterface;
use Yiisoft\Rbac\Permission;

final class PermissionProvider implements PermissionProviderInterface
{
    /** @var ManagerInterface */
    private $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return Permission[]
     */
    public function getPermissionsByUserId(int $userId): array
    {
        return $this->manager->getPermissionsByUserId($userId);
    }
}
