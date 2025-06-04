<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Grid\Filtering;

use Owl\Component\Core\Grid\Filtering\FiltersCriteriaResolver;
use Owl\Component\Core\Manager\UserPreferenceManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Filtering\FiltersCriteriaResolverInterface;
use Sylius\Component\Grid\Parameters;

final class FiltersCriteriaResolverTest extends TestCase
{
    private FiltersCriteriaResolver $resolver;

    private Parameters $parameters;

    private FiltersCriteriaResolverInterface&MockObject $decorated;

    private UserPreferenceManagerInterface&MockObject $userPreferenceManager;

    private Grid&MockObject $grid;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(FiltersCriteriaResolverInterface::class);
        $this->userPreferenceManager = $this->createMock(UserPreferenceManagerInterface::class);
        $this->grid = $this->createMock(Grid::class);
        $this->parameters = new Parameters();

        $this->grid->method('getCode')->willReturn('test_grid');

        $this->resolver = new FiltersCriteriaResolver($this->decorated, $this->userPreferenceManager);
    }

    public function testHasCriteriaReturnsTrueIfDecoratedHasCriteria(): void
    {
        $this->decorated->expects($this->once())
            ->method('hasCriteria')
            ->willReturn(true);
        $this->userPreferenceManager->expects($this->never())->method('has');

        $this->assertTrue($this->resolver->hasCriteria($this->grid, $this->parameters));
    }

    public function testHasCriteriaReturnsTrueIfUserPreferencesExist(): void
    {
        $this->decorated->expects($this->once())
            ->method('hasCriteria')
            ->willReturn(false);
        $this->userPreferenceManager->expects($this->once())
            ->method('has')
            ->with('filters.test_grid')
            ->willReturn(true);

        $this->assertTrue($this->resolver->hasCriteria($this->grid, $this->parameters));
    }

    public function testHasCriteriaReturnsFalseIfNeither(): void
    {
        $this->decorated->expects($this->once())
            ->method('hasCriteria')
            ->willReturn(false);
        $this->userPreferenceManager->expects($this->once())
            ->method('has')
            ->with('filters.test_grid')
            ->willReturn(false);

        $this->assertFalse($this->resolver->hasCriteria($this->grid, $this->parameters));
    }

    public function testGetCriteriaMergesUserPreferencesAndDecorated(): void
    {
        $this->decorated->expects($this->once())
            ->method('getCriteria')
            ->willReturn(['foo' => 'bar']);
        $this->userPreferenceManager->expects($this->once())
            ->method('get')
            ->with('filters.test_grid')
            ->willReturn(['baz' => 'qux']);

        $result = $this->resolver->getCriteria($this->grid, $this->parameters);

        $this->assertEquals(['baz' => 'qux', 'foo' => 'bar'], $result);
    }

    public function testGetCriteriaWithNoUserPreferences(): void
    {
        $this->decorated->expects($this->once())
            ->method('getCriteria')
            ->willReturn(['foo' => 'bar']);
        $this->userPreferenceManager->expects($this->once())
            ->method('get')
            ->with('filters.test_grid')
            ->willReturn(null);

        $result = $this->resolver->getCriteria($this->grid, $this->parameters);

        $this->assertEquals(['foo' => 'bar'], $result);
    }
}
