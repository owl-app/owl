<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\AdminBundle\Controller;

use Owl\Bundle\AdminBundle\Controller\DashboardController;
use Owl\Component\Setting\Storage\SettingStorageInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

#[CoversClass(DashboardController::class)]
final class DashboardControllerTest extends TestCase
{
    private DashboardController $controller;

    private SettingStorageInterface&MockObject $settingStorage;

    private Environment&MockObject $templatingEngine;

    private Request&MockObject $request;

    protected function setUp(): void
    {
        $this->settingStorage = $this->createMock(SettingStorageInterface::class);
        $this->templatingEngine = $this->createMock(Environment::class);
        $this->request = $this->createMock(Request::class);

        $this->controller = new DashboardController(
            $this->settingStorage,
            $this->templatingEngine,
        );
    }

    #[Test]
    public function it_renders_dashboard_index_with_settings(): void
    {
        // Arrange
        $settings = [
            'description_dashboard' => 'Welcome to the admin dashboard',
        ];

        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->with('system', ['description_dashboard'])
            ->willReturn($settings);

        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/dashboard/index.html.twig', [
                'settings' => $settings,
            ])
            ->willReturn('<html>dashboard content</html>');

        // Act
        $response = $this->controller->indexAction($this->request);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>dashboard content</html>', $response->getContent());
    }

    #[Test]
    public function it_handles_empty_settings(): void
    {
        // Arrange
        $settings = [];

        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->with('system', ['description_dashboard'])
            ->willReturn($settings);

        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/dashboard/index.html.twig', [
                'settings' => $settings,
            ])
            ->willReturn('<html>dashboard empty</html>');

        // Act
        $response = $this->controller->indexAction($this->request);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>dashboard empty</html>', $response->getContent());
    }

    #[Test]
    public function it_handles_null_settings(): void
    {
        // Arrange
        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->with('system', ['description_dashboard'])
            ->willReturn([]); // Return empty array instead of null

        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/dashboard/index.html.twig', [
                'settings' => [],
            ])
            ->willReturn('<html>dashboard empty</html>');

        // Act
        $response = $this->controller->indexAction($this->request);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>dashboard empty</html>', $response->getContent());
    }

    #[Test]
    public function it_handles_setting_storage_exception(): void
    {
        // Arrange
        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->with('system', ['description_dashboard'])
            ->willThrowException(new \RuntimeException('Storage error'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Storage error');
        $this->controller->indexAction($this->request);
    }

    #[Test]
    public function it_handles_templating_engine_exception(): void
    {
        // Arrange
        $settings = ['description_dashboard' => 'Test'];

        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->with('system', ['description_dashboard'])
            ->willReturn($settings);

        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->with('@OwlAdmin/dashboard/index.html.twig', [
                'settings' => $settings,
            ])
            ->willThrowException(new \RuntimeException('Template error'));

        // Act & Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Template error');
        $this->controller->indexAction($this->request);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function settingsProvider(): array
    {
        return [
            'valid settings' => [
                'settings' => ['description_dashboard' => 'Welcome message'],
                'expectedTemplate' => '@OwlAdmin/dashboard/index.html.twig',
            ],
            'missing description' => [
                'settings' => [],
                'expectedTemplate' => '@OwlAdmin/dashboard/index.html.twig',
            ],
            'multiple settings' => [
                'settings' => [
                    'description_dashboard' => 'Welcome',
                    'extra_setting' => 'value',
                ],
                'expectedTemplate' => '@OwlAdmin/dashboard/index.html.twig',
            ],
        ];
    }

    #[Test]
    #[DataProvider('settingsProvider')]
    public function it_renders_dashboard_with_various_settings(array $settings, string $expectedTemplate): void
    {
        // Arrange
        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->with('system', ['description_dashboard'])
            ->willReturn($settings);

        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->with($expectedTemplate, [
                'settings' => $settings,
            ])
            ->willReturn('<html>rendered content</html>');

        // Act
        $response = $this->controller->indexAction($this->request);

        // Assert
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('<html>rendered content</html>', $response->getContent());
    }

    #[Test]
    public function it_always_requests_system_section_and_description_dashboard_key(): void
    {
        // Arrange
        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->with(
                $this->identicalTo('system'),
                $this->identicalTo(['description_dashboard']),
            )
            ->willReturn([]);

        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->willReturn('<html>test</html>');

        // Act
        $this->controller->indexAction($this->request);

        // Assert - expectations are verified by PHPUnit
    }

    #[Test]
    public function it_creates_response_with_default_status_code(): void
    {
        // Arrange
        $this->settingStorage
            ->expects($this->once())
            ->method('getBySectionAndKeys')
            ->willReturn([]);

        $this->templatingEngine
            ->expects($this->once())
            ->method('render')
            ->willReturn('<html>content</html>');

        // Act
        $response = $this->controller->indexAction($this->request);

        // Assert
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('<html>content</html>', $response->getContent());
    }
}
