<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Resolver;

use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\RoleAwareInterface;
use Owl\Component\Core\Resolver\RoleBasedValidationGroupResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormInterface;

class RoleBasedValidationGroupResolverTest extends TestCase
{
    private AdminUserContextInterface&MockObject $adminUserContext;

    private FormInterface&MockObject $form;

    private RoleBasedValidationGroupResolver $resolver;

    protected function setUp(): void
    {
        $this->adminUserContext = $this->getMockBuilder(AdminUserContextInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->form = $this->getMockBuilder(FormInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->resolver = new RoleBasedValidationGroupResolver($this->adminUserContext);
    }

    public function testInvokeWithAdminSystemRole(): void
    {
        $this->adminUserContext
            ->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn(RoleAwareInterface::ROLE_ADMIN_SYSTEM_NAME);

        $validationGroups = ($this->resolver)($this->form);

        $this->assertEquals(['owl', 'owl.role.admin_system'], $validationGroups);
    }

    public function testInvokeWithUserRole(): void
    {
        $this->adminUserContext
            ->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn(RoleAwareInterface::ROLE_USER_NAME);

        $validationGroups = ($this->resolver)($this->form);

        $this->assertEquals(['owl', 'owl.role.user'], $validationGroups);
    }

    public function testInvokeWithUnknownRole(): void
    {
        $this->adminUserContext
            ->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn('unknown_role');

        $validationGroups = ($this->resolver)($this->form);

        $this->assertEquals(['owl'], $validationGroups);
    }

    public function testInvokeWithNullRole(): void
    {
        $this->adminUserContext
            ->expects($this->once())
            ->method('getRoleCanonicalName')
            ->willReturn(null);

        $validationGroups = ($this->resolver)($this->form);

        $this->assertEquals(['owl'], $validationGroups);
    }
}
