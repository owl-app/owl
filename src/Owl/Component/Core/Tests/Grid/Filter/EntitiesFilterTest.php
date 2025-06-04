<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Grid\Filter;

use Owl\Component\Core\Grid\Filter\EntitiesFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class EntitiesFilterTest extends TestCase
{
    private EntitiesFilter $filter;

    private DataSourceInterface&MockObject $dataSource;

    private ExpressionBuilderInterface&MockObject $expressionBuilder;

    protected function setUp(): void
    {
        $this->dataSource = $this->createMock(DataSourceInterface::class);
        $this->expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $this->dataSource->method('getExpressionBuilder')->willReturn($this->expressionBuilder);

        $this->filter = new EntitiesFilter();
    }

    public function testApplyWithData(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('equals')
            ->with('fieldName', 42);
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply($this->dataSource, 'name', 42, ['field' => 'fieldName']);
    }

    public function testApplyWithEmptyData(): void
    {
        $this->dataSource->expects($this->never())->method('restrict');
        $this->filter->apply($this->dataSource, 'name', null, ['field' => 'fieldName']);
    }
}
