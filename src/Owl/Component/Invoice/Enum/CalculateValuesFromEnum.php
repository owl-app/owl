<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Enum;

enum CalculateValuesFromEnum: string
{
    case FROM_NET = 'net';

    case FROM_GROSS = 'gross';
}
