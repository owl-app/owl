<?php

declare(strict_types=1);

namespace Owl\Component\Invoice\Enum;

enum InvoicePaymentMethodEnum: string
{
    case WIRE_TRANSFER = 'wire_transfer';

    case CASH = 'cash';

    case CARD = 'card';

    case CREDIT = 'credit';

    case CHECK = 'check';

    case OTHER = 'other';
}
