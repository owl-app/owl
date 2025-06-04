<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Authorization;

use Owl\Component\Core\Authorization\AuthorizationChecker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as SymfonyAuthorizationCheckerInterface;

class AuthorizationCheckerTest extends TestCase
{
    private AuthorizationChecker $authorizationChecker;

    private MockObject $symfonyAuthorizationChecker;

    protected function setUp(): void
    {
        $this->symfonyAuthorizationChecker = $this->createMock(SymfonyAuthorizationCheckerInterface::class);
        $this->authorizationChecker = new AuthorizationChecker($this->symfonyAuthorizationChecker);
    }

    public function testIsGrantedPassesCorrectArgumentsToSymfonyChecker(): void
    {
        $route = 'app_admin_dashboard';
        $permission = 'some_permission';

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => $route]);

        $requestConfiguration = $this->createMock(RequestConfiguration::class);
        $requestConfiguration->method('getRequest')->willReturn($request);

        $this->symfonyAuthorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with($route, $permission)
            ->willReturn(true);

        $result = $this->authorizationChecker->isGranted($requestConfiguration, $permission);

        $this->assertTrue($result);
    }

    public function testIsGrantedWithoutPermission(): void
    {
        $route = 'app_admin_dashboard';

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => $route]);

        $requestConfiguration = $this->createMock(RequestConfiguration::class);
        $requestConfiguration->method('getRequest')->willReturn($request);

        $this->symfonyAuthorizationChecker
            ->expects($this->once())
            ->method('isGranted')
            ->with($route, null)
            ->willReturn(true);

        $result = $this->authorizationChecker->isGranted($requestConfiguration);

        $this->assertTrue($result);
    }

    public function testIsGrantedReturnsFalseWhenDenied(): void
    {
        $route = 'app_admin_dashboard';

        $request = $this->createMock(Request::class);
        $request->attributes = new ParameterBag(['_route' => $route]);

        $requestConfiguration = $this->createMock(RequestConfiguration::class);
        $requestConfiguration->method('getRequest')->willReturn($request);

        $this->symfonyAuthorizationChecker
            ->method('isGranted')
            ->willReturn(false);

        $result = $this->authorizationChecker->isGranted($requestConfiguration);

        $this->assertFalse($result);
    }
}
