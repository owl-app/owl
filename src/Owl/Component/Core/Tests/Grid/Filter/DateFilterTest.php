<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Grid\Filter;

use Owl\Component\Core\Grid\Filter\DateFilter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Data\ExpressionBuilderInterface;

final class DateFilterTest extends TestCase
{
    private DateFilter $filter;

    private DataSourceInterface&MockObject $dataSource;

    private ExpressionBuilderInterface&MockObject $expressionBuilder;

    protected function setUp(): void
    {
        $this->dataSource = $this->createMock(DataSourceInterface::class);
        $this->expressionBuilder = $this->createMock(ExpressionBuilderInterface::class);
        $this->dataSource->method('getExpressionBuilder')->willReturn($this->expressionBuilder);
        $this->filter = new DateFilter();
    }

    public function testApplyWithFromInclusive(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('createdAt', '2023-01-01');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => '2023-01-01', 'to' => null],
            ['inclusive_from' => true],
        );
    }

    public function testApplyWithFromExclusive(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThan')
            ->with('createdAt', '2023-01-01');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => '2023-01-01', 'to' => null],
            ['inclusive_from' => false],
        );
    }

    public function testApplyWithToInclusive(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('createdAt', '2023-12-31');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => null, 'to' => '2023-12-31'],
            ['inclusive_to' => true],
        );
    }

    public function testApplyWithToExclusive(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('lessThan')
            ->with('createdAt', '2023-12-31');
        $this->dataSource->expects($this->once())
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => null, 'to' => '2023-12-31'],
            ['inclusive_to' => false],
        );
    }

    public function testApplyWithBothFromAndToDefaultInclusive(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('createdAt', '2023-01-01');
        $this->expressionBuilder->expects($this->once())
            ->method('lessThan')
            ->with('createdAt', '2023-12-31');
        $this->dataSource->expects($this->exactly(2))
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => '2023-01-01', 'to' => '2023-12-31'],
            [],
        );
    }

    public function testApplyWithBothFromAndToInclusive(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('createdAt', '2023-01-01');
        $this->expressionBuilder->expects($this->once())
            ->method('lessThanOrEqual')
            ->with('createdAt', '2023-12-31');
        $this->dataSource->expects($this->exactly(2))
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => '2023-01-01', 'to' => '2023-12-31'],
            ['inclusive_to' => true, 'inclusive_from' => true],
        );
    }

    public function testApplyWithBothFromAndToExclusive(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThan')
            ->with('createdAt', '2023-01-01');
        $this->expressionBuilder->expects($this->once())
            ->method('lessThan')
            ->with('createdAt', '2023-12-31');
        $this->dataSource->expects($this->exactly(2))
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => '2023-01-01', 'to' => '2023-12-31'],
            ['inclusive_to' => false, 'inclusive_from' => false],
        );
    }

    public function testApplyWithCustomFieldOption(): void
    {
        $this->expressionBuilder->expects($this->once())
            ->method('greaterThanOrEqual')
            ->with('customField', '2023-01-01');
        $this->expressionBuilder->expects($this->once())
            ->method('lessThan')
            ->with('customField', '2023-01-05');
        $this->dataSource->expects($this->exactly(2))
            ->method('restrict');

        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => '2023-01-01', 'to' => '2023-01-05'],
            ['field' => 'customField'],
        );
    }

    public function testApplyWithEmptyData(): void
    {
        $this->dataSource->expects($this->never())->method('restrict');
        $this->filter->apply($this->dataSource, 'createdAt', [], []);
    }

    public function testApplyWithNoFromAndNoTo(): void
    {
        $this->dataSource->expects($this->never())->method('restrict');
        $this->filter->apply(
            $this->dataSource,
            'createdAt',
            ['from' => null, 'to' => null],
            [],
        );
    }
}
