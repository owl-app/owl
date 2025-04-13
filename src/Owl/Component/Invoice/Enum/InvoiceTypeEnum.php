<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Enum;

enum InvoiceTypeEnum: string
{
    case sales = 'sales';

    case proforma = 'proforma';

    case correction = 'correction';

    public const SALES = self::sales->value;

    public const PROFORMA = self::proforma->value;

    public const CORRECTION = self::correction->value;
}
