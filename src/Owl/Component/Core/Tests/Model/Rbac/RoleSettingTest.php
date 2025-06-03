<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Model\Rbac;

use Owl\Component\Core\Model\Rbac\RoleSetting;
use Owl\Component\Core\Model\Rbac\RoleSettingInterface;
use PHPUnit\Framework\TestCase;

final class RoleSettingTest extends TestCase
{
    private RoleSetting $roleSetting;

    protected function setUp(): void
    {
        $this->roleSetting = new RoleSetting();
    }

    public function testImplementsRoleSettingInterface(): void
    {
        self::assertInstanceOf(RoleSettingInterface::class, $this->roleSetting);
    }

    public function testDisplayNameIsMutable(): void
    {
        $this->roleSetting->setDisplayName('Admin');
        self::assertSame('Admin', $this->roleSetting->getDisplayName());
    }

    public function testThemeIsMutable(): void
    {
        $this->roleSetting->setTheme('dark');
        self::assertSame('dark', $this->roleSetting->getTheme());
    }

    public function testRoleIsMutable(): void
    {
        $role = $this->createMock(\Owl\Component\Core\Model\Rbac\RoleInterface::class);
        $this->roleSetting->setRole($role);
        self::assertSame($role, $this->roleSetting->getRole());
    }
} 