<?php

declare(strict_types=1);

namespace Tests\Owl\Bundle\CoreBundle\Grid;

use Owl\Bundle\CoreBundle\Grid\SuffixableGridProvider;
use Owl\Component\Core\Provider\SuffixGridProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Provider\GridProviderInterface;

final class SuffixableGridProviderTest extends TestCase
{
    private SuffixableGridProvider $provider;

    private array $gridConfigurations;

    private GridProviderInterface&MockObject $decoratedGridProvider;

    private SuffixGridProviderInterface&MockObject $suffixGridProvider;

    protected function setUp(): void
    {
        $this->decoratedGridProvider = $this->createMock(GridProviderInterface::class);
        $this->suffixGridProvider = $this->createMock(SuffixGridProviderInterface::class);
        $this->gridConfigurations = [
            'product_grid' => [],
            'product_grid_suffix' => [],
            'user_grid' => [],
        ];

        $this->provider = new SuffixableGridProvider(
            $this->decoratedGridProvider,
            $this->suffixGridProvider,
            $this->gridConfigurations,
        );
    }

    public function testGetWithAvailableSuffix(): void
    {
        $grid = $this->createMock(Grid::class);
        $this->suffixGridProvider->method('getSuffix')->willReturn('_suffix');
        $this->decoratedGridProvider
            ->expects($this->once())
            ->method('get')
            ->with('product_grid_suffix')
            ->willReturn($grid);

        $result = $this->provider->get('product_grid');
        $this->assertSame($grid, $result);
    }

    public function testGetWithUnavailableSuffix(): void
    {
        $grid = $this->createMock(Grid::class);
        $this->suffixGridProvider->method('getSuffix')->willReturn('_unavailable');
        $this->decoratedGridProvider
            ->expects($this->once())
            ->method('get')
            ->with('product_grid')
            ->willReturn($grid);

        $result = $this->provider->get('product_grid');
        $this->assertSame($grid, $result);
    }

    public function testGetWithEmptySuffix(): void
    {
        $grid = $this->createMock(Grid::class);
        $this->suffixGridProvider->method('getSuffix')->willReturn('');
        $this->decoratedGridProvider
            ->expects($this->once())
            ->method('get')
            ->with('product_grid')
            ->willReturn($grid);

        $result = $this->provider->get('product_grid');
        $this->assertSame($grid, $result);
    }

    public function testGetWithNonExistingGrid(): void
    {
        $grid = $this->createMock(Grid::class);
        $this->suffixGridProvider->method('getSuffix')->willReturn('_suffix');
        $this->decoratedGridProvider
            ->expects($this->once())
            ->method('get')
            ->with('non_existing_grid')
            ->willReturn($grid);

        $result = $this->provider->get('non_existing_grid');
        $this->assertSame($grid, $result);
    }
}
