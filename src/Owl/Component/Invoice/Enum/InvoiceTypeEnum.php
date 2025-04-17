<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Enum;

enum InvoiceTypeEnum: string
{
    case SALES = 'sales';

    case PROFORMA = 'proforma';

    case CORRECTION = 'correction';

    public const TYPE_SALES = self::SALES->value;

    public const TYPE_PROFORMA = self::PROFORMA->value;

    public const TYPE_CORRECTION = self::CORRECTION->value;
}
