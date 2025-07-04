<?php

declare(strict_types=1);

namespace Owl\Component\Core\Enum\Grid\Filter;

use DateTime;
use UnhandledMatchError;
use LogicException;

enum PeriodQuarterEnum: string
{
    case TYPE_Q1 = '1';
    case TYPE_Q2 = '2';
    case TYPE_Q3 = '3';
    case TYPE_Q4 = '4';

    /**
     * @param int|string $quarter
     * @return array<string, string>
     */
    public static function getPeriodRange(int|string $quarter): array
    {
        if (is_int($quarter)) {
            $quarter = (string) $quarter;
        }

        return match ($quarter) {
            self::TYPE_Q1->value => ['start' => '01-01', 'end' => '03-31'],
            self::TYPE_Q2->value => ['start' => '04-01', 'end' => '06-30'],
            self::TYPE_Q3->value => ['start' => '07-01', 'end' => '09-30'],
            self::TYPE_Q4->value => ['start' => '10-01', 'end' => '12-31'],
            default => throw new UnhandledMatchError("Invalid quarter: $quarter"),
        };
    }

    public static function fromDate(DateTime $date): self
    {
        $month = (int) $date->format('n');

        if ($month <= 3) {
            return self::TYPE_Q1;
        }
        if ($month <= 6) {
            return self::TYPE_Q2;
        }
        if ($month <= 9) {
            return self::TYPE_Q3;
        }

        return self::TYPE_Q4;
    }
}