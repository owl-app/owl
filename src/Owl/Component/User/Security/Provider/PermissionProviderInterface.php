<?php

declare(strict_types=1);

namespace Owl\Component\User\Security\Provider;

interface PermissionProviderInterface
{
    public function getPermissionsByUserId(int $userId): array;
}
