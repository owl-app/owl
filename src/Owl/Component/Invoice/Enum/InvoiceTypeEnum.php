<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Enum;

enum InvoiceTypeEnum: string
{
    case sales = 'sales';

    case proforma = 'proforma';

    case correction = 'correction';
}
