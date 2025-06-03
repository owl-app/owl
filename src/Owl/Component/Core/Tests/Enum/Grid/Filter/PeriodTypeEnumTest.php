<?php

declare(strict_types=1);

namespace Tests\Owl\Component\Core\Enum\Grid\Filter;

use Owl\Component\Core\Enum\Grid\Filter\PeriodTypeEnum;
use PHPUnit\Framework\TestCase;

final class PeriodTypeEnumTest extends TestCase
{
    public function testEnumCases(): void
    {
        self::assertSame('all', PeriodTypeEnum::TYPE_ALL->value);
        self::assertSame('month', PeriodTypeEnum::TYPE_MONTH->value);
        self::assertSame('quarter', PeriodTypeEnum::TYPE_QUARTER->value);
        self::assertSame('year', PeriodTypeEnum::TYPE_YEAR->value);
    }
} 