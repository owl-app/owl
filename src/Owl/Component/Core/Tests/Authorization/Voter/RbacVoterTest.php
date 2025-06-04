<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Authorization\Voter;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Owl\Component\Core\Authorization\Voter\RbacVoter;
use Owl\Component\Core\Context\AdminUserContextInterface;
use Owl\Component\Core\Model\AdminUserInterface;

class RbacVoterTest extends TestCase
{
    private RbacVoter $voter;

    private RouterInterface&MockObject $router;

    private AdminUserContextInterface&MockObject $adminUserContext;

    private RouteCollection&MockObject $routeCollection;

    private TokenInterface&MockObject $token;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->adminUserContext = $this->createMock(AdminUserContextInterface::class);
        $this->routeCollection = $this->createMock(RouteCollection::class);
        $this->token = $this->createMock(TokenInterface::class);

        $this->router->method('getRouteCollection')->willReturn($this->routeCollection);

        $this->voter = new RbacVoter($this->router, $this->adminUserContext);
    }

    public function testSupportsWithValidRoute(): void
    {
        $route = 'app_admin_dashboard';
        $this->routeCollection->method('get')->with($route)->willReturn(new Route('/admin/dashboard'));

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, $route, null);

        $this->assertTrue($result);
    }

    public function testSupportsWithInvalidRoute(): void
    {
        $route = 'non_existent_route';
        $this->routeCollection->method('get')->with($route)->willReturn(null);

        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, $route, null);

        $this->assertFalse($result);
    }

    public function testSupportsWithEmptyRoute(): void
    {
        $route = '';
        
        $reflection = new \ReflectionMethod($this->voter, 'supports');
        $result = $reflection->invoke($this->voter, $route, null);

        $this->assertFalse($result);
    }

    public function testVoteOnAttributeWithPermission(): void
    {
        $route = 'app_admin_dashboard';
        $user = $this->createMock(AdminUserInterface::class);
        $user->method('getPermissions')->willReturn(['app_admin_dashboard', 'app_admin_users']);
        
        $this->adminUserContext->method('getUser')->willReturn($user);
        
        $reflection = new \ReflectionMethod($this->voter, 'voteOnAttribute');
        $result = $reflection->invoke($this->voter, $route, null, $this->token);
        
        $this->assertTrue($result);
    }
    
    public function testVoteOnAttributeWithoutPermission(): void
    {
        $route = 'app_admin_settings';
        $user = $this->createMock(AdminUserInterface::class);
        $user->method('getPermissions')->willReturn(['app_admin_dashboard', 'app_admin_users']);
        
        $this->adminUserContext->method('getUser')->willReturn($user);
        
        $reflection = new \ReflectionMethod($this->voter, 'voteOnAttribute');
        $result = $reflection->invoke($this->voter, $route, null, $this->token);
        
        $this->assertFalse($result);
    }
    
    public function testVoteOnAttributeWithNoUser(): void
    {
        $route = 'app_admin_dashboard';
        $this->adminUserContext->method('getUser')->willReturn(null);
        
        $reflection = new \ReflectionMethod($this->voter, 'voteOnAttribute');
        $result = $reflection->invoke($this->voter, $route, null, $this->token);
        
        $this->assertFalse($result);
    }
}
