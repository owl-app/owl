<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Rbac\Tests\Model;

use Doctrine\Common\Collections\Collection;
use Owl\Component\Rbac\Model\AuthItemInterface;
use Owl\Component\Rbac\Model\Permission;
use Owl\Component\Rbac\Model\PermissionInterface;
use Owl\Component\Rbac\Model\Role;
use Owl\Component\Rbac\Model\RoleInterface;
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

    public function testImplementsAuthItemInterface(): void
    {
        self::assertInstanceOf(AuthItemInterface::class, $this->role);
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->role->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->role->setName('admin');
        self::assertSame('admin', $this->role->getName());
    }

    public function testReturnsCorrectType(): void
    {
        self::assertSame('role', $this->role->getType());
    }

    public function testInitializesPermissionsCollection(): void
    {
        self::assertInstanceOf(Collection::class, $this->role->getPermissions());
        self::assertTrue($this->role->getPermissions()->isEmpty());
    }

    public function testHasNoPermissionsAddedByDefault(): void
    {
        self::assertFalse($this->role->hasPermissions());
    }

    public function testAddsPermission(): void
    {
        $permission = new Permission();
        $permission->setName('view-dashboard');

        $this->role->addPermission($permission);
        self::assertTrue($this->role->hasPermission($permission));
    }

    public function testRemovesPermission(): void
    {
        $permission = new Permission();
        $permission->setName('view-dashboard');

        $this->role->addPermission($permission);
        $this->role->removePermission($permission);

        self::assertFalse($this->role->hasPermission($permission));
    }

    public function testDoesNotAddSamePermissionTwice(): void
    {
        $permission = new Permission();
        $permission->setName('view-dashboard');

        $this->role->addPermission($permission);
        $this->role->addPermission($permission);

        self::assertCount(1, $this->role->getPermissions());
    }
} 