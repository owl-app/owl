<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Grid\Filter;

use Owl\Component\Core\Enum\Grid\Filter\PeriodQuarterEnum;
use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use Owl\Component\Core\Grid\Filter\PeriodFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class PeriodFilterTest extends TestCase
{
    private PeriodFilter $filter;

    private DataSourceInterface&MockObject $dataSource;

    private ExpressionBuilderInterface&MockObject $expressionBuilder;

    protected function setUp(): void
    {
        $this->dataSource = $this->createMock(DataSourceInterface::class);
        $this->expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);

        $this->dataSource->method('getExpressionBuilder')->willReturn($this->expressionBuilder);

        $this->filter = new PeriodFilter();
    }

    public function testApplyWithEmptyData(): void
    {
        $this->dataSource->expects($this->never())->method('restrict');
        $this->filter->apply($this->dataSource, 'createdAt', [], []);
    }

    public function testApplyWithTypeAll(): void
    {
        $this->dataSource->expects($this->never())->method('restrict');
        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['type' => PeriodTypeEnum::TYPE_ALL->value],
            [],
        );
    }

    public function testApplyWithMonth(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('createdAt', '2023-05-01');
        $this->expressionBuilder->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('createdAt', '2023-05-31');
        $this->expressionBuilder->expects($this->once())
            ->method('andX');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['type' => PeriodTypeEnum::TYPE_MONTH->value, 'year' => 2023, 'month' => 5],
            [],
        );
    }

    public function testApplyWithQuarter(): void
    {
        $range = ['start' => '04-01', 'end' => '06-30'];
        $quarter = 2;
        $year = 2023;

        $this->assertEquals($range, PeriodQuarterEnum::getPeriodRange($quarter));

        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('createdAt', "$year-{$range['start']}");
        $this->expressionBuilder->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('createdAt', "$year-{$range['end']}");
        $this->expressionBuilder->expects($this->once())
            ->method('andX');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['type' => PeriodTypeEnum::TYPE_QUARTER->value, 'year' => $year, 'quarter' => $quarter],
            [],
        );
    }

    public function testApplyWithYear(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('createdAt', '2023-01-01');
        $this->expressionBuilder->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('createdAt', '2023-12-31');
        $this->expressionBuilder->expects($this->once())
            ->method('andX');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['type' => PeriodTypeEnum::TYPE_YEAR->value, 'year' => 2023],
            [],
        );
    }

    public function testApplyWithInvalidTypeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['type' => 'invalid', 'year' => 2023],
            [],
        );
    }

    public function testApplyWithCustomFieldOption(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('customField', '2023-01-01');
        $this->expressionBuilder->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('customField', '2023-12-31');
        $this->expressionBuilder->expects($this->once())
            ->method('andX');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['type' => PeriodTypeEnum::TYPE_YEAR->value, 'year' => 2023],
            ['field' => 'customField'],
        );
    }
}
