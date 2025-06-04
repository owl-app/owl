<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Factory\Http;

use Owl\Component\Core\Factory\Http\RedirectResponseFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

final class RedirectResponseFactoryTest extends TestCase
{
    private RedirectResponseFactory $factory;

    private RouterInterface&MockObject $router;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->factory = new RedirectResponseFactory($this->router);
    }

    public function testCreateWithStringRoute(): void
    {
        $routeName = 'app_home';
        $generatedUrl = '/home';
        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with($routeName)
            ->willReturn($generatedUrl);

        /** @var RedirectResponse $response */
        $response = $this->factory->create($request, $routeName);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($generatedUrl, $response->getTargetUrl());
    }

    public function testCreateWithArrayRoute(): void
    {
        $routeConfig = [
            'route' => 'app_product',
            'params' => ['id' => 123],
        ];
        $generatedUrl = '/product/123';
        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with('app_product', ['id' => 123])
            ->willReturn($generatedUrl);

        /** @var RedirectResponse $response */
        $response = $this->factory->create($request, $routeConfig);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($generatedUrl, $response->getTargetUrl());
    }

    public function testCreateWithXmlHttpRequest(): void
    {
        $routeName = 'app_home';
        $generatedUrl = '/home';
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->router->expects($this->once())
            ->method('generate')
            ->with($routeName)
            ->willReturn($generatedUrl);

        $response = $this->factory->create($request, $routeName);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals($generatedUrl, $response->headers->get('X-OWL-LOCATION'));
    }

    public function testCreateWithArrayRouteWithMissingRoute(): void
    {
        $routeConfig = [
            'params' => ['id' => 123],
        ];
        $generatedUrl = '/login';
        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with('sylius_admin_login', ['id' => 123])
            ->willReturn($generatedUrl);

        /** @var RedirectResponse $response */
        $response = $this->factory->create($request, $routeConfig);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($generatedUrl, $response->getTargetUrl());
    }

    public function testCreateWithArrayRouteWithMissingParams(): void
    {
        $routeConfig = [
            'route' => 'app_product',
        ];
        $generatedUrl = '/product';
        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with('app_product', [])
            ->willReturn($generatedUrl);

        /** @var RedirectResponse $response */
        $response = $this->factory->create($request, $routeConfig);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($generatedUrl, $response->getTargetUrl());
    }

    public function testCreateWithEmptyArray(): void
    {
        $routeConfig = [];
        $generatedUrl = '/login';
        $request = new Request();

        $this->router->expects($this->once())
            ->method('generate')
            ->with('sylius_admin_login', [])
            ->willReturn($generatedUrl);

        /** @var RedirectResponse $response */
        $response = $this->factory->create($request, $routeConfig);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($generatedUrl, $response->getTargetUrl());
    }

    public function testCreateWithXmlHttpRequestAndArrayRoute(): void
    {
        $routeConfig = [
            'route' => 'app_product',
            'params' => ['id' => 123],
        ];
        $generatedUrl = '/product/123';
        $request = new Request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->router->expects($this->once())
            ->method('generate')
            ->with('app_product', ['id' => 123])
            ->willReturn($generatedUrl);

        $response = $this->factory->create($request, $routeConfig);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals($generatedUrl, $response->headers->get('X-OWL-LOCATION'));
    }
}
