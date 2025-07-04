<?php

declare(strict_types=1);

namespace Owl\Component\User\Security\Provider;

interface PermissionProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getPermissionsByUserId(int $userId): array;
}