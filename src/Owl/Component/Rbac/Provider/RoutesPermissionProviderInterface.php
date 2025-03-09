<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Provider;

interface RoutesPermissionProviderInterface
{
    public function getPermissions(): array;
}
