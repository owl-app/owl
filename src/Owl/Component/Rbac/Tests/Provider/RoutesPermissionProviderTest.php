<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Rbac\Tests\Provider;

use Owl\Component\Rbac\Provider\RoutesPermissionProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RoutesPermissionProviderTest extends TestCase
{
    private RoutesPermissionProvider $provider;

    private RouteCollection $routeCollection;

    private RouterInterface&MockObject $router;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);

        $this->routeCollection = new RouteCollection();
        $this->provider = new RoutesPermissionProvider($this->router);
    }

    public function testGetPermissionsWithNoRoutes(): void
    {
        $this->router->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $result = $this->provider->getPermissions();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetPermissionsWithRoutesWithoutPermissionGroup(): void
    {
        $route1 = new Route('/test1');
        $route2 = new Route('/test2', [], [], ['_sylius' => ['vars' => []]]);

        $this->routeCollection->add('test_route1', $route1);
        $this->routeCollection->add('test_route2', $route2);

        $this->router->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $result = $this->provider->getPermissions();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetPermissionsWithRoutesWithPermissionGroup(): void
    {
        $route1 = new Route('/test1', [], [], ['_sylius' => ['vars' => ['permission' => ['group' => 'admin']]]]);
        $route2 = new Route('/test2', [], [], ['_sylius' => ['vars' => ['permission' => ['group' => 'user']]]]);

        $this->routeCollection->add('test_route1', $route1);
        $this->routeCollection->add('test_route2', $route2);

        $this->router->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $result = $this->provider->getPermissions();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('test_route1', $result);
        $this->assertArrayHasKey('test_route2', $result);
        $this->assertEquals('admin', $result['test_route1']['group']);
        $this->assertEquals('user', $result['test_route2']['group']);
        $this->assertEquals('owl.ui.permission.test_route1', $result['test_route1']['description']);
        $this->assertEquals('owl.ui.permission.test_route2', $result['test_route2']['description']);
    }

    public function testGetPermissionsWithCustomDescription(): void
    {
        $route = new Route('/test', [], [], ['_sylius' => ['vars' => ['permission' => ['group' => 'admin', 'description' => 'custom.description']]]]);

        $this->routeCollection->add('test_route', $route);

        $this->router->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $result = $this->provider->getPermissions();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertArrayHasKey('test_route', $result);
        $this->assertEquals('admin', $result['test_route']['group']);
        $this->assertEquals('custom.description', $result['test_route']['description']);
    }

    public function testGetPermissionsMixedRoutes(): void
    {
        $route1 = new Route('/test1');
        $route2 = new Route('/test2', [], [], ['_sylius' => ['vars' => ['permission' => ['group' => 'user']]]]);
        $route3 = new Route('/test3', [], [], ['_sylius' => ['vars' => []]]);
        $route4 = new Route('/test4', [], [], ['_sylius' => ['vars' => ['permission' => ['group' => 'admin']]]]);

        $this->routeCollection->add('test_route1', $route1);
        $this->routeCollection->add('test_route2', $route2);
        $this->routeCollection->add('test_route3', $route3);
        $this->routeCollection->add('test_route4', $route4);

        $this->router->method('getRouteCollection')
            ->willReturn($this->routeCollection);

        $result = $this->provider->getPermissions();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('test_route2', $result);
        $this->assertArrayHasKey('test_route4', $result);
        $this->assertArrayNotHasKey('test_route1', $result);
        $this->assertArrayNotHasKey('test_route3', $result);
    }
}