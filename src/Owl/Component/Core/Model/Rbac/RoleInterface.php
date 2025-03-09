<?php

declare(strict_types=1);

namespace Owl\Component\Core\Model\Rbac;

use Owl\Component\Rbac\Model\RoleInterface as BaseRoleInterface;

interface RoleInterface extends BaseRoleInterface
{
    public function getCanonicalName(): string;

    public function getSetting(): ?RoleSettingInterface;

    public function setSetting(?RoleSettingInterface $setting): void;
}
