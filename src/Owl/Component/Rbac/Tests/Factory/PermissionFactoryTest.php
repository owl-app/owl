<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Rbac\Tests\Factory;

use Owl\Component\Rbac\Factory\PermissionFactory;
use Owl\Component\Rbac\Model\AuthItemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Factory\FactoryInterface;

class PermissionFactoryTest extends TestCase
{
    private PermissionFactory $permissionFactory;

    private FactoryInterface&MockObject $innerFactory;

    private AuthItemInterface&MockObject $permission;

    protected function setUp(): void
    {
        $this->innerFactory = $this->createMock(FactoryInterface::class);
        $this->permission = $this->createMock(AuthItemInterface::class);

        $this->permissionFactory = new PermissionFactory($this->innerFactory);
    }

    public function testCreateNew(): void
    {
        $this->innerFactory->method('createNew')
            ->willReturn($this->permission);

        $result = $this->permissionFactory->createNew();

        $this->assertSame($this->permission, $result);
    }

    public function testCreateWithData(): void
    {
        $name = 'test.permission';
        $groupPermission = 'test.group';
        $description = 'Test permission description';

        $this->innerFactory->method('createNew')
            ->willReturn($this->permission);

        $this->permission->method('setName')
            ->with($name);

        $this->permission->method('setGroupPermission')
            ->with($groupPermission);

        $this->permission->method('setDescription')
            ->with($description);

        $result = $this->permissionFactory->createWithData($name, $groupPermission, $description);

        $this->assertSame($this->permission, $result);
    }

    public function testCreateWithEmptyStrings(): void
    {
        $name = '';
        $groupPermission = '';
        $description = '';

        $this->innerFactory->method('createNew')
            ->willReturn($this->permission);

        $this->permission->method('setName')
            ->with($name);

        $this->permission->method('setGroupPermission')
            ->with($groupPermission);

        $this->permission->method('setDescription')
            ->with($description);

        $result = $this->permissionFactory->createWithData($name, $groupPermission, $description);

        $this->assertSame($this->permission, $result);
    }

    public function testCreateWithSpecialCharacters(): void
    {
        $name = 'test.permission!@#$%^&*()';
        $groupPermission = 'test.group!@#$%^&*()';
        $description = 'Test description with special characters !@#$%^&*()';

        $this->innerFactory->method('createNew')
            ->willReturn($this->permission);

        $this->permission->method('setName')
            ->with($name);

        $this->permission->method('setGroupPermission')
            ->with($groupPermission);

        $this->permission->method('setDescription')
            ->with($description);

        $result = $this->permissionFactory->createWithData($name, $groupPermission, $description);

        $this->assertSame($this->permission, $result);
    }
}