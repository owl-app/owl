<?php

declare(strict_types=1);

namespace Owl\Component\User\Model;

interface PermissionUserInterface
{
    /**
     * @param array<int, string>|array<string, mixed> $permissions
     */
    public function setPermissions(array $permissions): void;

    /**
     * @return array<int, string>|array<string, mixed>
     */
    public function getPermissions(): array;
}
