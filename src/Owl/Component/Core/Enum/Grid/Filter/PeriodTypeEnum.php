<?php

declare(strict_types=1);

namespace Owl\Component\Core\Enum\Grid\Filter;

enum PeriodTypeEnum: string
{
    case TYPE_ALL = 'all';

    case TYPE_MONTH = 'month';

    case TYPE_QUARTER = 'quarter';

    case TYPE_YEAR = 'year';
}
