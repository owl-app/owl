<?php

declare(strict_types=1);

namespace Owl\Component\Core\Enum\Grid\Filter;

use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use UnhandledMatchError;

class PeriodQuarterEnumTest extends TestCase
{
    #[DataProvider('validQuarterProvider')]
    public function testGetPeriodRangeWithStringAndInt($input, array $expected): void
    {
        $this->assertSame($expected, PeriodQuarterEnum::getPeriodRange($input));
    }

    public static function validQuarterProvider(): array
    {
        return [
            ['1', ['start' => '01-01', 'end' => '03-31']],
            [1,   ['start' => '01-01', 'end' => '03-31']],
            ['2', ['start' => '04-01', 'end' => '06-30']],
            [2,   ['start' => '04-01', 'end' => '06-30']],
            ['3', ['start' => '07-01', 'end' => '09-30']],
            [3,   ['start' => '07-01', 'end' => '09-30']],
            ['4', ['start' => '10-01', 'end' => '12-31']],
            [4,   ['start' => '10-01', 'end' => '12-31']],
        ];
    }

    #[DataProvider('invalidQuarterProvider')]
    public function testGetPeriodRangeThrowsOnInvalidInput($input): void
    {
        $this->expectException(UnhandledMatchError::class);
        PeriodQuarterEnum::getPeriodRange($input);
    }

    public static function invalidQuarterProvider(): array
    {
        return [
            [0],
            [5],
            ['0'],
            ['5'],
            ['Q1'],
            [''],
        ];
    }

    #[DataProvider('fromDateProvider')]
    public function testFromDate(string $date, PeriodQuarterEnum $expected): void
    {
        $this->assertSame($expected, PeriodQuarterEnum::fromDate(new DateTime($date)));
    }

    public static function fromDateProvider(): array
    {
        return [
            // Q1
            ['2023-01-01', PeriodQuarterEnum::TYPE_Q1],
            ['2023-02-28', PeriodQuarterEnum::TYPE_Q1],
            ['2023-03-31', PeriodQuarterEnum::TYPE_Q1],
            // Q2
            ['2023-04-01', PeriodQuarterEnum::TYPE_Q2],
            ['2023-05-15', PeriodQuarterEnum::TYPE_Q2],
            ['2023-06-30', PeriodQuarterEnum::TYPE_Q2],
            // Q3
            ['2023-07-01', PeriodQuarterEnum::TYPE_Q3],
            ['2023-08-15', PeriodQuarterEnum::TYPE_Q3],
            ['2023-09-30', PeriodQuarterEnum::TYPE_Q3],
            // Q4
            ['2023-10-01', PeriodQuarterEnum::TYPE_Q4],
            ['2023-11-30', PeriodQuarterEnum::TYPE_Q4],
            ['2023-12-31', PeriodQuarterEnum::TYPE_Q4],
            // Leap year
            ['2024-02-29', PeriodQuarterEnum::TYPE_Q1],
            // Other years
            ['1999-12-31', PeriodQuarterEnum::TYPE_Q4],
            ['2000-01-01', PeriodQuarterEnum::TYPE_Q1],
        ];
    }

    public function testEnumValues(): void
    {
        $this->assertSame('1', PeriodQuarterEnum::TYPE_Q1->value);
        $this->assertSame('2', PeriodQuarterEnum::TYPE_Q2->value);
        $this->assertSame('3', PeriodQuarterEnum::TYPE_Q3->value);
        $this->assertSame('4', PeriodQuarterEnum::TYPE_Q4->value);
    }

    public function testEnumCases(): void
    {
        $cases = PeriodQuarterEnum::cases();
        $this->assertCount(4, $cases);
        $this->assertContains(PeriodQuarterEnum::TYPE_Q1, $cases);
        $this->assertContains(PeriodQuarterEnum::TYPE_Q2, $cases);
        $this->assertContains(PeriodQuarterEnum::TYPE_Q3, $cases);
        $this->assertContains(PeriodQuarterEnum::TYPE_Q4, $cases);
    }
}
