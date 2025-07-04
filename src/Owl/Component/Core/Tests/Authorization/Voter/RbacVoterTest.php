<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Authorization\Voter;

use Owl\Component\Core\Authorization\Voter\RbacVoter;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class RbacVoterTest extends TestCase
{
    private RbacVoter $voter;

    private RouteCollection $routeCollection;

    private RouterInterface&MockObject $router;

    private AdminUserContextInterface&MockObject $adminUserContext;

    private TokenInterface&MockObject $token;

    private AdminUserInterface&MockObject $user;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->token = $this->createMock(TokenInterface::class);
        $this->user = $this->createMock(AdminUserInterface::class);

        $this->voter = new RbacVoter($this->router, $this->adminUserContext);
        $this->routeCollection = new RouteCollection();
    }

    public function testItGrantsAccessWhenRouteExistsAndUserHasPermission(): void
    {
        $this->routeCollection->add('admin_user_index', new Route('/admin/users'));

        $this->router
            ->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $this->user
            ->method('getPermissions')
            ->willReturn(['admin_user_index']);

        $this->adminUserContext
            ->method('getUser')
            ->willReturn($this->user);

        $result = $this->voter->vote($this->token, null, ['admin_user_index']);

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testItDeniesAccessWhenUserDoesNotHavePermission(): void
    {
        $this->routeCollection->add('admin_user_index', new Route('/admin/users'));

        $this->router
            ->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $this->user
            ->method('getPermissions')
            ->willReturn(['other_permission']);

        $this->adminUserContext
            ->method('getUser')
            ->willReturn($this->user);

        $result = $this->voter->vote($this->token, null, ['admin_user_index']);

        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testItAbstainsWhenRouteDoesNotExist(): void
    {
        $this->router
            ->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $result = $this->voter->vote($this->token, null, ['non_existing_route']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testItAbstainsWhenAttributeIsEmpty(): void
    {
        $this->router
            ->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $result = $this->voter->vote($this->token, null, ['']);

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }
}