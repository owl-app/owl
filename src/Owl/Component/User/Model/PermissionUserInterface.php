<?php

declare(strict_types=1);

namespace Owl\Component\User\Model;

interface PermissionUserInterface
{
    public function setPermissions(array $permissions): void;

    public function getPermissions(): array;
}
