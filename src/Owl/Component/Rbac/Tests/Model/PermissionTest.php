<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Rbac\Tests\Model;

use Owl\Component\Rbac\Model\AuthItemInterface;
use Owl\Component\Rbac\Model\Permission;
use Owl\Component\Rbac\Model\PermissionInterface;
use PHPUnit\Framework\TestCase;

final class PermissionTest extends TestCase
{
    private Permission $permission;

    protected function setUp(): void
    {
        $this->permission = new Permission();
    }

    public function testImplementsPermissionInterface(): void
    {
        self::assertInstanceOf(PermissionInterface::class, $this->permission);
    }

    public function testImplementsAuthItemInterface(): void
    {
        self::assertInstanceOf(AuthItemInterface::class, $this->permission);
    }

    public function testHasNoNameByDefault(): void
    {
        self::assertNull($this->permission->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->permission->setName('view-dashboard');
        self::assertSame('view-dashboard', $this->permission->getName());
    }

    public function testReturnsCorrectType(): void
    {
        self::assertSame('permission', $this->permission->getType());
    }
}
