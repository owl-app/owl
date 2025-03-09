<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Context;

interface UserPermissionContextInterface
{
    public function getPermissions(): array;
}
