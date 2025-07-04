<?php

declare(strict_types=1);

namespace Owl\Bundle\CoreBundle\Rbac;

use Stringable;

interface DirectPermissionUserProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getPermission(int|Stringable|string $userId): array;
}