<?php

declare(strict_types=1);

namespace Owl\Component\Rbac\Provider;

interface RoutesPermissionProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getPermissions(): array;
}