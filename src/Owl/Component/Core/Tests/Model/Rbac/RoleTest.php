<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model\Rbac;

use Owl\Component\Core\Model\Rbac\Role;
use Owl\Component\Core\Model\Rbac\RoleInterface;
use PHPUnit\Framework\TestCase;

final class RoleTest extends TestCase
{
    private Role $role;

    protected function setUp(): void
    {
        $this->role = new Role();
    }

    public function testImplementsRoleInterface(): void
    {
        self::assertInstanceOf(RoleInterface::class, $this->role);
    }

    public function testSettingIsMutable(): void
    {
        $setting = $this->createMock(\Owl\Component\Core\Model\Rbac\RoleSettingInterface::class);
        $this->role->setSetting($setting);
        self::assertSame($setting, $this->role->getSetting());
    }

    public function testTypeIsRole(): void
    {
        self::assertSame('role', $this->role->getType());
    }
}
