<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Model;

class Permission extends AuthItem implements PermissionInterface
{
    /**
     * @return 'permission'
     */
    public function getType(): string
    {
        return 'permission';
    }
}
