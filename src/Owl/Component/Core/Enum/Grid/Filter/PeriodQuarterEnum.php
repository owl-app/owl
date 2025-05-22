<?php

declare(strict_types=1);

namespace Owl\Component\Core\Enum\Grid\Filter;

use DateTime;

enum PeriodQuarterEnum: string
{
    case TYPE_Q1 = '1';

    case TYPE_Q2 = '2';

    case TYPE_Q3 = '3';

    case TYPE_Q4 = '4';

    public function getPeriodRange(): array
    {
        return match ($this) {
            self::TYPE_Q1 => ['start' => '01-01', 'end' => '03-31'],
            self::TYPE_Q2 => ['start' => '04-01', 'end' => '06-30'],
            self::TYPE_Q3 => ['start' => '07-01', 'end' => '09-30'],
            self::TYPE_Q4 => ['start' => '10-01', 'end' => '12-31'],
        };
    }

    public static function fromDate(DateTime $date): self
    {
        $month = (int) $date->format('n');

        return match (true) {
            $month >= 1 && $month <= 3 => self::TYPE_Q1,
            $month >= 4 && $month <= 6 => self::TYPE_Q2,
            $month >= 7 && $month <= 9 => self::TYPE_Q3,
            $month >= 10 && $month <= 12 => self::TYPE_Q4,
        };
    }
}
