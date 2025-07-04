<?php

declare(strict_types=1);

namespace Owl\Component\User\Model;

interface PermissionUserInterface
{
    /**
     * @param array<string, bool> $permissions
     */
    public function setPermissions(array $permissions): void;

    /**
     * @return array<string, bool>
     */
    public function getPermissions(): array;
}